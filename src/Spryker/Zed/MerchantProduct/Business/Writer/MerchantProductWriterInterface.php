<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Writer;

use Generated\Shared\Transfer\MerchantProductTransfer;

interface MerchantProductWriterInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantProductTransfer $merchantProductTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantProductTransfer
     */
    public function create(MerchantProductTransfer $merchantProductTransfer): MerchantProductTransfer;
}
