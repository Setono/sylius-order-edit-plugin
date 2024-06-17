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
        /** @var mixed $route */
        $route = $this->requestStack->getCurrentRequest()?->attributes->get('_route');

        // This disables the \Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor if the route is 'setono_sylius_order_edit_admin_update'
        // which means we are editing the order in the admin panel
        if ('setono_sylius_order_edit_admin_update' === $route) {
            return;
        }

        $this->decorated->process($order);
    }
}
