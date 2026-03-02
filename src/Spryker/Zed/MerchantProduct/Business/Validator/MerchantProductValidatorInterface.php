<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Validator;

use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\ValidationResponseTransfer;

interface MerchantProductValidatorInterface
{
    public function validateMerchantProduct(MerchantProductTransfer $merchantProductTransfer): ValidationResponseTransfer;
}
