<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Generated\Shared\Transfer\MerchantProductTransfer;

interface MerchantProductEntityManagerInterface
{
    public function create(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer;
}
