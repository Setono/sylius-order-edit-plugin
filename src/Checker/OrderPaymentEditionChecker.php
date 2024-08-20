<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Checker;

use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class OrderPaymentEditionChecker implements OrderPaymentEditionCheckerInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function shouldOrderPaymentBeEdited(OrderInterface $order): bool
    {
        Assert::isInstanceOf($order, \Sylius\Component\Core\Model\OrderInterface::class);

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return true;
        }

        if ('setono_sylius_order_edit_admin_update' !== $request->attributes->get('_route')) {
            return true;
        }

        return $order->getPaymentState() === OrderPaymentStates::STATE_AWAITING_PAYMENT;
    }
}
