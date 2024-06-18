<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Event;

final class OrderUpdated
{
    public function __construct(public int $orderId)
    {
    }
}
