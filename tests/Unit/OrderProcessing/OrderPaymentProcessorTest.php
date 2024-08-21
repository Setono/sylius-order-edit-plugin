<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\OrderProcessing;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Checker\OrderPaymentEditionCheckerInterface;
use Setono\SyliusOrderEditPlugin\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPaymentProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItDoesNotAllowToProcessOrderIfItShouldNotBeEdited(): void
    {
        $orderProcessor = $this->prophesize(OrderProcessorInterface::class);
        $orderPaymentEditionChecker = $this->prophesize(OrderPaymentEditionCheckerInterface::class);

        $processor = new OrderPaymentProcessor($orderProcessor->reveal(), $orderPaymentEditionChecker->reveal());

        $order = new Order();

        $orderPaymentEditionChecker->shouldOrderPaymentBeEdited($order)->willReturn(false);
        $orderProcessor->process($order)->shouldNotBeCalled();

        $processor->process($order);
    }

    public function testItDoesNothingIfItShouldBeEdited(): void
    {
        $orderProcessor = $this->prophesize(OrderProcessorInterface::class);
        $orderPaymentEditionChecker = $this->prophesize(OrderPaymentEditionCheckerInterface::class);

        $processor = new OrderPaymentProcessor($orderProcessor->reveal(), $orderPaymentEditionChecker->reveal());

        $order = new Order();

        $orderPaymentEditionChecker->shouldOrderPaymentBeEdited($order)->willReturn(true);
        $orderProcessor->process($order)->shouldBeCalled();

        $processor->process($order);
    }
}
