<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface PostUpdateChangesCheckerInterface
{
    public function check(EditableOrderInterface $previousOrder, OrderInterface $newOrder): void;
}
