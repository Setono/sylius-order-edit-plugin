<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Setono\SyliusOrderEditPlugin\Setter\OrderDiscountAdjustmentSetterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Webmozart\Assert\Assert;

final class OrderDiscountCollectionType extends CustomDiscountCollectionType
{
    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory,
        private readonly OrderDiscountAdjustmentSetterInterface $orderDiscountAdjustmentSetter,
    ) {
        parent::__construct($adjustmentFactory, 'Custom discount', AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT);
    }

    /** @param OrderInterface $adjustable */
    public function setDiscounts(AdjustableInterface $adjustable, array $discounts): void
    {
        Assert::isInstanceOf($adjustable, OrderInterface::class);

        $adjustable->removeAdjustmentsRecursively($this->adjustmentType);

        /** @var int $discount */
        foreach ($discounts as $discount) {
            $this->orderDiscountAdjustmentSetter->set($adjustable, $discount);
        }
    }
}
