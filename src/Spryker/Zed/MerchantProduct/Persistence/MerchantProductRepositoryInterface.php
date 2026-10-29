<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Persistence;

use Generated\Shared\Transfer\MerchantProductAbstractCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductAbstractCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantProductRepositoryInterface
{
    public function findMerchant(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): ?MerchantTransfer;

    public function get(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): MerchantProductCollectionTransfer;

    /**
     * @param array<string> $concreteSku
     *
     * @return array<string, string>
     */
    public function getConcreteProductSkuMerchantReferenceMap(array $concreteSku): array;

    public function findMerchantProduct(
        MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer
    ): ?MerchantProductTransfer;

    public function getMerchantProductAbstractCollection(
        MerchantProductAbstractCriteriaTransfer $merchantProductAbstractCriteriaTransfer
    ): MerchantProductAbstractCollectionTransfer;

    /**
     * @param array<int> $merchantIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByMerchantIds(array $merchantIds): array;

    /**
     * Reads the merchant product abstracts of each given merchant in pages of `$batchSize`, ordered by `id_merchant_product_abstract`, and yields their abstract product IDs.
     *
     * @param array<int> $merchantIds
     * @param int $batchSize
     *
     * @return iterable<array<int>>
     */
    public function iterateProductAbstractIdsByMerchantIds(array $merchantIds, int $batchSize): iterable;
}
