<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\MerchantProduct\Dependency\Service\MerchantProductToUtilEncodingServiceInterface;
use Spryker\Zed\MerchantProduct\MerchantProductDependencyProvider;
use Spryker\Zed\MerchantProduct\Persistence\Propel\Mapper\MerchantMapper;
use Spryker\Zed\MerchantProduct\Persistence\Propel\Mapper\MerchantProductMapper;

/**
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface getRepository()
 * @method \Spryker\Zed\MerchantProduct\MerchantProductConfig getConfig()
 * @method \Spryker\Zed\MerchantProduct\Persistence\MerchantProductEntityManagerInterface getEntityManager()
 */
class MerchantProductPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\MerchantProduct\Persistence\SpyMerchantProductAbstractQuery
     */
    public function getMerchantProductAbstractPropelQuery(): SpyMerchantProductAbstractQuery
    {
        return SpyMerchantProductAbstractQuery::create();
    }

    /**
     * @return \Spryker\Zed\MerchantProduct\Persistence\Propel\Mapper\MerchantMapper
     */
    public function createMerchantMapper(): MerchantMapper
    {
        return new MerchantMapper();
    }

    /**
     * @return \Spryker\Zed\MerchantProduct\Persistence\Propel\Mapper\MerchantProductMapper
     */
    public function createMerchantProductMapper(): MerchantProductMapper
    {
        return new MerchantProductMapper($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Zed\MerchantProduct\Dependency\Service\MerchantProductToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): MerchantProductToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(MerchantProductDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
