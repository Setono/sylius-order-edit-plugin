<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Setono\SyliusOrderEditPlugin\Model\OrderEditDiscountTypes;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class CustomDiscountCollectionType extends AbstractType
{
    protected string $label;
    protected string $adjustmentType;

    public function __construct(
        protected readonly AdjustmentFactoryInterface $adjustmentFactory,
    ) {
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
            'getter' => function (AdjustableInterface &$adjustable): array {
                $adjustments = $adjustable->getAdjustments($this->adjustmentType)->toArray();

                return array_map(function (AdjustmentInterface $adjustment): int {
                    return -1 * $adjustment->getAmount();
                }, $adjustments);
            },
            'setter' => function (AdjustableInterface &$adjustable, array $discounts): void {
                $adjustable->removeAdjustments($this->adjustmentType);

                /** @var int $discount */
                foreach ($discounts as $discount) {
                    $adjustment = $this->adjustmentFactory->createWithData(
                        $this->adjustmentType,
                        $this->label,
                        -1 * $discount,
                    );
                    $adjustable->addAdjustment($adjustment);
                }
            },
        ]);
    }
}
