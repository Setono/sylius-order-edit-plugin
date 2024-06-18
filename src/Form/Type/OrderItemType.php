<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            /** @var OrderItemInterface|null $orderItem */
            $orderItem = $event->getData();
            if ($orderItem === null) {
                return;
            }

            /** @var OrderInterface $order */
            $order = $orderItem->getOrder();

            $form->add('discounts', OrderItemDiscountCollectionType::class, [
                'property_path' => 'adjustments',
                'entry_options' => [
                    'currency' => $order->getCurrencyCode(),
                ],
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            /** @var array $orderItem */
            $orderItem = $event->getData();
            if (!isset($orderItem['discounts'])) {
                $orderItem['discounts'] = [];
            }
            $event->setData($orderItem);
        });
    }
}
