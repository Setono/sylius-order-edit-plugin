<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOrderEditPlugin\Exception\OrderUpdateException;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProvider;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Model\Order;
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
        $order = new Order();
        $newOrder = new Order();
        $request = new Request();

        $formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $form->getData()->willReturn($newOrder);

        self::assertSame($newOrder, $provider->provideFromOldOrderAndRequest($order, $request));
    }

    public function testItThrowsExceptionIfFormIsNotSubmitted(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = new Order();
        $request = new Request();

        $formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(false);

        $this->expectException(OrderUpdateException::class);

        $provider->provideFromOldOrderAndRequest($order, $request);
    }

    public function testItThrowsExceptionIfFormIsNotValid(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = new Order();
        $request = new Request();

        $formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(false);

        $this->expectException(OrderUpdateException::class);

        $provider->provideFromOldOrderAndRequest($order, $request);
    }

    public function testItThrowsExceptionIfFormDataIsNotOrder(): void
    {
        $formFactory = $this->prophesize(FormFactoryInterface::class);
        $form = $this->prophesize(FormInterface::class);

        $provider = new UpdatedOrderProvider($formFactory->reveal());
        $order = new Order();
        $request = new Request();

        $formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius', 'csrf_protection' => false])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $form->getData()->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $provider->provideFromOldOrderAndRequest($order, $request);
    }
}
