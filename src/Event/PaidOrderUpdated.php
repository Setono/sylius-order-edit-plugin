<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Event;

final class PaidOrderUpdated
{
    public function __construct(public int $orderId, public int $oldTotal, public int $newTotal)
    {
    }
}
