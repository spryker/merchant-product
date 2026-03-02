<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Event;

use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Spryker\Shared\MerchantProduct\MerchantProductConfig;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventFacadeInterface;
use Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface;

class ProductEventTrigger implements ProductEventTriggerInterface
{
    /**
     * @var int
     *
     * @phpstan-var positive-int
     */
    protected const CHUNK_SIZE = 1000;

    public function __construct(
        protected MerchantProductRepositoryInterface $repository,
        protected MerchantProductToEventFacadeInterface $eventFacade
    ) {
    }

    public function trigger(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): void
    {
        if (!$merchantProductCriteriaTransfer->getMerchantIds()) {
            return;
        }

        $productAbstractIds = $this->repository->getProductAbstractIdsByMerchantIds($merchantProductCriteriaTransfer->getMerchantIds());
        $this->triggerProductAbstractPublishEvent($productAbstractIds);
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    protected function triggerProductAbstractPublishEvent(array $productAbstractIds): void
    {
        foreach (array_chunk($productAbstractIds, static::CHUNK_SIZE) as $productAbstractIdsChunk) {
            $eventEntityTransfers = [];

            foreach ($productAbstractIdsChunk as $idProductAbstract) {
                $eventEntityTransfers[] = (new EventEntityTransfer())
                    ->setId($idProductAbstract);
            }

            $this->eventFacade->triggerBulk(
                MerchantProductConfig::PRODUCT_ABSTRACT_PUBLISH,
                $eventEntityTransfers,
            );
        }
    }
}
