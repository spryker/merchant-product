<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Updater;

use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Spryker\Zed\MerchantProduct\Persistence\MerchantProductEntityManagerInterface;
use Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface;

class MerchantProductUpdater implements MerchantProductUpdaterInterface
{
    public function __construct(
        protected MerchantProductEntityManagerInterface $entityManager,
        protected MerchantProductRepositoryInterface $repository,
    ) {
    }

    public function updateMerchantProduct(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer
    {
        $existingMerchantProduct = $this->repository->findMerchantProduct(
            (new MerchantProductCriteriaTransfer())->setIdProductAbstract($merchantProductTransfer->getIdProductAbstract()),
        );

        $newIdMerchant = $merchantProductTransfer->getIdMerchant();

        if ($existingMerchantProduct !== null && !$newIdMerchant) {
            $this->entityManager->delete($existingMerchantProduct);

            return $merchantProductTransfer;
        }

        if ($existingMerchantProduct === null && $newIdMerchant) {
            $this->entityManager->create(
                (new MerchantProductTransfer())
                    ->setIdMerchant($newIdMerchant)
                    ->setIdProductAbstract($merchantProductTransfer->getIdProductAbstract()),
            );

            return $merchantProductTransfer;
        }

        if ($existingMerchantProduct !== null && $newIdMerchant) {
            $this->entityManager->update(
                $existingMerchantProduct->setIdMerchant($newIdMerchant),
            );
        }

        return $merchantProductTransfer;
    }
}
