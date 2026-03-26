<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\MerchantProduct\Business\Checker\MerchantProductShoppingListItemChecker;
use Spryker\Zed\MerchantProduct\Business\Checker\MerchantProductShoppingListItemCheckerInterface;
use Spryker\Zed\MerchantProduct\Business\Event\ProductEventTrigger;
use Spryker\Zed\MerchantProduct\Business\Event\ProductEventTriggerInterface;
use Spryker\Zed\MerchantProduct\Business\Expander\ShoppingListItemExpander;
use Spryker\Zed\MerchantProduct\Business\Expander\ShoppingListItemExpanderInterface;
use Spryker\Zed\MerchantProduct\Business\Hydrator\CartReorderItemHydrator;
use Spryker\Zed\MerchantProduct\Business\Hydrator\CartReorderItemHydratorInterface;
use Spryker\Zed\MerchantProduct\Business\Reader\MerchantProductReader;
use Spryker\Zed\MerchantProduct\Business\Reader\MerchantProductReaderInterface;
use Spryker\Zed\MerchantProduct\Business\Updater\MerchantProductUpdater;
use Spryker\Zed\MerchantProduct\Business\Updater\MerchantProductUpdaterInterface;
use Spryker\Zed\MerchantProduct\Business\Validator\Constraint\ProductAbstractBelongsToMerchantConstraint;
use Spryker\Zed\MerchantProduct\Business\Validator\MerchantProductCartValidator;
use Spryker\Zed\MerchantProduct\Business\Validator\MerchantProductCartValidatorInterface;
use Spryker\Zed\MerchantProduct\Business\Validator\MerchantProductValidator;
use Spryker\Zed\MerchantProduct\Business\Validator\MerchantProductValidatorInterface;
use Spryker\Zed\MerchantProduct\Business\Writer\MerchantProductWriter;
use Spryker\Zed\MerchantProduct\Business\Writer\MerchantProductWriterInterface;
use Spryker\Zed\MerchantProduct\Dependency\External\MerchantProductToValidationAdapterInterface;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventFacadeInterface;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToMerchantFacadeInterface;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToProductFacadeInterface;
use Spryker\Zed\MerchantProduct\MerchantProductDependencyProvider;
use Symfony\Component\Validator\Constraint;

/**
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface getRepository()
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductEntityManagerInterface getEntityManager()
 */
class MerchantProductBusinessFactory extends AbstractBusinessFactory
{
    public function createMerchantProductCartValidator(): MerchantProductCartValidatorInterface
    {
        return new MerchantProductCartValidator(
            $this->getRepository(),
        );
    }

    public function createMerchantProductReader(): MerchantProductReaderInterface
    {
        return new MerchantProductReader(
            $this->getRepository(),
            $this->getProductFacade(),
        );
    }

    public function createMerchantProductUpdater(): MerchantProductUpdaterInterface
    {
        return new MerchantProductUpdater(
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    public function createMerchantProductWriter(): MerchantProductWriterInterface
    {
        return new MerchantProductWriter(
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    public function createMerchantProductValidator(): MerchantProductValidatorInterface
    {
        return new MerchantProductValidator(
            $this->getValidationAdapter(),
            $this->getMerchantProductConstraints(),
        );
    }

    public function createProductEventTrigger(): ProductEventTriggerInterface
    {
        return new ProductEventTrigger($this->getRepository(), $this->getEventFacade());
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    public function getMerchantProductConstraints(): array
    {
        return [
            $this->createProductAbstractBelongsToMerchantConstraint(),
        ];
    }

    public function createProductAbstractBelongsToMerchantConstraint(): Constraint
    {
        return new ProductAbstractBelongsToMerchantConstraint($this->getRepository());
    }

    public function createShoppingListItemExpander(): ShoppingListItemExpanderInterface
    {
        return new ShoppingListItemExpander(
            $this->getRepository(),
        );
    }

    public function createCartReorderItemHydrator(): CartReorderItemHydratorInterface
    {
        return new CartReorderItemHydrator();
    }

    public function getProductFacade(): MerchantProductToProductFacadeInterface
    {
        return $this->getProvidedDependency(MerchantProductDependencyProvider::FACADE_PRODUCT);
    }

    public function getValidationAdapter(): MerchantProductToValidationAdapterInterface
    {
        return $this->getProvidedDependency(MerchantProductDependencyProvider::EXTERNAL_ADAPTER_VALIDATION);
    }

    public function createMerchantProductShoppingListItemChecker(): MerchantProductShoppingListItemCheckerInterface
    {
        return new MerchantProductShoppingListItemChecker(
            $this->getMerchantFacade(),
        );
    }

    public function getMerchantFacade(): MerchantProductToMerchantFacadeInterface
    {
        return $this->getProvidedDependency(MerchantProductDependencyProvider::FACADE_MERCHANT);
    }

    public function getEventFacade(): MerchantProductToEventFacadeInterface
    {
        return $this->getProvidedDependency(MerchantProductDependencyProvider::FACADE_EVENT);
    }
}
