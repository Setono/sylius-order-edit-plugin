<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;

final class OrderItemDiscountType extends AbstractType
{
    public function getParent(): string
    {
        return MoneyType::class;
    }
}
