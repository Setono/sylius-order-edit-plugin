<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderDiscountCollectionType extends AbstractType
{
    private const CUSTOM_ORDER_DISCOUNT = 'custom_order_discount';

    public function __construct(private readonly AdjustmentFactoryInterface $adjustmentFactory)
    {
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => OrderDiscountType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'sylius.form.order.discounts',
            'entry_options' => [
                'label' => false,
            ],
            'getter' => function (OrderInterface &$order): array {
                $adjustments = $order->getAdjustments(self::CUSTOM_ORDER_DISCOUNT)->toArray();

                return array_map(function (AdjustmentInterface $adjustment): int {
                    return -1 * $adjustment->getAmount();
                }, $adjustments);
            },
            'setter' => function (OrderInterface &$order, array $discounts): void {
                $order->removeAdjustments(self::CUSTOM_ORDER_DISCOUNT);

                /** @var int $discount */
                foreach ($discounts as $discount) {
                    $adjustment = $this->adjustmentFactory->createWithData(
                        self::CUSTOM_ORDER_DISCOUNT,
                        'Custom discount: ' . number_format($discount, 2),
                        -1 * $discount,
                    );
                    $order->addAdjustment($adjustment);
                }
            },
        ]);
    }
}
