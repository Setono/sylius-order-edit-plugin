<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Model\OrderEditDiscountTypes;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrderItemDiscountCollectionType extends AbstractType
{
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
            'entry_type' => OrderItemDiscountType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'sylius.form.order.discounts',
            'entry_options' => [
                'label' => false,
            ],
            'getter' => function (OrderItemInterface &$orderItem): array {
                $adjustments = $orderItem->getAdjustments(OrderEditDiscountTypes::SETONO_ADMIN_ORDER_ITEM_DISCOUNT)->toArray();

                return array_map(function (AdjustmentInterface $adjustment): int {
                    return -1 * $adjustment->getAmount();
                }, $adjustments);
            },
            'setter' => function (OrderItemInterface &$ordeorderItem, array $discounts): void {
                $ordeorderItem->removeAdjustments(OrderEditDiscountTypes::SETONO_ADMIN_ORDER_ITEM_DISCOUNT);

                /** @var int $discount */
                foreach ($discounts as $discount) {
                    $adjustment = $this->adjustmentFactory->createWithData(
                        OrderEditDiscountTypes::SETONO_ADMIN_ORDER_ITEM_DISCOUNT,
                        'Custom item discount',
                        -1 * $discount,
                    );
                    $ordeorderItem->addAdjustment($adjustment);
                }
            },
        ]);
    }
}
