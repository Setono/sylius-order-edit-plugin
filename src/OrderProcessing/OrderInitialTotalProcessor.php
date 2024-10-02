<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;

final class OrderInitialTotalProcessor implements OrderInitialTotalProcessorInterface
{
    public function process(EditableOrderInterface $order): void
    {
        $order->setInitialTotal($order->getTotal());
    }
}
