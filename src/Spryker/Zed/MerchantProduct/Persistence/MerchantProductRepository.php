<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductPersistenceFactory getFactory()
 */
class MerchantProductRepository extends AbstractRepository implements MerchantProductRepositoryInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
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

    /**
     * @param \Generated\Shared\Transfer\MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantProductTransfer[]
     */
    public function get(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): array
    {
        $merchantProductAbstractQuery = $this->getFactory()->getMerchantProductAbstractPropelQuery();

        $this->applyFilters($merchantProductAbstractQuery, $merchantProductCriteriaTransfer);

        $merchantProductAbstractEntities = $merchantProductAbstractQuery->find();

        $merchantProductTransfers = [];
        $merchantProductMapper = $this->getFactory()->createMerchantProductMapper();

        foreach ($merchantProductAbstractEntities as $merchantProductAbstractEntity) {
            $merchantProductTransfers[] = $merchantProductMapper
                ->mapMerchantProductEntityToMerchantProductTransfer($merchantProductAbstractEntity, new MerchantProductTransfer());
        }

        return $merchantProductTransfers;
    }

    /**
     * @param \Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery $merchantProductAbstractQuery
     * @param \Generated\Shared\Transfer\MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
     *
     * @return \Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery
     */
    protected function applyFilters(
        SpyMerchantProductAbstractQuery $merchantProductAbstractQuery,
        MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
    ): SpyMerchantProductAbstractQuery {
        if ($merchantProductCriteriaTransfer->getIdProductAbstract()) {
            $merchantProductAbstractQuery->filterByFkProductAbstract($merchantProductCriteriaTransfer->getIdProductAbstract());
        }

        if ($merchantProductCriteriaTransfer->getMerchantProductAbstractIds()) {
            $merchantProductAbstractQuery->filterByIdMerchantProductAbstract_In($merchantProductCriteriaTransfer->getMerchantProductAbstractIds());
        }

        if ($merchantProductCriteriaTransfer->getMerchantIds()) {
            $merchantProductAbstractQuery->filterByFkMerchant_In($merchantProductCriteriaTransfer->getMerchantIds());
        }

        return $merchantProductAbstractQuery;
    }
}
