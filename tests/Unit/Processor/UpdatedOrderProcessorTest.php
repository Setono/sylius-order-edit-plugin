<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Processor;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessor;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class UpdatedOrderProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItProcessesAndUpdatedTheOrder(): void
    {
        $orderProcessor = $this->prophesize(OrderProcessorInterface::class);
        $orderInventoryOperator = $this->prophesize(OrderInventoryOperatorInterface::class);
        $afterCheckoutOrderPaymentProcessor = $this->prophesize(OrderProcessorInterface::class);

        $processor = new UpdatedOrderProcessor(
            $orderProcessor->reveal(),
            $orderInventoryOperator->reveal(),
            $afterCheckoutOrderPaymentProcessor->reveal(),
        );

        $newOrder = $this->prophesize(OrderInterface::class);

        $newOrder->setState('cart')->shouldBeCalled();
        $orderProcessor->process($newOrder)->shouldBeCalled();
        $afterCheckoutOrderPaymentProcessor->process($newOrder)->shouldBeCalled();
        $newOrder->setState('new')->shouldBeCalled();
        $orderInventoryOperator->hold($newOrder)->shouldBeCalled();

        $processor->process($newOrder->reveal());
    }
}
