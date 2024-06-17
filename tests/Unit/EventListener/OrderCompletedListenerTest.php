<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrder;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Setono\SyliusOrderEditPlugin\EventListener\OrderCompletedListener;
use Sylius\Bundle\ApiBundle\Event\OrderCompleted;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class OrderCompletedListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsInitialTotalAfterOrderIsCompleted(): void
    {
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $listener = new OrderCompletedListener($orderRepository->reveal(), $entityManager->reveal());
        $order = new class extends Order implements OrderInterface, InitialTotalAwareOrderInterface {
            use InitialTotalAwareOrder;

            public function getTotal(): int
            {
                return 1000;
            }
        };
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $entityManager->flush()->shouldBeCalled();

        $listener(new OrderCompleted('TOKEN'));

        self::assertSame(1000, $order->getInitialTotal());
    }

    public function testItDoesNothingIfOrderDoesNotExist(): void
    {
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $listener = new OrderCompletedListener($orderRepository->reveal(), $entityManager->reveal());
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn(null);
        $entityManager->flush()->shouldNotBeCalled();

        $listener(new OrderCompleted('TOKEN'));
    }
}
