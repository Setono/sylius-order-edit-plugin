<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

interface InitialTotalAwareOrderInterface
{
    public function getInitialTotal(): int;

    public function setInitialTotal(int $initialTotal): void;
}
