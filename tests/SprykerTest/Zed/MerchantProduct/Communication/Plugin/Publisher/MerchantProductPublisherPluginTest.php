<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProduct\Communication\Plugin\Publisher;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap;
use Spryker\Zed\Merchant\Dependency\MerchantEvents;
use Spryker\Zed\Merchant\MerchantConfig;
use Spryker\Zed\MerchantProduct\Business\MerchantProductBusinessFactory;
use Spryker\Zed\MerchantProduct\Communication\Plugin\Publisher\MerchantProductPublisherPlugin;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventFacadeInterface;
use Spryker\Zed\MerchantProduct\MerchantProductConfig;
use Spryker\Zed\MerchantProduct\MerchantProductDependencyProvider;
use SprykerTest\Zed\MerchantProduct\MerchantProductCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProduct
 * @group Communication
 * @group Plugin
 * @group Publisher
 * @group MerchantProductPublisherPluginTest
 * Add your own group annotations below this line
 */
class MerchantProductPublisherPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantProduct\MerchantProductCommunicationTester
     */
    protected MerchantProductCommunicationTester $tester;

    public function testMerchantSearchEventListenerStoresData(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
        ]);
        $merchantSearchEventListener = new MerchantProductPublisherPlugin();
        $eventTransfers = [
            (new EventEntityTransfer())->setId($merchantTransfer->getIdMerchant()),
        ];

        // Assert
        $this->tester->setDependencyWithExpectedCall($productConcreteTransfer->getFkProductAbstract());

        // Act
        $merchantSearchEventListener->handleBulk($eventTransfers, MerchantEvents::MERCHANT_PUBLISH);
    }

    /**
     * @dataProvider getProductAbstractRelevantMerchantColumnsDataProvider
     */
    public function testGivenMerchantEventWithRelevantModifiedColumnWhenHandlingThenMerchantProductAbstractsAreRepublished(string $modifiedColumn): void
    {
        // Arrange
        [$merchantTransfer, $idProductAbstract] = $this->haveMerchantWithMerchantProduct();
        $triggeredProductAbstractIds = $this->mockEventFacade();

        // Act
        (new MerchantProductPublisherPlugin())->handleBulk(
            [(new EventEntityTransfer())->setId($merchantTransfer->getIdMerchantOrFail())->setModifiedColumns([$modifiedColumn])],
            MerchantEvents::MERCHANT_PUBLISH,
        );

        // Assert
        $this->assertSame([$idProductAbstract], $triggeredProductAbstractIds->getArrayCopy());
    }

    /**
     * @return array<string, array<string>>
     */
    public function getProductAbstractRelevantMerchantColumnsDataProvider(): array
    {
        $dataSets = [];
        foreach ((new MerchantProductConfig())->getProductAbstractRelevantMerchantColumns() as $modifiedColumn) {
            $dataSets[$modifiedColumn] = [$modifiedColumn];
        }

        return $dataSets;
    }

    /**
     * @dataProvider getIrrelevantMerchantModifiedColumnsDataProvider
     *
     * @param list<string> $modifiedColumns
     */
    public function testGivenMerchantEventWithIrrelevantModifiedColumnsWhenHandlingThenNothingIsRepublished(array $modifiedColumns): void
    {
        // Arrange
        [$merchantTransfer] = $this->haveMerchantWithMerchantProduct();
        $triggeredProductAbstractIds = $this->mockEventFacade();

        // Act
        (new MerchantProductPublisherPlugin())->handleBulk(
            [(new EventEntityTransfer())->setId($merchantTransfer->getIdMerchantOrFail())->setModifiedColumns($modifiedColumns)],
            MerchantEvents::ENTITY_SPY_MERCHANT_UPDATE,
        );

        // Assert
        $this->assertSame([], $triggeredProductAbstractIds->getArrayCopy());
    }

    /**
     * @return array<string, array<list<string>>>
     */
    public function getIrrelevantMerchantModifiedColumnsDataProvider(): array
    {
        return [
            'merchant related data marker' => [[MerchantConfig::MODIFIED_COLUMN_MERCHANT_RELATED_DATA]],
            'merchant email' => [[SpyMerchantTableMap::COL_EMAIL]],
        ];
    }

    public function testGivenSeveralMerchantsWithSeveralProductsWhenHandlingMerchantEventsThenEveryMerchantProductAbstractIsRepublished(): void
    {
        // Arrange
        [$firstMerchantTransfer, $firstIdProductAbstract] = $this->haveMerchantWithMerchantProduct();
        [$secondMerchantTransfer, $secondIdProductAbstract] = $this->haveMerchantWithMerchantProduct();
        $thirdIdProductAbstract = $this->tester->haveProduct()->getFkProductAbstractOrFail();
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $secondMerchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $thirdIdProductAbstract,
        ]);
        $expectedProductAbstractIds = [$firstIdProductAbstract, $secondIdProductAbstract, $thirdIdProductAbstract];
        sort($expectedProductAbstractIds);
        $triggeredProductAbstractIds = $this->mockEventFacade();

        // Act
        (new MerchantProductPublisherPlugin())->handleBulk([
            (new EventEntityTransfer())->setId($firstMerchantTransfer->getIdMerchantOrFail()),
            (new EventEntityTransfer())->setId($secondMerchantTransfer->getIdMerchantOrFail()),
        ], MerchantEvents::MERCHANT_PUBLISH);

        // Assert
        $actualProductAbstractIds = array_values(array_unique($triggeredProductAbstractIds->getArrayCopy()));
        sort($actualProductAbstractIds);
        $this->assertSame($expectedProductAbstractIds, $actualProductAbstractIds);
    }

    /**
     * @return array{\Generated\Shared\Transfer\MerchantTransfer, int}
     */
    protected function haveMerchantWithMerchantProduct(): array
    {
        $productConcreteTransfer = $this->tester->haveProduct();
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
        ]);

        return [$merchantTransfer, $productConcreteTransfer->getFkProductAbstractOrFail()];
    }

    /**
     * @return \ArrayObject<int, int>
     */
    protected function mockEventFacade(): ArrayObject
    {
        $triggeredProductAbstractIds = new ArrayObject();
        $eventFacadeMock = $this->createMock(MerchantProductToEventFacadeInterface::class);
        $eventFacadeMock->method('triggerBulk')->willReturnCallback(function (string $eventName, array $eventEntityTransfers) use ($triggeredProductAbstractIds): void {
            foreach ($eventEntityTransfers as $eventEntityTransfer) {
                $triggeredProductAbstractIds->append($eventEntityTransfer->getIdOrFail());
            }
        });
        $this->tester->setDependency(
            MerchantProductDependencyProvider::FACADE_EVENT,
            $eventFacadeMock,
            MerchantProductBusinessFactory::class,
        );

        return $triggeredProductAbstractIds;
    }
}
