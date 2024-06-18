<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;

interface OrderInitialTotalProcessorInterface
{
    public function process(EditableOrderInterface $order): void;
}
