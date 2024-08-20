<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Sylius\Component\Order\Model\OrderInterface;

interface OrderPaymentEditionCheckerInterface
{
    public function shouldOrderPaymentBeEdited(OrderInterface $order): bool;
}
