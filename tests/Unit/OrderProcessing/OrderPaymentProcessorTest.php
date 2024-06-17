<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\OrderProcessing;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class OrderPaymentProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItDoesNotAllowToProcessOrderIfItsEdited(): void
    {
        $orderProcessor = $this->prophesize(OrderProcessorInterface::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'setono_sylius_order_edit_admin_update']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $processor = new OrderPaymentProcessor($orderProcessor->reveal(), $requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);

        $orderProcessor->process($order)->shouldNotBeCalled();

        $processor->process($order->reveal());
    }

    public function testItDoesNothingIfItsDifferentRoute(): void
    {
        $orderProcessor = $this->prophesize(OrderProcessorInterface::class);
        $requestStack = $this->prophesize(RequestStack::class);
        $request = new Request([], [], ['_route' => 'some_other_route']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $processor = new OrderPaymentProcessor($orderProcessor->reveal(), $requestStack->reveal());

        $order = $this->prophesize(OrderInterface::class);

        $orderProcessor->process($order)->shouldBeCalled();

        $processor->process($order->reveal());
    }
}
