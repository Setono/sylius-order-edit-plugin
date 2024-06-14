<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Checker\PostUpdateChangesCheckerInterface;
use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Setono\SyliusOrderEditPlugin\Preparer\OrderPreparerInterface;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessorInterface;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EditOrderAction
{
    public function __construct(
        private readonly OrderPreparerInterface $oldOrderProvider,
        private readonly UpdatedOrderProviderInterface $updatedOrderProvider,
        private readonly UpdatedOrderProcessorInterface $updatedOrderProcessor,
        private readonly PostUpdateChangesCheckerInterface $postUpdateChangesChecker,
        private readonly UrlGeneratorInterface $router,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(Request $request, int $id): Response
    {
        $order = $this->oldOrderProvider->prepareToUpdate($id);

        $oldOrder = clone $order;
        $updatedOrder = $this->updatedOrderProvider->provideFromOldOrderAndRequest($order, $request);

        try {
            $this->updatedOrderProcessor->process($updatedOrder);
            $this->postUpdateChangesChecker->check($oldOrder, $updatedOrder);
            $this->entityManager->flush();
        } catch (NewOrderWrongTotalException) {
            return $this->addFlashAndRedirect(
                'error',
                'setono_sylius_order_edit.order_update.total_error',
                'sylius_admin_order_update',
                $id,
            );
        } catch (\Exception) {
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
