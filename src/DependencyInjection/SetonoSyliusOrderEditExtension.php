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
                'callbacks' => [
                    'after' => [
                        'setono_sylius_order_edit.set_initial_total' => [
                            'on' => ['create'],
                            'do' => [
                                '@setono_sylius_order_edit.order_processing.order_initial_total_processor',
                                'process',
                            ],
                            'args' => ['object'],
                        ],
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

        $container->prependExtensionConfig('sylius_grid', [
            'templates' => [
                'action' => [
                    'edit_order' => '@SetonoSyliusOrderEditPlugin/admin/order/grid/editOrder.html.twig',
                ],
            ],
        ]);

        $container->prependExtensionConfig('framework', [
            'messenger' => [
                'buses' => [
                    'setono_sylius_order_edit.event_bus' => [
                        'default_middleware' => 'allow_no_handlers',
                    ],
                ],
            ],
        ]);
    }
}
