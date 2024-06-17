<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface PostUpdateChangesCheckerInterface
{
    public function check(
        InitialTotalAwareOrderInterface $previousOrder,
        OrderInterface $newOrder
    ): void;
}
