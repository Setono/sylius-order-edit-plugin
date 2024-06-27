<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('variant', ResourceAutocompleteChoiceType::class, [
                'label' => false,
                'multiple' => false,
                'required' => true,
                'choice_name' => 'descriptor',
                'choice_value' => 'id',
                'resource' => 'sylius.product_variant',
            ])
        ;

        /** @var string $currencyCode */
        $currencyCode = $options['currency_code'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currencyCode): void {
            $form = $event->getForm();

            $form->add('discounts', OrderItemDiscountCollectionType::class, [
                'property_path' => 'adjustments',
                'entry_options' => [
                    'currency' => $currencyCode,
                ],
                'button_add_label' => 'setono_sylius_order_edit.ui.add_discount',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('currency_code');
        $resolver->setAllowedTypes('currency_code', 'string');
    }
}
