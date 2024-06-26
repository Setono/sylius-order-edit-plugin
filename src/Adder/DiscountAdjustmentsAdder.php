<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Adder;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;

final class DiscountAdjustmentsAdder implements DiscountAdjustmentsAdderInterface
{
    public function __construct(
        private readonly IntegerDistributorInterface $integerDistributor,
        private readonly AdjustmentFactoryInterface $adjustmentFactory,
    ) {
    }

    public function add(OrderItemInterface $orderItem, string $adjustmentType, int $discount): void
    {
        $discounts = $this->integerDistributor->distribute($discount, $orderItem->getQuantity());
        $units = $orderItem->getUnits();

        foreach ($discounts as $i => $discount) {
            $adjustment = $this->adjustmentFactory->createWithData(
                $adjustmentType,
                'Custom discount',
                $discount,
            );

            $units->get($i)->addAdjustment($adjustment);
        }
    }
}
