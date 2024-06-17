<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Sylius\Component\Core\Model\OrderInterface;

final class PostUpdateChangesChecker implements PostUpdateChangesCheckerInterface
{
    public function check(OrderInterface $previousOrder, OrderInterface $newOrder): void
    {
        if ($newOrder->getTotal() > $previousOrder->getTotal()) {
            throw new NewOrderWrongTotalException();
        }
    }
}
