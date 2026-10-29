<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class MerchantProductConfig extends AbstractBundleConfig
{
    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap::COL_IS_ACTIVE
     */
    protected const string COL_MERCHANT_IS_ACTIVE = 'spy_merchant.is_active';

    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap::COL_STATUS
     */
    protected const string COL_MERCHANT_STATUS = 'spy_merchant.status';

    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantTableMap::COL_NAME
     */
    protected const string COL_MERCHANT_NAME = 'spy_merchant.name';

    /**
     * @uses \Orm\Zed\Merchant\Persistence\Map\SpyMerchantStoreTableMap::COL_FK_STORE
     */
    protected const string COL_MERCHANT_STORE_FK_STORE = 'spy_merchant_store.fk_store';

    /**
     * Specification:
     * - Returns the merchant columns that change the merchant data of merchant product abstracts.
     * - A merchant event that has modified columns republishes the merchant product abstracts only when one of these columns changed.
     * - A merchant event without modified columns always republishes the merchant product abstracts.
     *
     * @api
     *
     * @return list<string>
     */
    public function getProductAbstractRelevantMerchantColumns(): array
    {
        return [
            static::COL_MERCHANT_IS_ACTIVE,
            static::COL_MERCHANT_STATUS,
            static::COL_MERCHANT_NAME,
            static::COL_MERCHANT_STORE_FK_STORE,
        ];
    }
}
