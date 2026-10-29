<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Communication\Plugin\Publisher;

use Spryker\Shared\MerchantProduct\MerchantProductConfig;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PublisherExtension\Dependency\Plugin\PublisherPluginInterface;

/**
 * @method \Spryker\Zed\MerchantProduct\Business\MerchantProductFacadeInterface getFacade()
 * @method \Spryker\Zed\MerchantProduct\Communication\MerchantProductCommunicationFactory getFactory()
 * @method \Spryker\Zed\MerchantProduct\Business\MerchantProductBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 */
class MerchantProductPublisherPlugin extends AbstractPlugin implements PublisherPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * - Skips merchant events whose modified columns contain none of {@link \Spryker\Zed\MerchantProduct\MerchantProductConfig::getProductAbstractRelevantMerchantColumns()}.
     * - Does not skip merchant events without modified columns.
     * - Extracts Merchant IDs from event entity transfers.
     * - Retrieves product abstract IDs related to these merchants.
     * - Triggers product abstract events to rebuild merchant product abstracts after merchant changes.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName): void
    {
        $this->getBusinessFactory()->createProductEventTrigger()->triggerByMerchantEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return array<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            MerchantProductConfig::MERCHANT_PUBLISH,
            MerchantProductConfig::ENTITY_SPY_MERCHANT_UPDATE,
        ];
    }
}
