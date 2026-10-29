<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Generated\Shared\Transfer\MerchantProductAbstractCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductAbstractCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Orm\Zed\MerchantProduct\Persistence\Map\SpyMerchantProductAbstractTableMap;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Model\Formatter\OptimizedSimpleArrayFormatter;

/**
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductPersistenceFactory getFactory()
 */
class MerchantProductRepository extends AbstractRepository implements MerchantProductRepositoryInterface
{
    public function findMerchant(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): ?MerchantTransfer
    {
        $merchantProductAbstractQuery = $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->joinWithMerchant();

        $merchantProductAbstractEntity = $this->applyFilters($merchantProductAbstractQuery, $merchantProductCriteriaTransfer)
            ->findOne();

        if (!$merchantProductAbstractEntity) {
            return null;
        }

        return $this->getFactory()
            ->createMerchantMapper()
            ->mapMerchantEntityToMerchantTransfer($merchantProductAbstractEntity->getMerchant(), new MerchantTransfer());
    }

    public function get(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): MerchantProductCollectionTransfer
    {
        /** @var \Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery $merchantProductAbstractQuery */
        $merchantProductAbstractQuery = $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->leftJoinWithMerchant()
            ->leftJoinWithProductAbstract()
            ->useProductAbstractQuery()
                ->leftJoinWithSpyProduct()
                ->useSpyProductQuery()
                    ->leftJoinWithSpyProductLocalizedAttributes()
                ->endUse()
            ->endUse();

        $merchantProductAbstractQuery = $this->applyFilters($merchantProductAbstractQuery, $merchantProductCriteriaTransfer);

        $merchantProductAbstractEntities = $merchantProductAbstractQuery->find();

        $merchantProductCollectionTransfer = new MerchantProductCollectionTransfer();
        $merchantProductMapper = $this->getFactory()->createMerchantProductMapper();

        foreach ($merchantProductAbstractEntities as $merchantProductAbstractEntity) {
            $merchantProductCollectionTransfer->addMerchantProduct(
                $merchantProductMapper->mapMerchantProductAbstractEntityToMerchantProductTransfer(
                    $merchantProductAbstractEntity,
                    new MerchantProductTransfer(),
                ),
            );
        }

        return $merchantProductCollectionTransfer;
    }

    /**
     * @param array<string> $concreteSku
     *
     * @return array<string, string>
     */
    public function getConcreteProductSkuMerchantReferenceMap(array $concreteSku): array
    {
        /** @var \Propel\Runtime\Collection\ArrayCollection $concreteProductSkuMerchantReferenceMap */
        $concreteProductSkuMerchantReferenceMap = $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->select([SpyMerchantTableMap::COL_MERCHANT_REFERENCE, SpyProductTableMap::COL_SKU])
            ->joinMerchant()
            ->useProductAbstractQuery()
                ->useSpyProductQuery()
                    ->filterBySku_In($concreteSku)
                ->endUse()
            ->endUse()
            ->find();

        return $concreteProductSkuMerchantReferenceMap->toKeyValue(SpyProductTableMap::COL_SKU, SpyMerchantTableMap::COL_MERCHANT_REFERENCE);
    }

    public function findMerchantProduct(
        MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
    ): ?MerchantProductTransfer {
        $merchantProductAbstractQuery = $this->getFactory()->getMerchantProductAbstractPropelQuery();
        $merchantProductAbstractQuery = $this->applyFilters($merchantProductAbstractQuery, $merchantProductCriteriaTransfer);

        $merchantProductAbstractEntity = $merchantProductAbstractQuery->findOne();

        if (!$merchantProductAbstractEntity) {
            return null;
        }

        return $this->getFactory()->createMerchantProductMapper()->mapMerchantProductAbstractEntityToMerchantProductTransfer(
            $merchantProductAbstractEntity,
            new MerchantProductTransfer(),
        );
    }

    public function getMerchantProductAbstractCollection(
        MerchantProductAbstractCriteriaTransfer $merchantProductAbstractCriteriaTransfer
    ): MerchantProductAbstractCollectionTransfer {
        $merchantProductAbstractCollectionTransfer = new MerchantProductAbstractCollectionTransfer();
        $merchantProductAbstractQuery = $this->getFactory()->getMerchantProductAbstractPropelQuery();

        $paginationTransfer = $merchantProductAbstractCriteriaTransfer->getPagination();
        if ($paginationTransfer !== null) {
            $merchantProductAbstractQuery = $this
                ->applyMerchantProductAbstractPagination($merchantProductAbstractQuery, $paginationTransfer);
            $merchantProductAbstractCollectionTransfer->setPagination($paginationTransfer);
        }

        return $this->getFactory()
            ->createMerchantMapper()
            ->mapMerchantProductAbstractEntitiesToMerchantProductAbstractCollectionTransfer(
                $merchantProductAbstractQuery->find(),
                $merchantProductAbstractCollectionTransfer,
            );
    }

    /**
     * @param array<int> $merchantIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByMerchantIds(array $merchantIds): array
    {
        $merchantProductAbstractPropelQuery = $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->filterByFkMerchant_In($merchantIds);

        return $merchantProductAbstractPropelQuery
            ->select([SpyMerchantProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT])
            ->find()
            ->getData();
    }

    /**
     * @param array<int> $merchantIds
     * @param int $batchSize
     *
     * @return iterable<array<int>>
     */
    public function iterateProductAbstractIdsByMerchantIds(array $merchantIds, int $batchSize): iterable
    {
        foreach ($merchantIds as $idMerchant) {
            $lastIdMerchantProductAbstract = 0;

            do {
                /** @var array<array<string, mixed>> $merchantProductAbstractRows */
                $merchantProductAbstractRows = $this->getFactory()
                    ->getMerchantProductAbstractPropelQuery()
                    ->filterByFkMerchant($idMerchant)
                    ->filterByIdMerchantProductAbstract($lastIdMerchantProductAbstract, Criteria::GREATER_THAN)
                    ->select([SpyMerchantProductAbstractTableMap::COL_ID_MERCHANT_PRODUCT_ABSTRACT, SpyMerchantProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT])
                    ->orderByIdMerchantProductAbstract()
                    ->setLimit($batchSize)
                    ->setFormatter(new OptimizedSimpleArrayFormatter())
                    ->find()
                    ->getData();

                if (!$merchantProductAbstractRows) {
                    break;
                }

                $lastIdMerchantProductAbstract = (int)end($merchantProductAbstractRows)[SpyMerchantProductAbstractTableMap::COL_ID_MERCHANT_PRODUCT_ABSTRACT];

                yield array_map('intval', array_column($merchantProductAbstractRows, SpyMerchantProductAbstractTableMap::COL_FK_PRODUCT_ABSTRACT));
            } while (count($merchantProductAbstractRows) === $batchSize);
        }
    }

    protected function applyFilters(
        SpyMerchantProductAbstractQuery $merchantProductAbstractQuery,
        MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
    ): SpyMerchantProductAbstractQuery {
        if ($merchantProductCriteriaTransfer->getIdProductAbstract()) {
            $merchantProductAbstractQuery->filterByFkProductAbstract($merchantProductCriteriaTransfer->getIdProductAbstract());
        }

        if ($merchantProductCriteriaTransfer->getProductAbstractIds()) {
            $merchantProductAbstractQuery->filterByFkProductAbstract_In($merchantProductCriteriaTransfer->getProductAbstractIds());
        }

        if ($merchantProductCriteriaTransfer->getMerchantProductAbstractIds()) {
            $merchantProductAbstractQuery->filterByIdMerchantProductAbstract_In($merchantProductCriteriaTransfer->getMerchantProductAbstractIds());
        }

        if ($merchantProductCriteriaTransfer->getMerchantIds()) {
            $merchantProductAbstractQuery->filterByFkMerchant_In($merchantProductCriteriaTransfer->getMerchantIds());
        }

        if ($merchantProductCriteriaTransfer->getProductConcreteIds()) {
            $merchantProductAbstractQuery
                ->useProductAbstractQuery()
                    ->useSpyProductQuery()
                        ->filterByIdProduct_In($merchantProductCriteriaTransfer->getProductConcreteIds())
                    ->endUse()
                ->endUse();
        }

        if ($merchantProductCriteriaTransfer->getProductConcreteSkus()) {
            $merchantProductAbstractQuery
                ->useProductAbstractQuery()
                    ->useSpyProductQuery()
                        ->filterBySku_In($merchantProductCriteriaTransfer->getProductConcreteSkus())
                    ->endUse()
                ->endUse();
        }

        return $merchantProductAbstractQuery;
    }

    protected function applyMerchantProductAbstractPagination(
        SpyMerchantProductAbstractQuery $merchantProductAbstractQuery,
        PaginationTransfer $paginationTransfer
    ): SpyMerchantProductAbstractQuery {
        $paginationTransfer->setNbResults($merchantProductAbstractQuery->count());
        if ($paginationTransfer->getLimit() !== null && $paginationTransfer->getOffset() !== null) {
            return $merchantProductAbstractQuery
                ->limit($paginationTransfer->getLimit())
                ->offset($paginationTransfer->getOffset());
        }

        return $merchantProductAbstractQuery;
    }
}
