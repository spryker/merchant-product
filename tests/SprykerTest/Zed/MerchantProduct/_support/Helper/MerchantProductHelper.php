<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProduct\Helper;

use Codeception\Module;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstract;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;

class MerchantProductHelper extends Module
{
    use DataCleanupHelperTrait;

    public function haveMerchantProduct(array $seedData = []): MerchantProductTransfer
    {
        $merchantProductAbstractEntity = new SpyMerchantProductAbstract();
        $merchantProductAbstractEntity->setFkMerchant($seedData[MerchantProductTransfer::ID_MERCHANT]);
        $merchantProductAbstractEntity->setFkProductAbstract($seedData[MerchantProductTransfer::ID_PRODUCT_ABSTRACT]);
        $merchantProductAbstractEntity->save();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($merchantProductAbstractEntity) {
            $merchantProductAbstractEntity->delete();
        });

        return (new MerchantProductTransfer())
            ->fromArray($merchantProductAbstractEntity->toArray(), true)
            ->setIdMerchant($merchantProductAbstractEntity->getFkMerchant())
            ->setIdProductAbstract($merchantProductAbstractEntity->getFkProductAbstract());
    }

    public function addMerchantProductRelation(int $idMerchant, int $idProductAbstract): int
    {
        $merchantProductAbstract = $this->getMerchantProductAbstractPropelQuery()
            ->filterByFkMerchant($idMerchant)
            ->filterByFkProductAbstract($idProductAbstract)
            ->findOneOrCreate();

        $merchantProductAbstract->save();

        $this->getDataCleanupHelper()->_addCleanup(function () use ($merchantProductAbstract) {
            $merchantProductAbstract->delete();
        });

        return $merchantProductAbstract->getIdMerchantProductAbstract();
    }

    protected function getMerchantProductAbstractPropelQuery(): SpyMerchantProductAbstractQuery
    {
        return SpyMerchantProductAbstractQuery::create();
    }
}
