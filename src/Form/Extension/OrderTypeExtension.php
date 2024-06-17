<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Extension;

use Setono\SyliusOrderEditPlugin\Form\Type\OrderDiscountCollectionType;
use Setono\SyliusOrderEditPlugin\Form\Type\OrderItemCollectionType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
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
            ->add('discounts', OrderDiscountCollectionType::class, ['property_path' => 'adjustments'])
        ;

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
