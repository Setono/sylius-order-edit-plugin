<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Sylius\Component\Core\Model\OrderInterface;

interface EditableOrderInterface extends OrderInterface, InitialTotalAwareOrderInterface
{
    public function isAlreadyPaid(): bool;
}
