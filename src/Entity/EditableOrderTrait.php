<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Entity;

use Sylius\Component\Core\OrderPaymentStates;

/** @method getPaymentState() */
trait EditableOrderTrait
{
    public function isAlreadyPaid(): bool
    {
        return $this->getPaymentState() === OrderPaymentStates::STATE_PAID;
    }
}
