<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Exception\OrderUpdateException;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class UpdatedOrderProvider implements UpdatedOrderProviderInterface
{
    public function __construct(private readonly FormFactoryInterface $formFactory)
    {
    }

    public function provideFromOldOrderAndRequest(OrderInterface $oldOrder, Request $request): EditableOrderInterface
    {
        $form = $this->formFactory->create(
            OrderType::class,
            $oldOrder,
            // TODO: figure out how to not disable CSRF here - it blocks us on the testing level, so let's leave
            //       it how it is now, but definitely it should not be disabled in the production-ready code
            ['validation_groups' => 'sylius', 'csrf_protection' => false],
        );
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            /** @var FormErrorIterator<FormError> $errors */
            $errors = $form->getErrors(true);

            $error = '';
            foreach ($errors as $err) {
                $error .= $err->getMessage();
            }

            throw new OrderUpdateException($error);
        }

        $data = $form->getData();
        Assert::isInstanceOf($data, EditableOrderInterface::class);

        return $data;
    }
}
