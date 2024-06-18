<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\OrderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\OrderProcessing\OrderInitialTotalProcessor;

final class OrderInitialTotalProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testItSetsInitialTotalAfterOrderIsCompleted(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $processor = new OrderInitialTotalProcessor($entityManager->reveal());
        $order = $this->prophesize(EditableOrderInterface::class);

        $order->getTotal()->willReturn(1000);
        $order->setInitialTotal(1000)->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $processor->process($order->reveal());
    }
}
