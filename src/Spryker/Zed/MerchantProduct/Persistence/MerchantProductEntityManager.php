<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Generated\Shared\Transfer\MerchantProductTransfer;
use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstract;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductPersistenceFactory getFactory()
 */
class MerchantProductEntityManager extends AbstractEntityManager implements MerchantProductEntityManagerInterface
{
    public function create(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer
    {
        $merchantProductMapper = $this->getFactory()->createMerchantProductMapper();

        $merchantProductEntity = $merchantProductMapper->mapMerchantProductTransferToMerchantProductAbstractEntity(
            $merchantProductTransfer,
            new SpyMerchantProductAbstract(),
        );

        $merchantProductEntity->save();

        return $merchantProductMapper->mapMerchantProductAbstractEntityToMerchantProductTransfer(
            $merchantProductEntity,
            $merchantProductTransfer,
        );
    }

    public function update(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer
    {
        $merchantProductEntity = $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->filterByIdMerchantProductAbstract($merchantProductTransfer->getIdMerchantProductAbstractOrFail())
            ->findOne();

        if ($merchantProductEntity === null) {
            throw new PropelException(
                sprintf(
                    'Merchant product entity could not be found by given id %s',
                    $merchantProductTransfer->getIdMerchantProductAbstract(),
                ),
            );
        }

        $merchantProductMapper = $this->getFactory()->createMerchantProductMapper();

        $merchantProductEntity = $merchantProductMapper->mapMerchantProductTransferToMerchantProductAbstractEntity(
            $merchantProductTransfer,
            $merchantProductEntity,
        );

        $merchantProductEntity->save();

        return $merchantProductMapper->mapMerchantProductAbstractEntityToMerchantProductTransfer(
            $merchantProductEntity,
            $merchantProductTransfer,
        );
    }

    public function delete(MerchantProductTransfer $merchantProductTransfer): void
    {
        $this->getFactory()
            ->getMerchantProductAbstractPropelQuery()
            ->filterByIdMerchantProductAbstract($merchantProductTransfer->getIdMerchantProductAbstractOrFail())
            ->delete();
    }
}
