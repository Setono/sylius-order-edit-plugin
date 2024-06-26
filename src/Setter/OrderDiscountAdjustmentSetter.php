<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Setter;

use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdderInterface;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderDiscountAdjustmentSetter implements OrderDiscountAdjustmentSetterInterface
{
    public function __construct(
        private readonly MinimumPriceDistributorInterface $minimumPriceDistributor,
        private readonly DiscountAdjustmentsAdderInterface $orderItemDiscountAdjustmentAdder,
    ) {
    }

    public function set(OrderInterface $order, int $discount): void
    {
        $channel = $order->getChannel();
        $items = $order->getItems();

        $distributedPrices = $this->minimumPriceDistributor->distribute($items->toArray(), $discount, $channel, true);

        foreach ($distributedPrices as $i => $distribution) {
            $this->orderItemDiscountAdjustmentAdder->add(
                $items->get($i),
                AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT,
                -$distribution,
            );
        }
    }
}
