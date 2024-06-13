<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class OrderItemType extends AbstractResourceType
{
    /**
     * @param list<string> $validationGroups
     */
    public function __construct(
        private readonly OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'setter' => function (OrderItemInterface &$orderItem, int $quantity): void {
                    $this->orderItemQuantityModifier->modify($orderItem, $quantity);
                },
            ])
            // TODO: change to autocomplete type for product variant
            ->add('variant', EntityType::class, [
                'class' => ProductVariant::class,
                'choice_label' => 'code',
            ])
        ;
    }
}
