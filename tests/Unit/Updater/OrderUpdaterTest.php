<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Updater;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Checker\PostUpdateChangesCheckerInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Preparer\OrderPreparerInterface;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessorInterface;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProviderInterface;
use Setono\SyliusOrderEditPlugin\Updated\OrderUpdater;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderUpdaterTest extends TestCase
{
    use ProphecyTrait;

    public function testItUpdatesOrder(): void
    {
        $request = $this->prophesize(Request::class);
        $orderPreparer = $this->prophesize(OrderPreparerInterface::class);
        $updatedOrderProvider = $this->prophesize(UpdatedOrderProviderInterface::class);
        $updatedOrderProcessor = $this->prophesize(UpdatedOrderProcessorInterface::class);
        $postUpdateChangesChecker = $this->prophesize(PostUpdateChangesCheckerInterface::class);
        $entityManager = $this->prophesize(EntityManagerInterface::class);

        $orderUpdater = new OrderUpdater(
            $orderPreparer->reveal(),
            $updatedOrderProvider->reveal(),
            $updatedOrderProcessor->reveal(),
            $postUpdateChangesChecker->reveal(),
            $entityManager->reveal(),
        );

        $order = $this->prophesize(EditableOrderInterface::class);
        $updatedOrder = $this->prophesize(OrderInterface::class);

        $orderPreparer->prepareToUpdate(1)->willReturn($order);
        $updatedOrderProvider->provideFromOldOrderAndRequest($order->reveal(), $request)->willReturn($updatedOrder);
        $postUpdateChangesChecker->check(Argument::type(OrderInterface::class), $updatedOrder->reveal())->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $orderUpdater->update($request->reveal(), 1);
    }
}
