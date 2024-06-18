<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

trait InitialTotalAwareOrderTrait
{
    /** @ORM\Column(type="integer") */
    #[ORM\Column(type: 'integer')]
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
