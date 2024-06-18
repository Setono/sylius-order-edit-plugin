<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;

final class OrderDiscountCollectionType extends CustomDiscountCollectionType
{
    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        parent::__construct($adjustmentFactory);

        $this->label = 'Custom discount';
        $this->adjustmentType = AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT;
    }
}
