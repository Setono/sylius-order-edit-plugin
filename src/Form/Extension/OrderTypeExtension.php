<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Extension;

use Setono\SyliusOrderEditPlugin\Form\Type\OrderDiscountCollectionType;
use Setono\SyliusOrderEditPlugin\Form\Type\OrderItemCollectionType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class OrderTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', OrderItemCollectionType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            /** @var OrderInterface $order */
            $order = $event->getData();

            $form->add('discounts', OrderDiscountCollectionType::class, [
                'property_path' => 'adjustments',
                'entry_options' => [
                    'currency' => $order->getCurrencyCode(),
                ],
                'button_add_label' => 'setono_sylius_order_edit.ui.add_discount',
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            /** @var array $order */
            $order = $event->getData();
            if (!isset($order['discounts'])) {
                $order['discounts'] = [];
            }
            $event->setData($order);
        });
    }

    public static function getExtendedTypes(): \Generator
    {
        yield OrderType::class;
    }
}
