<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;

interface PostUpdateChangesCheckerInterface
{
    public function check(OrderInterface $previousOrder, OrderInterface $newOrder): void;
}
