<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Preparer\OrderPreparer;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class OrderPreparerTest extends TestCase
{
    use ProphecyTrait;

    public function testItProvidesOrderAndCancelsItsStock(): void
    {
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $inventoryOperator = $this->prophesize(OrderInventoryOperatorInterface::class);

        $provider = new OrderPreparer(
            $orderRepository->reveal(),
            $inventoryOperator->reveal(),
        );

        $order = new Order();
        $orderRepository->find(1)->willReturn($order);
        $inventoryOperator->cancel($order)->shouldBeCalled();

        self::assertSame($order, $provider->prepareToUpdate(1));
    }

    public function testItThrowsExceptionIfOrderWithGivenIdDoesNotExist(): void
    {
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $inventoryOperator = $this->prophesize(OrderInventoryOperatorInterface::class);

        $provider = new OrderPreparer(
            $orderRepository->reveal(),
            $inventoryOperator->reveal(),
        );

        $orderRepository->find(1)->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        $provider->prepareToUpdate(1);
    }
}
