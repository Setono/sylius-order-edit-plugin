<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\EventSubscriber;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Bundle\AdminBundle\Menu\OrderShowMenuBuilder;
use Sylius\Component\Core\OrderShippingStates;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OrderShowMenuSubscriber implements EventSubscriberInterface
{
    private const MENU_ITEM_KEY = 'edit';

    public static function getSubscribedEvents(): array
    {
        /** @psalm-suppress DeprecatedClass */
        return [OrderShowMenuBuilder::EVENT_NAME => 'addEditButton'];
    }

    public function addEditButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        if ($order->getShippingState() === OrderShippingStates::STATE_SHIPPED) {
            return;
        }

        $menu
            ->addChild(
                self::MENU_ITEM_KEY,
                [
                    'route' => 'sylius_admin_order_update',
                    'routeParameters' => ['id' => $order->getId()],
                ],
            )
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.edit')
            ->setLabelAttribute('icon', 'edit')
            ->setLabelAttribute('color', 'purple')
        ;

        $sort = array_keys($menu->getChildren());
        array_unshift($sort, self::MENU_ITEM_KEY);

        try {
            $event->getMenu()->reorderChildren($sort);
        } catch (\InvalidArgumentException) {
        }
    }
}
