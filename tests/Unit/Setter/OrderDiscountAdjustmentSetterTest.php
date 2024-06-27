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
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class OrderDiscountAdjustmentSetterTest extends TestCase
{
    use ProphecyTrait;

    public function testItDistributesDiscountsOnOrderItemUnits(): void
    {
        $integerDistributor = $this->prophesize(IntegerDistributorInterface::class);
        $orderItemDiscountAdjustmentAdder = $this->prophesize(DiscountAdjustmentsAdderInterface::class);

        $setter = new OrderDiscountAdjustmentSetter(
            $integerDistributor->reveal(),
            $orderItemDiscountAdjustmentAdder->reveal(),
        );

        $order = $this->prophesize(EditableOrderInterface::class);
        $firstItem = $this->prophesize(OrderItemInterface::class);
        $secondItem = $this->prophesize(OrderItemInterface::class);
        $order->getItems()->willReturn(new ArrayCollection([$firstItem->reveal(), $secondItem->reveal()]));
        $order->getId()->willReturn(100);

        $integerDistributor
            ->distribute(1000, 2)
            ->willReturn([500, 500])
        ;

        $orderItemDiscountAdjustmentAdder
            ->add($firstItem->reveal(), AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT . '_100', 'Custom order discount', -500)
            ->shouldBeCalled()
        ;

        $orderItemDiscountAdjustmentAdder
            ->add($secondItem->reveal(), AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT . '_100', 'Custom order discount', -500)
            ->shouldBeCalled()
        ;

        $setter->set($order->reveal(), 1000);
    }
}
