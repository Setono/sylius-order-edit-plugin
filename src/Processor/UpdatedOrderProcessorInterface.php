<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Processor;

use Sylius\Component\Core\Model\OrderInterface;

interface UpdatedOrderProcessorInterface
{
    public function process(OrderInterface $newOrder): OrderInterface;
}
