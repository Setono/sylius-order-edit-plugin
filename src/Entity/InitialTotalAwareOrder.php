<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

trait InitialTotalAwareOrder
{
    /**
     * @ORM\Column(type="integer", name="initial_total", options={"default": 0})
     */
    private int $initialTotal = 0;

    public function getInitialTotal(): int
    {
        return $this->initialTotal;
    }

    public function setInitialTotal(int $initialTotal): void
    {
        $this->initialTotal = $initialTotal;
    }
}
