<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Setter;

use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdderInterface;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderDiscountAdjustmentSetter implements OrderDiscountAdjustmentSetterInterface
{
    public function __construct(
        private readonly MinimumPriceDistributorInterface $minimumPriceDistributor,
        private readonly DiscountAdjustmentsAdderInterface $orderItemDiscountAdjustmentAdder,
    ) {
    }

    public function set(OrderInterface $order, int $discount): void
    {
        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        $items = $order->getItems();
        /** @var int $orderId */
        $orderId = $order->getId();

        $distributedPrices = $this->minimumPriceDistributor->distribute($items->toArray(), $discount, $channel, true);

        /** @var int $distribution */
        foreach ($distributedPrices as $i => $distribution) {
            /** @var OrderItemInterface $item */
            $item = $items->get($i);
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
