<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Setter;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdderInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Setono\SyliusOrderEditPlugin\Setter\OrderDiscountAdjustmentSetter;
use Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderDiscountAdjustmentSetterTest extends TestCase
{
    use ProphecyTrait;

    public function testItDistributesDiscountsOnOrderItemUnits(): void
    {
        $minimumPriceDistributor = $this->prophesize(MinimumPriceDistributorInterface::class);
        $orderItemDiscountAdjustmentAdder = $this->prophesize(DiscountAdjustmentsAdderInterface::class);

        $setter = new OrderDiscountAdjustmentSetter(
            $minimumPriceDistributor->reveal(),
            $orderItemDiscountAdjustmentAdder->reveal(),
        );

        $order = $this->prophesize(EditableOrderInterface::class);
        $firstItem = $this->prophesize(OrderItemInterface::class);
        $secondItem = $this->prophesize(OrderItemInterface::class);
        $channel = $this->prophesize(ChannelInterface::class);
        $order->getItems()->willReturn(new ArrayCollection([$firstItem->reveal(), $secondItem->reveal()]));
        $order->getChannel()->willReturn($channel->reveal());

        $minimumPriceDistributor
            ->distribute([$firstItem->reveal(), $secondItem->reveal()], 1000, $channel->reveal(), true)
            ->willReturn([400, 600])
        ;

        $orderItemDiscountAdjustmentAdder
            ->add($firstItem->reveal(), AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, -400)
            ->shouldBeCalled()
        ;

        $orderItemDiscountAdjustmentAdder
            ->add($secondItem->reveal(), AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, -600)
            ->shouldBeCalled()
        ;

        $setter->set($order->reveal(), 1000);
    }
}
