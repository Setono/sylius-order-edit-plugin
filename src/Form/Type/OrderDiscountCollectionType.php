<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Component\Core\Model\Adjustment;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderDiscountCollectionType extends AbstractType
{
    private const CUSTOM_ORDER_DISCOUNT = 'custom_order_discount';

    public function getParent()
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => OrderDiscountType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'sylius.form.order.discounts',
            'entry_options' => [
                'label' => false,
                'currency' => 'USD',
            ],
            'getter' => function (OrderInterface &$order): array {
                $adjustments = $order->getAdjustments(self::CUSTOM_ORDER_DISCOUNT)->toArray();

                return array_map(function (AdjustmentInterface $adjustment): int {
                    return -1 * $adjustment->getAmount();
                }, $adjustments);
            },
            'setter' => function (OrderInterface &$order, array $discounts): void {
                $order->removeAdjustments(self::CUSTOM_ORDER_DISCOUNT);

                foreach ($discounts as $discount) {
                    $adjustment = new Adjustment();
                    $adjustment->setType(self::CUSTOM_ORDER_DISCOUNT);
                    $adjustment->setAmount(-1 * $discount);
                    $order->addAdjustment($adjustment);
                }
            },
        ]);
    }
}
