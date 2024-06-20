<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Sylius\Component\Core\Model\OrderInterface;

final class PostUpdateChangesChecker implements PostUpdateChangesCheckerInterface
{
    public function check(EditableOrderInterface $previousOrder, OrderInterface $newOrder): void
    {
        if ($newOrder->getTotal() > $previousOrder->getInitialTotal()) {
            throw new NewOrderWrongTotalException();
        }
    }
}
