<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Setono\SyliusOrderEditPlugin\Tests\Application\Entity\Order;

final class EditableOrderTraitTest extends TestCase
{
    public function testItAllowsSettingInitialTotal(): void
    {
        $order = new Order();
        $order->setInitialTotal(1000);

        self::assertSame(1000, $order->getInitialTotal());
    }

    public function testItAllowsSettingStoreNotesAndNullingThem(): void
    {
        $order = new Order();
        $order->setStoreNotes('Store notes');

        self::assertSame('Store notes', $order->getStoreNotes());

        $order->setStoreNotes(null);
        self::assertNull($order->getStoreNotes());
    }
}
