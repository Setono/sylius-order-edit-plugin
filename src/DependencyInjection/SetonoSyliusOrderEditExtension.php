<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusOrderEditExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('winzou_state_machine', [
            'sylius_order' => [
                'transitions' => [
                    'edit' => [
                        'from' => ['new'],
                        'to' => 'cart',
                    ],
                ],
                'callbacks' => [
                    'after' => [
                        'setono_sylius_order_edit.edit_shipping' => [
                            'on' => ['edit'],
                            'do' => ['@sm.callback.cascade_transition', 'apply'],
                            'args' => ['object', 'event', "'edit'", "'sylius_order_shipping'"],
                        ],
                    ],
                ],
            ],
            'sylius_order_shipping' => [
                'transitions' => [
                    'edit' => [
                        'from' => ['ready'],
                        'to' => 'cart',
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('sylius_ui', [
            'events' => [
                'sylius.admin.order.update.content' => [
                    'blocks' => [
                        'javascripts' => [
                            'template' => '@SetonoSyliusOrderEditPlugin/admin/order/update/_javascripts.html.twig',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
