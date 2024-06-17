<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Preparer;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderPreparerInterface
{
    public function prepareToUpdate(int $orderId): OrderInterface;
}
