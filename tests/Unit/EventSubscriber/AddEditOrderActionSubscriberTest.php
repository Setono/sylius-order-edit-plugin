<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\EventSubscriber\AddEditOrderActionSubscriber;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Grid\Definition\ActionGroup;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

final class AddEditOrderActionSubscriberTest extends TestCase
{
    use ProphecyTrait;

    public function testItAddsEditOrderAction(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('sylius_order', 'doctrine/orm', ['class' => Order::class]);
        $grid->addActionGroup(ActionGroup::named('item'));
        $event = new GridDefinitionConverterEvent($grid);

        (new AddEditOrderActionSubscriber())->add($event);

        self::assertNotNull($grid->getActionGroup('item')->getAction('edit_order'));
        $action = $grid->getActionGroup('item')->getAction('edit_order');
        self::assertSame('edit_order', $action->getType());
        self::assertSame([
            'link' => [
                'route' => 'sylius_admin_order_update',
                'parameters' => ['id' => 'resource.id'],
            ],
        ], $action->getOptions());
    }

    public function testItDoesNothingIfThereIsNoItemActionGroup(): void
    {
        $grid = Grid::fromCodeAndDriverConfiguration('sylius_order', 'doctrine/orm', ['class' => Order::class]);
        $event = new GridDefinitionConverterEvent($grid);

        (new AddEditOrderActionSubscriber())->add($event);

        self::expectException(\InvalidArgumentException::class);
        $grid->getActionGroup('item');
    }
}
