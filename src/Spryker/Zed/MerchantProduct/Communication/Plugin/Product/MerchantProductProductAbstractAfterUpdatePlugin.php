<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Communication\Plugin\Product;

use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Product\Dependency\Plugin\ProductAbstractPluginUpdateInterface;

/**
 * @method \Spryker\Zed\MerchantProduct\Business\MerchantProductFacadeInterface getFacade()
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 * @method \Spryker\Zed\MerchantProduct\Communication\MerchantProductCommunicationFactory getFactory()
 */
class MerchantProductProductAbstractAfterUpdatePlugin extends AbstractPlugin implements ProductAbstractPluginUpdateInterface
{
    /**
     * {@inheritDoc}
     * - Updates or removes the merchant-product abstract association based on `ProductAbstractTransfer.idMerchant`.
     *
     * @api
     */
    public function update(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        $this->getFacade()->updateMerchantProduct(
            (new MerchantProductTransfer())
                ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract())
                ->setIdMerchant($productAbstractTransfer->getIdMerchant()),
        );

        return $productAbstractTransfer;
    }
}
