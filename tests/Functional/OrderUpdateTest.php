<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderUpdateTest extends WebTestCase
{
    private static KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        self::ensureKernelShutdown();
        self::$client = static::createClient(['environment' => 'test', 'debug' => true]);

        $this->makeVariantTrackedWithStockAndPrice('000F_office_grey_jeans-variant-0');
        $this->makeVariantTrackedWithStockAndPrice('111F_patched_jeans_with_fancy_badges-variant-0');
    }

    public function testItAllowsToChangeItemQuantity(): void
    {
        $order = $this->placeOrderProgramatically(quantity: 5);

        /** @var ProductVariantInterface $variant */
        $variant = $this->getVariantRepository()->findOneBy(['code' => '000F_office_grey_jeans-variant-0']);
        $initialHold = $variant->getOnHold();

        $this->loginAsAdmin();
        $this->changeOrderItemQuantity($order->getId());

        self::assertResponseStatusCodeSame(302);

        $order = $this->getOrderRepository()->findOneBy(['tokenValue' => 'TOKEN']);
        self::assertSame(3, $order->getItems()->first()->getQuantity());

        $variant = $this->getVariantRepository()->findOneBy(['code' => '000F_office_grey_jeans-variant-0']);
        self::assertSame($initialHold - 2, $variant->getOnHold());
    }

    public function testItAllowsToAddAndRemoveOrderItems(): void
    {
        $order = $this->placeOrderProgramatically(quantity: 5);

        /** @var ProductVariantInterface $variant */
        $variant = $this->getVariantRepository()->findOneBy(['code' => '000F_office_grey_jeans-variant-0']);
        $initialHold = $variant->getOnHold();

        $this->loginAsAdmin();

        /** @var ProductVariantInterface $newVariant */
        $newVariant = $this->getVariantRepository()->findOneBy(['code' => '111F_patched_jeans_with_fancy_badges-variant-0']);
        $this->manipulateOrderItems($order->getId(), $newVariant->getId(), 2);

        self::assertResponseStatusCodeSame(302);

        $order = $this->getOrderRepository()->findOneBy(['tokenValue' => 'TOKEN']);
        self::assertSame(2, $order->getItems()->first()->getQuantity());
        self::assertSame($newVariant->getId(), $order->getItems()->first()->getVariant()->getId());

        $oldVariant = $this->getVariantRepository()->findOneBy(['code' => '000F_office_grey_jeans-variant-0']);
        self::assertSame($initialHold - 5, $oldVariant->getOnHold());
        $newVariant = $this->getVariantRepository()->findOneBy(['code' => '111F_patched_jeans_with_fancy_badges-variant-0']);
        self::assertSame(2, $newVariant->getOnHold());
    }

    public function testItDoesNotAllowToExceedTheInitialOrderTotal(): void
    {
        $this->makeVariantTrackedWithStockAndPrice('111F_patched_jeans_with_fancy_badges-variant-0', 100);
        $order = $this->placeOrderProgramatically(quantity: 1);

        /** @var ProductVariantInterface $variant */
        $variant = $this->getVariantRepository()->findOneBy(['code' => '000F_office_grey_jeans-variant-0']);

        $this->loginAsAdmin();

        /** @var ProductVariantInterface $newVariant */
        $newVariant = $this->getVariantRepository()->findOneBy(['code' => '111F_patched_jeans_with_fancy_badges-variant-0']);
        $this->manipulateOrderItems($order->getId(), $newVariant->getId(), 90);

        self::assertResponseStatusCodeSame(302);

        $this->getEntityManager()->clear();

        $order = $this->getOrderRepository()->findOneBy(['tokenValue' => 'TOKEN']);
        self::assertSame(1, $order->getItems()->first()->getQuantity());
        self::assertSame($variant->getId(), $order->getItems()->first()->getVariant()->getId());
    }

    private function placeOrderProgramatically(
        string $variantCode = '000F_office_grey_jeans-variant-0',
        int $quantity = 1,
    ): Order {
        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('sylius.command_bus');

        $pickupCart = new PickupCart('TOKEN');
        $pickupCart->setLocaleCode('en_US');
        $pickupCart->setChannelCode('FASHION_WEB');
        $pickupCart->setEmail('shop@example.com');
        $commandBus->dispatch($pickupCart);

        $orderRepository = self::getContainer()->get('sylius.repository.order');
        /** @var OrderInterface $order */
        $order = $orderRepository->findOneBy(['tokenValue' => 'TOKEN']);

        $addToCartCommand = new AddItemToCart($variantCode, $quantity);
        $addToCartCommand->setOrderTokenValue('TOKEN');
        $commandBus->dispatch($addToCartCommand);

        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCountryCode('US');
        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $addressCart = new UpdateCart(billingAddress: $address, shippingAddress: $address);
        $addressCart->setOrderTokenValue('TOKEN');
        $commandBus->dispatch($addressCart);

        $chooseShippingMethod = new ChooseShippingMethod('dhl_express');
        $chooseShippingMethod->setOrderTokenValue('TOKEN');
        $chooseShippingMethod->setSubresourceId((string) $order->getShipments()->first()->getId());
        $commandBus->dispatch($chooseShippingMethod);

        $choosePaymentMethod = new ChoosePaymentMethod('bank_transfer');
        $choosePaymentMethod->setOrderTokenValue('TOKEN');
        $choosePaymentMethod->setSubresourceId((string) $order->getPayments()->first()->getId());
        $commandBus->dispatch($choosePaymentMethod);

        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('TOKEN');
        $commandBus->dispatch($completeOrder);

        return $this->getOrderRepository()->findOneBy(['tokenValue' => 'TOKEN']);
    }

    private function makeVariantTrackedWithStockAndPrice(
        string $code = '000F_office_grey_jeans-variant-0',
        int $stock = 10,
        int $price = 1000,
    ): void {
        $variantRepository = self::getContainer()->get('sylius.repository.product_variant');
        /** @var ProductVariantInterface $variant */
        $variant = $variantRepository->findOneBy(['code' => $code]);
        $variant->setTracked(true);
        $variant->setOnHand($stock);
        $variant->setOnHold(0);
        foreach ($variant->getChannelPricings() as $channelPricing) {
            $channelPricing->setPrice($price);
        }

        $this->getEntityManager()->flush();
    }

    protected function tearDown(): void
    {
        $order = $this->getOrderRepository()->findOneBy(['tokenValue' => 'TOKEN']);
        $orderManager = self::getContainer()->get('sylius.manager.order');
        $orderManager->remove($order);
        $orderManager->flush();
    }

    private function loginAsAdmin(): void
    {
        $crawler = static::$client->request('GET', '/admin/login');
        $form = $crawler->selectButton('Login')->form([
            '_username' => 'sylius@example.com',
            '_password' => 'sylius',
        ]);

        static::$client->submit($form);
    }

    private function changeOrderItemQuantity(int $orderId): void
    {
        static::$client->request(
            'PATCH',
            sprintf('/admin/orders/%d/update-and-process', $orderId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'sylius_order' => [
                    'items' => [
                        ['quantity' => 3],
                    ],
                ],
            ]),
        );
    }

    private function manipulateOrderItems(int $orderId, int $newItemVariantId, int $newItemQuantity): void
    {
        static::$client->request(
            'PATCH',
            sprintf('/admin/orders/%d/update-and-process', $orderId),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'sylius_order' => [
                    'items' => [
                        ['quantity' => $newItemQuantity, 'variant' => $newItemVariantId],
                    ],
                ],
            ]),
        );
    }

    private function getOrderRepository(): OrderRepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.order');
    }

    private function getVariantRepository(): ProductVariantRepositoryInterface
    {
        return self::getContainer()->get('sylius.repository.product_variant');
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine.orm.entity_manager');
    }
}
