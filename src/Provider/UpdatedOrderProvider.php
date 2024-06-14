<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Setono\SyliusOrderEditPlugin\Exception\OrderUpdateException;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class UpdatedOrderProvider implements UpdatedOrderProviderInterface
{
    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    public function provideFromOldOrderAndRequest(OrderInterface $oldOrder, Request $request): OrderInterface
    {
        $form = $this->formFactory->create(OrderType::class, $oldOrder, ['validation_groups' => 'sylius']);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new OrderUpdateException();
        }

        $data = $form->getData();
        Assert::isInstanceOf($data, OrderInterface::class);

        return $data;
    }
}
