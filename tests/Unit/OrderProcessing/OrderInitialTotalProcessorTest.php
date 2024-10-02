<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\OrderProcessing;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\OrderProcessing\OrderInitialTotalProcessor;

final class OrderInitialTotalProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsInitialTotalAfterOrderIsCompleted(): void
    {
        $processor = new OrderInitialTotalProcessor();
        $order = $this->prophesize(EditableOrderInterface::class);

        $order->getTotal()->willReturn(1000);
        $order->setInitialTotal(1000)->shouldBeCalled();

        $processor->process($order->reveal());
    }
}
