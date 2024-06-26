<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdderInterface;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Webmozart\Assert\Assert;

final class OrderItemDiscountCollectionType extends CustomDiscountCollectionType
{
    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory,
        private readonly DiscountAdjustmentsAdderInterface $discountAdjustmentsAdder,
    ) {
        parent::__construct($adjustmentFactory, 'Custom item discount', AdjustmentTypes::SETONO_ADMIN_ORDER_ITEM_DISCOUNT);
    }

    /** @param OrderItemInterface $adjustable */
    public function setDiscounts(AdjustableInterface $adjustable, array $discounts): void
    {
        Assert::isInstanceOf($adjustable, OrderItemInterface::class);

        $adjustable->removeAdjustmentsRecursively($this->adjustmentType);

        /** @var int $discount */
        foreach ($discounts as $discount) {
            $this->discountAdjustmentsAdder->add($adjustable, $this->adjustmentType, -$discount);
        }
    }
}
