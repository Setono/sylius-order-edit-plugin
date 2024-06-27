<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Adder;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Adder\DiscountAdjustmentsAdder;
use Setono\SyliusOrderEditPlugin\Model\AdjustmentTypes;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;

final class DiscountAdjustmentsAdderTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsDiscountAdjustmentsOnOrderItemUnits(): void
    {
        $integerDistributor = $this->prophesize(IntegerDistributorInterface::class);
        $adjustmentFactory = $this->prophesize(AdjustmentFactoryInterface::class);

        $item = $this->prophesize(OrderItemInterface::class);
        $firstUnit = $this->prophesize(OrderItemUnitInterface::class);
        $secondUnit = $this->prophesize(OrderItemUnitInterface::class);
        $thirdUnit = $this->prophesize(OrderItemUnitInterface::class);

        $adder = new DiscountAdjustmentsAdder(
            $integerDistributor->reveal(),
            $adjustmentFactory->reveal(),
        );

        $item->getQuantity()->willReturn(3);
        $item->getUnits()->willReturn(new ArrayCollection(
            [$firstUnit->reveal(), $secondUnit->reveal(), $thirdUnit->reveal()],
        ));

        $integerDistributor->distribute(-1000, 3)->willReturn([-333, -333, -334]);

        $firstAdjustment = $this->prophesize(AdjustmentInterface::class);
        $secondAdjustment = $this->prophesize(AdjustmentInterface::class);
        $thirdAdjustment = $this->prophesize(AdjustmentInterface::class);

        $adjustmentFactory
            ->createWithData(AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, 'Label', -333)
            ->willReturn($firstAdjustment->reveal(), $secondAdjustment->reveal())
        ;
        $adjustmentFactory
            ->createWithData(AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, 'Label', -334, )
            ->willReturn($thirdAdjustment->reveal())
        ;

        $firstAdjustment->setOriginCode('ORIGIN_CODE')->shouldBeCalled();
        $secondAdjustment->setOriginCode('ORIGIN_CODE')->shouldBeCalled();
        $thirdAdjustment->setOriginCode('ORIGIN_CODE')->shouldBeCalled();

        $firstUnit->addAdjustment($firstAdjustment->reveal())->shouldBeCalled();
        $secondUnit->addAdjustment($secondAdjustment->reveal())->shouldBeCalled();
        $thirdUnit->addAdjustment($thirdAdjustment->reveal())->shouldBeCalled();

        $adder->add($item->reveal(), AdjustmentTypes::SETONO_ADMIN_ORDER_DISCOUNT, 'ORIGIN_CODE', 'Label', -1000);
    }
}
