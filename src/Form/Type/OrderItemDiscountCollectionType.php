<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;

final class OrderItemDiscountCollectionType extends CustomDiscountCollectionType
{
    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        parent::__construct($adjustmentFactory, 'Custom item discount', AdjustmentTypes::SETONO_ADMIN_ORDER_ITEM_DISCOUNT);
    }
}
