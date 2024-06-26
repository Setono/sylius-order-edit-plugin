<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;

/** @mixin OrderInterface */
trait EditableOrderTrait
{
    /** @ORM\Column(type="integer") */
    #[ORM\Column(type: 'integer')]
    private int $initialTotal = 0;

    /** @ORM\Column(type="text", nullable=true) */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $storeNotes = null;

    public function isAlreadyPaid(): bool
    {
        return $this->getPaymentState() === OrderPaymentStates::STATE_PAID;
    }

    public function getInitialTotal(): int
    {
        return $this->initialTotal;
    }

    public function setInitialTotal(int $initialTotal): void
    {
        $this->initialTotal = $initialTotal;
    }

    public function getStoreNotes(): ?string
    {
        return $this->storeNotes;
    }

    public function setStoreNotes(?string $storeNotes): void
    {
        $this->storeNotes = $storeNotes;
    }
}
