<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Expander;

use Generated\Shared\Transfer\MerchantProductCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\ShoppingListItemCollectionTransfer;
use Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface;

class ShoppingListItemExpander implements ShoppingListItemExpanderInterface
{
    /**
     * @var \Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface
     */
    protected $merchantProductRepository;

    public function __construct(MerchantProductRepositoryInterface $merchantProductRepository)
    {
        $this->merchantProductRepository = $merchantProductRepository;
    }

    public function expandShoppingListItemCollectionWithMerchantReference(
        ShoppingListItemCollectionTransfer $shoppingListItemCollectionTransfer
    ): ShoppingListItemCollectionTransfer {
        $productCriteriaTransfer = $this->getMerchantProductCriteriaTransfer($shoppingListItemCollectionTransfer);

        if (count($productCriteriaTransfer->getProductConcreteSkus()) === 0) {
            return $shoppingListItemCollectionTransfer;
        }

        $merchantProductCollectionTransfer = $this->merchantProductRepository->get($productCriteriaTransfer);

        $merchantReferencesIndexedByProductConcreteSku = $this->getMerchantReferencesIndexedByProductConcreteSku(
            $merchantProductCollectionTransfer,
        );

        foreach ($shoppingListItemCollectionTransfer->getItems() as $shoppingListItemTransfer) {
            if ($shoppingListItemTransfer->getMerchantReference()) {
                continue;
            }

            $productConcreteSku = $shoppingListItemTransfer->getSku();
            $merchantReference = $merchantReferencesIndexedByProductConcreteSku[$productConcreteSku] ?? null;

            if ($merchantReference) {
                $shoppingListItemTransfer->setMerchantReference($merchantReference);
            }
        }

        return $shoppingListItemCollectionTransfer;
    }

    protected function getMerchantProductCriteriaTransfer(
        ShoppingListItemCollectionTransfer $shoppingListItemCollectionTransfer
    ): MerchantProductCriteriaTransfer {
        $merchantProductCriteriaTransfer = new MerchantProductCriteriaTransfer();

        foreach ($shoppingListItemCollectionTransfer->getItems() as $item) {
            if (!$item->getMerchantReference() && $item->getSku()) {
                $merchantProductCriteriaTransfer->addProductConcreteSku($item->getSku());
            }
        }

        return $merchantProductCriteriaTransfer;
    }

    protected function getMerchantReferencesIndexedByProductConcreteSku(
        MerchantProductCollectionTransfer $merchantProductCollectionTransfer
    ): array {
        $merchantReferencesIndexedByProductConcreteSku = [];

        foreach ($merchantProductCollectionTransfer->getMerchantProducts() as $merchantProductTransfer) {
            foreach ($merchantProductTransfer->getProducts() as $productConcreteTransfer) {
                $merchantReferencesIndexedByProductConcreteSku[$productConcreteTransfer->getSku()] = $merchantProductTransfer->getMerchantReference();
            }
        }

        return $merchantReferencesIndexedByProductConcreteSku;
    }
}
