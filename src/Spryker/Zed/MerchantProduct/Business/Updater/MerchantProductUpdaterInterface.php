<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Updater;

use Generated\Shared\Transfer\MerchantProductTransfer;

interface MerchantProductUpdaterInterface
{
    public function updateMerchantProduct(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer;
}
