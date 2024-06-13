<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Extension;

use Setono\SyliusOrderEditPlugin\Form\Type\OrderItemCollectionType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class OrderTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('items', OrderItemCollectionType::class);
    }

    public static function getExtendedTypes(): \Generator
    {
        yield OrderType::class;
    }
}
