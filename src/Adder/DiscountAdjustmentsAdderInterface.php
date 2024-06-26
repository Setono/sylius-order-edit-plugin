<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Adder;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

interface DiscountAdjustmentsAdderInterface
{
    public function add(OrderItemInterface $orderItem, string $adjustmentType, int $discount): void;
}
