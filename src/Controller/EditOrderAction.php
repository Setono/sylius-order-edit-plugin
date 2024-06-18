<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Setono\SyliusOrderEditPlugin\Updated\OrderUpdaterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EditOrderAction
{
    public function __construct(
        private readonly OrderUpdaterInterface $orderUpdater,
        private readonly UrlGeneratorInterface $router,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function __invoke(Request $request, int $id): Response
    {
        try {
            $this->orderUpdater->update($request, $id);
        } catch (NewOrderWrongTotalException) {
            return $this->addFlashAndRedirect(
                'error',
                'setono_sylius_order_edit.order_update.total_error',
                'sylius_admin_order_update',
                $id,
            );
        } catch (\Throwable) {
            return $this->addFlashAndRedirect(
                'error',
                'setono_sylius_order_edit.order_update.general_error',
                'sylius_admin_order_update',
                $id,
            );
        }

        return $this->addFlashAndRedirect(
            'success',
            'setono_sylius_order_edit.order_update.success',
            'sylius_admin_order_show',
            $id,
        );
    }

    private function addFlashAndRedirect(
        string $type,
        string $message,
        string $route,
        int $orderId,
    ): RedirectResponse {
        $session = $this->requestStack->getSession();

        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, $message);
        }

        return new RedirectResponse($this->router->generate($route, ['id' => $orderId]));
    }
}
