<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessor;
use Setono\SyliusOrderEditPlugin\Provider\OldOrderProvider;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EditOrderAction
{
    public function __construct(
        private OldOrderProvider $oldOrderProvider,
        private UrlGeneratorInterface $router,
        private RequestStack $requestStack,
        private UpdatedOrderProvider $updatedOrderProvider,
        private UpdatedOrderProcessor $updatedOrderProcessor,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $orderId = (int) $request->attributes->get('id');
        $order = $this->oldOrderProvider->provide($orderId);

        $initialTotal = $order->getTotal();
        $resource = $this->updatedOrderProvider->fromRequest($order, $request);

        try {
            $this->updatedOrderProcessor->process($initialTotal, $resource);
        } catch (NewOrderWrongTotalException) {
            return $this->addFlashAndRedirect(
                'error',
                'setono_sylius_order_edit.error.order_update',
                'sylius_admin_order_update',
                $orderId,
            );
        }

        return $this->addFlashAndRedirect(
            'success',
            'setono_sylius_order_edit.success.order_update',
            'sylius_admin_order_show',
            $orderId,
        );
    }

    private function addFlashAndRedirect(
        string $type,
        string $message,
        string $route,
        int $orderId,
    ): RedirectResponse {
        $session = $this->requestStack->getSession();
        $session->getBag('flashes')->add($type, $message);

        return new RedirectResponse($this->router->generate($route, ['id' => $orderId]));
    }
}
