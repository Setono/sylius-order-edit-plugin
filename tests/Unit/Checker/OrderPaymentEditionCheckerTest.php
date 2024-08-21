<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Checker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Checker\OrderPaymentEditionChecker;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class OrderPaymentEditionCheckerTest extends TestCase
{
    use ProphecyTrait;

    public function testItSaysOrderPaymentsShouldNotBeEditedIfTheRouteIsOrderEdit(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'setono_sylius_order_edit_admin_update']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $checker = new OrderPaymentEditionChecker($requestStack->reveal());
        $order = new Order();

        self::assertFalse($checker->shouldOrderPaymentBeEdited($order));
    }

    public function testItSaysOrderPaymentCanBeEditedIfItsNotOrderEditRoute(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'sylius_admin_order_any_other_route']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $checker = new OrderPaymentEditionChecker($requestStack->reveal());
        $order = new Order();

        self::assertTrue($checker->shouldOrderPaymentBeEdited($order));
    }

    public function testItSaysOrderPaymentCanBeEditedIfThereIsNoCurrentRequest(): void
    {
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn(null);

        $checker = new OrderPaymentEditionChecker($requestStack->reveal());
        $order = new Order();

        self::assertTrue($checker->shouldOrderPaymentBeEdited($order));
    }
}
