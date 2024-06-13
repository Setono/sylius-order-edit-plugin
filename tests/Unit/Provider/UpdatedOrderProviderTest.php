<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
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

        $formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius'])->willReturn($form);
        $form->handleRequest($request)->shouldBeCalled()->willReturn($form);
        $form->getData()->willReturn($newOrder);

        self::assertSame($newOrder, $provider->fromRequest($order, $request));
    }
}
