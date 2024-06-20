<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Preparer;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;

interface OrderPreparerInterface
{
    public function prepareToUpdate(int $orderId): EditableOrderInterface;
}
