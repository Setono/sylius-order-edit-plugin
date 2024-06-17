<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\OrderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrder;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Setono\SyliusOrderEditPlugin\OrderProcessing\OrderInitialTotalProcessor;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderInitialTotalProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsInitialTotalAfterOrderIsCompleted(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $processor = new OrderInitialTotalProcessor($entityManager->reveal());
        $order = new class extends Order implements OrderInterface, InitialTotalAwareOrderInterface {
            use InitialTotalAwareOrder;

            public function getTotal(): int
            {
                return 1000;
            }
        };
        $entityManager->flush()->shouldBeCalled();

        $processor->process($order);

        self::assertSame(1000, $order->getInitialTotal());
    }
}
