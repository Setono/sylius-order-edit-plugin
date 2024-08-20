<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Checker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Checker\PostCheckoutOrderPaymentEditionChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PostCheckoutOrderPaymentEditionCheckerTest extends TestCase
{
    use ProphecyTrait;

    public function testItSaysOrderPaymentsShouldNotBeEditedIfTheRouteIsOrderEditAndThePaymentIsAlreadyProcessed(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'setono_sylius_order_edit_admin_update']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $checker = new PostCheckoutOrderPaymentEditionChecker($requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AUTHORIZED);

        self::assertFalse($checker->shouldOrderPaymentBeEdited($order->reveal()));
    }

    public function testItSaysOrderPaymentShouldBeEditedItTheRouteIsOrderEditButOrderIsAwaitingPayment(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'setono_sylius_order_edit_admin_update']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $checker = new PostCheckoutOrderPaymentEditionChecker($requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_AWAITING_PAYMENT);

        self::assertTrue($checker->shouldOrderPaymentBeEdited($order->reveal()));
    }

    public function testItSaysOrderPaymentShouldBeEditedIfItsNotOrderEditRoute(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'sylius_admin_order_any_other_route']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $checker = new PostCheckoutOrderPaymentEditionChecker($requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);

        self::assertTrue($checker->shouldOrderPaymentBeEdited($order->reveal()));
    }

    public function testItSaysOrderPaymentShouldBeEditedIfThereIsNoCurrentRequest(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn(null);

        $checker = new PostCheckoutOrderPaymentEditionChecker($requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);

        self::assertTrue($checker->shouldOrderPaymentBeEdited($order->reveal()));
    }
}
