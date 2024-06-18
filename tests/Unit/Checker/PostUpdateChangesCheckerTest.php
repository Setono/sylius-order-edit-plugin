<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Checker;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Checker\PostUpdateChangesChecker;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Sylius\Component\Core\Model\OrderInterface;

final class PostUpdateChangesCheckerTest extends TestCase
{
    use ProphecyTrait;

    public function testItThrowsExceptionIfNewOrderTotalIsBiggerThanThePreviousOne(): void
    {
        $this->expectException(NewOrderWrongTotalException::class);

        $validator = new PostUpdateChangesChecker();

        $newOrder = $this->prophesize(OrderInterface::class);
        $newOrder->getTotal()->willReturn(1000);

        $previousOrder = $this->prophesize(InitialTotalAwareOrderInterface::class);
        $previousOrder->getInitialTotal()->willReturn(500);

        $validator->check($previousOrder->reveal(), $newOrder->reveal());
    }

    public function testItDoesNothingIfNewOrderTotalIsSmallerThanThePreviousOne(): void
    {
        $validator = new PostUpdateChangesChecker();

        $newOrder = $this->prophesize(OrderInterface::class);
        $newOrder->getTotal()->willReturn(500);

        $previousOrder = $this->prophesize(InitialTotalAwareOrderInterface::class);
        $previousOrder->getInitialTotal()->willReturn(1000);

        $this->expectNotToPerformAssertions();

        $validator->check($previousOrder->reveal(), $newOrder->reveal());
    }

    public function testItDoesNothingIfNewOrderTotalIsEqualToThePreviousOne(): void
    {
        $validator = new PostUpdateChangesChecker();

        $newOrder = $this->prophesize(OrderInterface::class);
        $newOrder->getTotal()->willReturn(500);

        $previousOrder = $this->prophesize(InitialTotalAwareOrderInterface::class);
        $previousOrder->getInitialTotal()->willReturn(500);

        $this->expectNotToPerformAssertions();

        $validator->check($previousOrder->reveal(), $newOrder->reveal());
    }
}
