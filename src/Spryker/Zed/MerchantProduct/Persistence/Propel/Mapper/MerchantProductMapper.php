<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\LocalizedAttributesTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Orm\Zed\Locale\Persistence\SpyLocale;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductLocalizedAttributes;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\MerchantProduct\Dependency\Service\MerchantProductToUtilEncodingServiceInterface;

class MerchantProductMapper
{
    /**
     * @var \Spryker\Zed\MerchantProduct\Dependency\Service\MerchantProductToUtilEncodingServiceInterface
     */
    protected $utilEncodingService;

    public function __construct(MerchantProductToUtilEncodingServiceInterface $utilEncodingService)
    {
        $this->utilEncodingService = $utilEncodingService;
    }

    public function mapMerchantProductAbstractEntityToMerchantProductTransfer(
        SpyMerchantProductAbstract $merchantProductAbstractEntity,
        MerchantProductTransfer $merchantProductTransfer
    ): MerchantProductTransfer {
        $merchantProductTransfer->fromArray($merchantProductAbstractEntity->toArray(), true)
            ->setIdProductAbstract($merchantProductAbstractEntity->getFkProductAbstract())
            ->setIdMerchant($merchantProductAbstractEntity->getFkMerchant());

        $merchantProductTransfer->setMerchantReference($merchantProductAbstractEntity->getMerchant()->getMerchantReference());

        $this->mapConcreteProductsToMerchantProductTransfer(
            $merchantProductTransfer,
            $merchantProductAbstractEntity->getProductAbstract()->getSpyProducts(),
        );

        return $merchantProductTransfer;
    }

    public function mapMerchantProductTransferToMerchantProductAbstractEntity(
        MerchantProductTransfer $merchantProductTransfer,
        SpyMerchantProductAbstract $merchantProductAbstractEntity
    ): SpyMerchantProductAbstract {
        $merchantProductAbstractEntity->fromArray($merchantProductTransfer->toArray());
        $merchantProductAbstractEntity->setFkMerchant($merchantProductTransfer->getIdMerchantOrFail());
        $merchantProductAbstractEntity->setFkProductAbstract($merchantProductTransfer->getIdProductAbstractOrFail());

        return $merchantProductAbstractEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantProductTransfer $merchantProductTransfer
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Product\Persistence\SpyProduct> $productEntities
     *
     * @return \Generated\Shared\Transfer\MerchantProductTransfer
     */
    protected function mapConcreteProductsToMerchantProductTransfer(
        MerchantProductTransfer $merchantProductTransfer,
        ObjectCollection $productEntities
    ): MerchantProductTransfer {
        foreach ($productEntities as $productEntity) {
            $productConcreteTransfer = (new ProductConcreteTransfer())->fromArray($productEntity->toArray(), true);
            $productConcreteTransfer->setIdProductConcrete($productEntity->getIdProduct());
            $productConcreteTransfer->setAttributes($this->utilEncodingService->decodeJson($productEntity->getAttributes(), true) ?? []);

            foreach ($productEntity->getSpyProductLocalizedAttributess() as $productLocalizedAttributesEntity) {
                $productConcreteTransfer->addLocalizedAttributes(
                    $this->mapProductLocalizedAttributesEntityToTransfer($productLocalizedAttributesEntity, new LocalizedAttributesTransfer()),
                );
            }

            $merchantProductTransfer->addProduct($productConcreteTransfer);
        }

        return $merchantProductTransfer;
    }

    protected function mapProductLocalizedAttributesEntityToTransfer(
        SpyProductLocalizedAttributes $productLocalizedAttributesEntity,
        LocalizedAttributesTransfer $localizedAttributesTransfer
    ): LocalizedAttributesTransfer {
        $localizedAttributesTransfer->fromArray(
            $productLocalizedAttributesEntity->toArray(),
            true,
        );

        $localizedAttributesTransfer->setLocale(
            $this->mapLocaleEntityToTransfer($productLocalizedAttributesEntity->getLocale(), new LocaleTransfer()),
        );

        return $localizedAttributesTransfer;
    }

    protected function mapLocaleEntityToTransfer(SpyLocale $localeEntity, LocaleTransfer $localeTransfer): LocaleTransfer
    {
        return $localeTransfer->fromArray(
            $localeEntity->toArray(),
            true,
        );
    }
}
