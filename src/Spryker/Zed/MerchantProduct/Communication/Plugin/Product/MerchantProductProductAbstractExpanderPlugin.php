<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Communication\Plugin\Product;

use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductExtension\Dependency\Plugin\ProductAbstractExpanderPluginInterface;

/**
 * @method \Spryker\Zed\MerchantProduct\Business\MerchantProductFacadeInterface getFacade()
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 * @method \Spryker\Zed\MerchantProduct\Communication\MerchantProductCommunicationFactory getFactory()
 */
class MerchantProductProductAbstractExpanderPlugin extends AbstractPlugin implements ProductAbstractExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `ProductAbstractTransfer.idProductAbstract` to be set.
     * - Expands product abstract transfer with merchant ID.
     *
     * @api
     */
    public function expand(ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        $merchantTransfer = $this->getFacade()->findMerchant(
            (new MerchantProductCriteriaTransfer())
                ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract()),
        );

        if ($merchantTransfer === null) {
            return $productAbstractTransfer;
        }

        return $productAbstractTransfer->setIdMerchant($merchantTransfer->getIdMerchant());
    }
}
