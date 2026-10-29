<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Event;

use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Spryker\Shared\MerchantProduct\MerchantProductConfig as SharedMerchantProductConfig;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventBehaviorFacadeInterface;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventFacadeInterface;
use Spryker\Zed\MerchantProduct\MerchantProductConfig;
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
        protected MerchantProductToEventFacadeInterface $eventFacade,
        protected MerchantProductToEventBehaviorFacadeInterface $eventBehaviorFacade,
        protected MerchantProductConfig $merchantProductConfig
    ) {
    }

    /**
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function triggerByMerchantEvents(array $eventEntityTransfers): void
    {
        $this->trigger(
            (new MerchantProductCriteriaTransfer())->setMerchantIds(
                $this->eventBehaviorFacade->getEventTransferIds(
                    $this->filterProductAbstractRelevantMerchantEvents($eventEntityTransfers),
                ),
            ),
        );
    }

    public function trigger(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): void
    {
        if (!$merchantProductCriteriaTransfer->getMerchantIds()) {
            return;
        }

        $productAbstractIdIterator = $this->repository->iterateProductAbstractIdsByMerchantIds(
            $merchantProductCriteriaTransfer->getMerchantIds(),
            static::CHUNK_SIZE,
        );

        foreach ($productAbstractIdIterator as $productAbstractIds) {
            $this->triggerProductAbstractPublishEvent($productAbstractIds);
        }
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
                SharedMerchantProductConfig::PRODUCT_ABSTRACT_PUBLISH,
                $eventEntityTransfers,
            );
        }
    }

    /**
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return list<\Generated\Shared\Transfer\EventEntityTransfer>
     */
    protected function filterProductAbstractRelevantMerchantEvents(array $eventEntityTransfers): array
    {
        $relevantMerchantColumns = $this->merchantProductConfig->getProductAbstractRelevantMerchantColumns();

        // An event without modified columns stands for a change of the whole merchant, for example a republish.
        return array_values(array_filter(
            $eventEntityTransfers,
            fn (EventEntityTransfer $eventEntityTransfer): bool => !$eventEntityTransfer->getModifiedColumns()
                || array_intersect($eventEntityTransfer->getModifiedColumns(), $relevantMerchantColumns) !== [],
        ));
    }
}
