<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EditOrderAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private OrderProcessorInterface $orderProcessor,
        private OrderProcessorInterface $afterCheckoutOrderPaymentProcessor,
        private OrderInventoryOperatorInterface $orderInventoryOperator,
        private OrderRepositoryInterface $orderRepository,
        private UrlGeneratorInterface $router,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $orderId = (int) $request->attributes->get('id');
        $order = $this->orderRepository->find($orderId);
        $initialTotal = $order->getTotal();
        $this->orderInventoryOperator->cancel($order);

        $form = $this->formFactory->create(OrderType::class, $order, ['validation_groups' => 'sylius_shipping_address_update']);

        $form->handleRequest($request);

        /** @var OrderInterface $resource */
        $resource = $form->getData();
        $newTotal = $resource->getTotal();

        $resource->setState('cart');

        $this->orderProcessor->process($resource);
        $this->afterCheckoutOrderPaymentProcessor->process($resource);

        $resource->setState('new');
        $this->orderInventoryOperator->hold($resource);

        if ($newTotal > $initialTotal) {
            $session = $this->requestStack->getSession();
            $session->getBag('flashes')->add('error', 'Order should not have bigger total than before editing');

            return new RedirectResponse($this->router->generate('sylius_admin_order_update', ['id' => $orderId]));
        }

        $this->entityManager->flush();

        $session = $this->requestStack->getSession();
        $session->getBag('flashes')->add('success', 'Order has been successfully updated');

        return new RedirectResponse($this->router->generate('sylius_admin_order_show', ['id' => $orderId]));
    }
}
