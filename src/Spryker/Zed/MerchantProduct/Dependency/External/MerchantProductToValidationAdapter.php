<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Dependency\External;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MerchantProductToValidationAdapter implements MerchantProductToValidationAdapterInterface
{
    public function createValidator(): ValidatorInterface
    {
        return Validation::createValidator();
    }
}
