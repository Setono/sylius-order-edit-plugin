<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\EventSubscriber;

use Sylius\Component\Grid\Definition\Action;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverter;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AddEditOrderActionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // The grid name is found in this file: vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Resources/config/grids/order.yml
        $eventName = sprintf(ArrayToDefinitionConverter::EVENT_NAME, 'admin_order');

        return [$eventName => 'add'];
    }

    public function add(GridDefinitionConverterEvent $event): void
    {
        try {
            $actionGroup = $event->getGrid()->getActionGroup('item');
        } catch (\InvalidArgumentException) {
            return;
        }

        $action = Action::fromNameAndType('edit_order', 'edit_order');
        $action->setOptions([
            'link' => [
                'route' => 'sylius_admin_order_update',
                'parameters' => ['id' => 'resource.id'],
            ],
        ]);
        $actionGroup->addAction($action);
    }
}
