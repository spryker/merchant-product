<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProduct\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CartChangeTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProduct
 * @group Business
 * @group Facade
 * @group MerchantProductFacadeTest
 *
 * Add your own group annotations below this line
 */
class MerchantProductFacadeTest extends Unit
{
    protected const TEST_SKU = 'test-sku';
    protected const TEST_PRODUCT_OFFER_REFERENCE = 'test-product-offer-reference';
    protected const TEST_MERCHANT_REFERENCE = 'test-merchant-reference';

    /**
     * @var \SprykerTest\Zed\MerchantProduct\MerchantProductBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testFindMerchantReturnsMerchant(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $expectedMerchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $expectedMerchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant(
            (new MerchantProductCriteriaTransfer())->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract())
        );

        // Assert
        $this->assertSame($expectedMerchantTransfer->getIdMerchant(), $merchantTransfer->getIdMerchant());
        $this->assertSame($expectedMerchantTransfer->getName(), $merchantTransfer->getName());
    }

    /**
     * @return void
     */
    public function testFindMerchantForNotExistingMerchantProductReturnsNull(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();

        // Act
        $merchantTransfer = $this->tester->getFacade()->findMerchant(
            (new MerchantProductCriteriaTransfer())->setIdProductAbstract(1)
        );

        // Assert
        $this->assertNull($merchantTransfer);
    }

    /**
     * @return void
     */
    public function testGetByIdProductAbstract(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(1, $merchantProductCollectionTransfer->getMerchantProducts());
    }

    /**
     * @return void
     */
    public function testGetByIdMerchantProductAbstracts(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $merchantProductTransfer1 = $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $merchantProductTransfer2 = $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer2->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->addMerchantProductAbstractId($merchantProductTransfer1->getIdMerchantProductAbstract())
            ->addMerchantProductAbstractId($merchantProductTransfer2->getIdMerchantProductAbstract());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(2, $merchantProductCollectionTransfer->getMerchantProducts());
    }

    /**
     * @return void
     */
    public function testGetByIdMerchants(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $merchantTransfer2 = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $productAbstractTransfer2 = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer->getIdProductAbstract(),
        ]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer2->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productAbstractTransfer2->getIdProductAbstract(),
        ]);
        $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
            ->addIdMerchant($merchantTransfer->getIdMerchant())
            ->addIdMerchant($merchantTransfer2->getIdMerchant());

        // Act
        $merchantProductCollectionTransfer = $this->tester->getFacade()->get($merchantProductCriteriaTransfer);

        // Assert
        $this->assertCount(2, $merchantProductCollectionTransfer->getMerchantProducts());
    }

    /**
     * @return void
     */
    public function testValidateCartChangeIgnoresGenericProducts(): void
    {
        // Arrange
        $cartChangeTransfer = (new CartChangeTransfer())
            ->addItem(
                (new ItemTransfer())
                    ->setSku(static::TEST_SKU)
            );

        // Act
        $cartPreCheckResponseTransfer = $this->tester->getFacade()->validateCartChange($cartChangeTransfer);

        // Assert
        $this->assertTrue($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertEmpty($cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateCartChangeIgnoresItemsWithProductOfferReference(): void
    {
        // Arrange
        $cartChangeTransfer = (new CartChangeTransfer())
            ->addItem(
                (new ItemTransfer())
                    ->setSku(static::TEST_SKU)
                    ->setProductOfferReference(static::TEST_PRODUCT_OFFER_REFERENCE)
            );

        // Act
        $cartPreCheckResponseTransfer = $this->tester->getFacade()->validateCartChange($cartChangeTransfer);

        // Assert
        $this->assertTrue($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertEmpty($cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateCartChangeFailsForInvalidMerchantProducts(): void
    {
        // Arrange
        $cartChangeTransfer = (new CartChangeTransfer())
            ->addItem(
                (new ItemTransfer())
                    ->setSku(static::TEST_SKU)
                    ->setMerchantReference(static::TEST_MERCHANT_REFERENCE)
            );

        // Act
        $cartPreCheckResponseTransfer = $this->tester->getFacade()->validateCartChange($cartChangeTransfer);

        // Assert
        $this->assertFalse($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertNotEmpty($cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testValidateCartChangeSuccessForValidMerchantProduct(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();

        $merchantTransfer = $this->tester->haveMerchant();
        $concreteProductTransfer = $this->tester->haveProduct();

        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $concreteProductTransfer->getFkProductAbstract(),
        ]);

        $cartChangeTransfer = (new CartChangeTransfer())
            ->addItem(
                (new ItemTransfer())
                    ->setAbstractSku($concreteProductTransfer->getAbstractSku())
                    ->setMerchantReference($merchantTransfer->getMerchantReference())
                    ->setSku($concreteProductTransfer->getSku())
            );

        // Act
        $cartPreCheckResponseTransfer = $this->tester->getFacade()->validateCartChange($cartChangeTransfer);

        // Assert
        $this->assertTrue($cartPreCheckResponseTransfer->getIsSuccess());
        $this->assertEmpty($cartPreCheckResponseTransfer->getMessages());
    }

    /**
     * @return void
     */
    public function testFindProductAbstractFindsExistingProductByIdProductAbstractAndIdMerchant(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant();
        $expectedProductAbstractTransfer = $this->tester->haveProductAbstract();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $expectedProductAbstractTransfer->getIdProductAbstract(),
        ]);

        // Act
        $productAbstractTransfer = $this->tester->getFacade()->findProductAbstract(
            (new MerchantProductCriteriaTransfer())
                ->setIdProductAbstract($expectedProductAbstractTransfer->getIdProductAbstract())
                ->setIdMerchant($merchantTransfer->getIdMerchant())
        );

        // Assert
        $this->assertNotNull($productAbstractTransfer);
        $this->assertSame($expectedProductAbstractTransfer->getIdProductAbstract(), $productAbstractTransfer->getIdProductAbstract());
    }

    /**
     * @return void
     */
    public function testFindProductAbstractReturnsNullIfMerchantProductDoesNotExist(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $productAbstractTransfer = $this->tester->haveProductAbstract();

        // Act
        $productAbstractTransfer = $this->tester->getFacade()->findProductAbstract(
            (new MerchantProductCriteriaTransfer())
                ->setIdProductAbstract($productAbstractTransfer->getIdProductAbstract())
                ->setIdMerchant($merchantTransfer->getIdMerchant())
        );

        // Assert
        $this->assertNull($productAbstractTransfer);
    }

    /**
     * @return void
     */
    public function testUpdateProductAbstractUpdatesProductIfItBelongsToMerchant(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer = $this->tester->haveMerchant();
        $expectedProductAbstractTransfer = $this->tester->haveProductAbstract();
        $expectedProductAbstractTransfer->setSku($expectedProductAbstractTransfer->getSku() . '_changed');
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $expectedProductAbstractTransfer->getIdProductAbstract(),
        ]);

        // Act
        $productAbstractResponseTransfer = $this->tester->getFacade()->updateProductAbstract(
            (new MerchantProductTransfer())
                ->setIdProductAbstract($expectedProductAbstractTransfer->getIdProductAbstract())
                ->setIdMerchant($merchantTransfer->getIdMerchant())
                ->setProductAbstract($expectedProductAbstractTransfer)
        );
        $productAbstractTransfer = $this->tester->findProductAbstractById($expectedProductAbstractTransfer->getIdProductAbstract());

        // Assert
        $this->assertTrue($productAbstractResponseTransfer->getIsSuccessful());
        $this->assertNotNull($productAbstractTransfer);
        $this->assertSame($expectedProductAbstractTransfer->getSku(), $productAbstractTransfer->getSku());
    }

    /**
     * @return void
     */
    public function testUpdateProductAbstractDoesNotUpdateProductIfItDoesNotBelongToMerchant(): void
    {
        // Arrange
        $this->tester->ensureMerchantProductAbstractTableIsEmpty();
        $merchantTransfer1 = $this->tester->haveMerchant();
        $merchantTransfer2 = $this->tester->haveMerchant();
        $expectedProductAbstractTransfer = $this->tester->haveProductAbstract();
        $expectedSku = $expectedProductAbstractTransfer->getSku();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer1->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $expectedProductAbstractTransfer->getIdProductAbstract(),
        ]);

        // Act
        $productAbstractResponseTransfer = $this->tester->getFacade()->updateProductAbstract(
            (new MerchantProductTransfer())
                ->setIdProductAbstract($expectedProductAbstractTransfer->getIdProductAbstract())
                ->setIdMerchant($merchantTransfer2->getIdMerchant())
                ->setProductAbstract($expectedProductAbstractTransfer->setSku($expectedProductAbstractTransfer->getSku() . '_changed'))
        );
        $productAbstractTransfer = $this->tester->findProductAbstractById($expectedProductAbstractTransfer->getIdProductAbstract());

        // Assert
        $this->assertFalse($productAbstractResponseTransfer->getIsSuccessful());
        $this->assertNotNull($productAbstractTransfer);
        $this->assertSame($expectedSku, $productAbstractTransfer->getSku());
    }
}
