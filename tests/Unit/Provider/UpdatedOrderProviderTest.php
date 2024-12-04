<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Exception\OrderUpdateException;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProvider;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class UpdatedOrderProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testItProvidesUpdateOrderFromRequest(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = $this->prophesize(EditableOrderInterface::class);
        $newOrder = $this->prophesize(EditableOrderInterface::class);
        $request = new Request();

        $formFactory->create(OrderType::class, $order->reveal(), ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $form->getData()->willReturn($newOrder->reveal());

        self::assertSame($newOrder->reveal(), $provider->provideFromOldOrderAndRequest($order->reveal(), $request));
    }

    public function testItThrowsExceptionIfFormIsNotSubmitted(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = $this->prophesize(EditableOrderInterface::class);
        $request = new Request();

        $formFactory->create(OrderType::class, $order->reveal(), ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $errors = new FormErrorIterator($form->reveal(), [new FormError('error')]);
        $form->isSubmitted()->willReturn(false);
        $form->getErrors(true)->willReturn($errors);

        $this->expectException(OrderUpdateException::class);

        $provider->provideFromOldOrderAndRequest($order->reveal(), $request);
    }

    public function testItThrowsExceptionIfFormIsNotValid(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = $this->prophesize(EditableOrderInterface::class);
        $request = new Request();

        $formFactory->create(OrderType::class, $order->reveal(), ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $errors = new FormErrorIterator($form->reveal(), [new FormError('error')]);
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(false);
        $form->getErrors(true)->willReturn($errors);

        $this->expectException(OrderUpdateException::class);

        $provider->provideFromOldOrderAndRequest($order->reveal(), $request);
    }

    public function testItThrowsExceptionIfFormDataIsNotOrder(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = $this->prophesize(EditableOrderInterface::class);
        $request = new Request();

        $formFactory->create(OrderType::class, $order->reveal(), ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $form->getData()->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $provider->provideFromOldOrderAndRequest($order->reveal(), $request);
    }
}
