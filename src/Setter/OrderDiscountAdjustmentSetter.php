<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Setter;

use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdderInterface;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderDiscountAdjustmentSetter implements OrderDiscountAdjustmentSetterInterface
{
    public function __construct(
        private readonly IntegerDistributorInterface $integerDistributor,
        private readonly DiscountAdjustmentsAdderInterface $orderItemDiscountAdjustmentAdder,
    ) {
    }

    public function set(OrderInterface $order, int $discount): void
    {
        /** @var array<int, OrderItemInterface> $items */
        $items = $order->getItems()->getValues();
        $orderId = (int) $order->getId();

        $distributedPrices = $this->integerDistributor->distribute($discount, count($items));

        /**
         * @var int $i
         * @var int $distribution
         */
        foreach ($distributedPrices as $i => $distribution) {
            $item = $items[$i];
            $this->orderItemDiscountAdjustmentAdder->add(
                $item,
                AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT,
                AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT . '_' . $orderId,
                'Custom order discount',
                -$distribution,
            );
        }
    }
}
