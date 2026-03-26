<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProduct\Communication\Plugin\Product;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Spryker\Zed\MerchantProduct\Communication\Plugin\Product\MerchantProductProductAbstractExpanderPlugin;
use SprykerTest\Zed\MerchantProduct\MerchantProductCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProduct
 * @group Communication
 * @group Plugin
 * @group Product
 * @group MerchantProductProductAbstractExpanderPluginTest
 * Add your own group annotations below this line
 */
class MerchantProductProductAbstractExpanderPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantProduct\MerchantProductCommunicationTester
     */
    protected MerchantProductCommunicationTester $tester;

    public function testExpandSetsIdMerchantWhenMerchantProductExists(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
        ]);

        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->setIdProductAbstract($productConcreteTransfer->getFkProductAbstract());

        $plugin = new MerchantProductProductAbstractExpanderPlugin();

        // Act
        $expandedProductAbstractTransfer = $plugin->expand($productAbstractTransfer);

        // Assert
        $this->assertSame($merchantTransfer->getIdMerchant(), $expandedProductAbstractTransfer->getIdMerchant());
    }

    public function testExpandReturnsUnchangedProductAbstractWhenNoMerchantProductExists(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();

        $productAbstractTransfer = (new ProductAbstractTransfer())
            ->setIdProductAbstract($productConcreteTransfer->getFkProductAbstract());

        $plugin = new MerchantProductProductAbstractExpanderPlugin();

        // Act
        $expandedProductAbstractTransfer = $plugin->expand($productAbstractTransfer);

        // Assert
        $this->assertNull($expandedProductAbstractTransfer->getIdMerchant());
    }
}
