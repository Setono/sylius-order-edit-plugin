<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Sylius\Component\Core\Model\OrderInterface;

interface EditableOrderInterface extends OrderInterface
{
    public function isAlreadyPaid(): bool;

    public function getInitialTotal(): int;

    public function setInitialTotal(int $initialTotal): void;

    public function getStoreNotes(): ?string;

    public function setStoreNotes(?string $storeNotes): void;
}
