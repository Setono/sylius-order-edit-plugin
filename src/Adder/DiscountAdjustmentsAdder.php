<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Adder;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
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

        /** @var int $discount */
        foreach ($discounts as $i => $discount) {
            /** @var AdjustmentInterface $adjustment */
            $adjustment = $this->adjustmentFactory->createWithData(
                $adjustmentType,
                'Custom discount',
                $discount,
            );

            /** @var OrderItemUnitInterface $unit */
            $unit = $units->get($i);
            $unit->addAdjustment($adjustment);
        }
    }
}
