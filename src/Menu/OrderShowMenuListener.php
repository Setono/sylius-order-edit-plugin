<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Component\Core\OrderShippingStates;

final class OrderShowMenuListener
{
    public function addEditButton(OrderShowMenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $order = $event->getOrder();

        if ($order->getShippingState() === OrderShippingStates::STATE_SHIPPED) {
            return;
        }

        $menu
            ->addChild(
                'edit',
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

        $menu->reorderChildren(['edit', 'order_history', 'cancel']);
    }
}
