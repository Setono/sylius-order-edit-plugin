<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class UpdatedOrderProvider
{
    public function __construct(private FormFactoryInterface $formFactory)
    {
    }

    public function fromRequest(OrderInterface $order, Request $request): OrderInterface
    {
        $form = $this->formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius']);
        $form->handleRequest($request);

        $data = $form->getData();
        Assert::isInstanceOf($data, OrderInterface::class);

        return $data;
    }
}
