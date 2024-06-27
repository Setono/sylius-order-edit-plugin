<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Setter;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderDiscountAdjustmentSetterInterface
{
    public function set(OrderInterface $order, int $discount): void;
}
