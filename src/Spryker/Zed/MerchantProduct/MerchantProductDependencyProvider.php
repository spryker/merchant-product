<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\MerchantProduct\Dependency\External\MerchantProductToValidationAdapter;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventBehaviorFacadeBridge;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToEventFacadeBridge;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToMerchantFacadeBridge;
use Spryker\Zed\MerchantProduct\Dependency\Facade\MerchantProductToProductFacadeBridge;
use Spryker\Zed\MerchantProduct\Dependency\Service\MerchantProductToUtilEncodingServiceBridge;

/**
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 */
class MerchantProductDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_EVENT_BEHAVIOR = 'FACADE_EVENT_BEHAVIOR';

    /**
     * @var string
     */
    public const FACADE_EVENT = 'FACADE_EVENT';

    /**
     * @var string
     */
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';

    /**
     * @var string
     */
    public const FACADE_MERCHANT = 'FACADE_MERCHANT';

    /**
     * @var string
     */
    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';

    /**
     * @var string
     */
    public const EXTERNAL_ADAPTER_VALIDATION = 'EXTERNAL_ADAPTER_VALIDATION';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        parent::provideBusinessLayerDependencies($container);

        $container = $this->addProductFacade($container);
        $container = $this->addValidationAdapter($container);
        $container = $this->addMerchantFacade($container);
        $container = $this->addEventFacade($container);

        return $container;
    }

    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = $this->addUtilEncodingService($container);

        return $container;
    }

    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        parent::provideCommunicationLayerDependencies($container);

        $container = $this->addEventBehaviorFacade($container);

        return $container;
    }

    protected function addEventBehaviorFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT_BEHAVIOR, function (Container $container) {
            return new MerchantProductToEventBehaviorFacadeBridge(
                $container->getLocator()->eventBehavior()->facade(),
            );
        });

        return $container;
    }

    protected function addEventFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT, function (Container $container) {
            return new MerchantProductToEventFacadeBridge(
                $container->getLocator()->event()->facade(),
            );
        });

        return $container;
    }

    protected function addProductFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRODUCT, function (Container $container) {
            return new MerchantProductToProductFacadeBridge($container->getLocator()->product()->facade());
        });

        return $container;
    }

    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new MerchantProductToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    protected function addValidationAdapter(Container $container): Container
    {
        $container->set(static::EXTERNAL_ADAPTER_VALIDATION, function () {
            return new MerchantProductToValidationAdapter();
        });

        return $container;
    }

    protected function addMerchantFacade(Container $container): Container
    {
        $container->set(static::FACADE_MERCHANT, function (Container $container) {
            return new MerchantProductToMerchantFacadeBridge($container->getLocator()->merchant()->facade());
        });

        return $container;
    }
}
