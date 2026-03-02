<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProduct\Business\Validator\Constraint;

use Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

class ProductAbstractBelongsToMerchantConstraint extends SymfonyConstraint
{
    /**
     * @var string
     */
    protected const MESSAGE = 'Merchant product is not found for product abstract id %d and merchant id %d.';

    /**
     * @var \Spryker\Zed\MerchantProduct\Persistence\MerchantProductRepositoryInterface
     */
    protected $merchantProductRepository;

    public function __construct(MerchantProductRepositoryInterface $merchantProductRepository)
    {
        $this->merchantProductRepository = $merchantProductRepository;

        parent::__construct();
    }

    public function getMerchantProductRepository(): MerchantProductRepositoryInterface
    {
        return $this->merchantProductRepository;
    }

    public function getMessage(): string
    {
        return static::MESSAGE;
    }

    public function getTargets(): string
    {
        return static::CLASS_CONSTRAINT;
    }
}
