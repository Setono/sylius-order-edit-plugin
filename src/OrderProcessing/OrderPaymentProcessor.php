<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class OrderPaymentProcessor implements OrderProcessorInterface
{
    public function __construct(
        private readonly OrderProcessorInterface $decorated,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(OrderInterface $order): void
    {
        $route = $this->requestStack->getCurrentRequest()?->attributes->getString('_route');

        // This disables the \Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor if the route is 'sylius_admin_order_update'
        // which means we are editing the order in the admin panel
        if ('sylius_admin_order_update' === $route) {
            return;
        }

        $this->decorated->process($order);
    }
}
