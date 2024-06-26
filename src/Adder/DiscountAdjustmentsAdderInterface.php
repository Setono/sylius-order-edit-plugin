<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Adder;

use Sylius\Component\Core\Model\OrderItemInterface;

interface DiscountAdjustmentsAdderInterface
{
    public function add(
        OrderItemInterface $orderItem,
        string $adjustmentType,
        string $originCode,
        string $label,
        int $discount,
    ): void;
}
