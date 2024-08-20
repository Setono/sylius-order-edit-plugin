<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Setono\SyliusOrderEditPlugin\Checker\OrderPaymentEditionCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessor implements OrderProcessorInterface
{
    public function __construct(
        private readonly OrderProcessorInterface $decorated,
        private readonly OrderPaymentEditionCheckerInterface $orderPaymentEditionChecker,
    ) {
    }

    public function process(OrderInterface $order): void
    {
        if (!$this->orderPaymentEditionChecker->shouldOrderPaymentBeEdited($order)) {
            return;
        }

        $this->decorated->process($order);
    }
}
