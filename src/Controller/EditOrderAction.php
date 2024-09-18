<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Setono\SyliusOrderEditPlugin\Updater\OrderUpdaterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatableMessage;

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
                'sylius_admin_order_update',
                $id,
                'error',
                'setono_sylius_order_edit.order_update.total_error',
            );
        } catch (\Throwable $e) {
            return $this->addFlashAndRedirect(
                'sylius_admin_order_update',
                $id,
                'error',
                'setono_sylius_order_edit.order_update.general_error',
                ['%error%' => $e->getMessage()],
            );
        }

        return $this->addFlashAndRedirect(
            'sylius_admin_order_show',
            $id,
            'success',
            'setono_sylius_order_edit.order_update.success',
        );
    }

    private function addFlashAndRedirect(
        string $route,
        int $orderId,
        string $type,
        string $message,
        array $messageParameters = [],
    ): RedirectResponse {
        $session = $this->requestStack->getSession();

        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, new TranslatableMessage($message, $messageParameters, 'flashes'));
        }

        return new RedirectResponse($this->router->generate($route, ['id' => $orderId]));
    }
}
