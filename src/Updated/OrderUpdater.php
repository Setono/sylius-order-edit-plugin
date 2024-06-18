<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Updated;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Checker\PostUpdateChangesCheckerInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Event\OrderUpdated;
use Setono\SyliusOrderEditPlugin\Event\PaidOrderTotalChanged;
use Setono\SyliusOrderEditPlugin\Preparer\OrderPreparerInterface;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessorInterface;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderUpdater implements OrderUpdaterInterface
{
    public function __construct(
        private readonly OrderPreparerInterface $oldOrderProvider,
        private readonly UpdatedOrderProviderInterface $updatedOrderProvider,
        private readonly UpdatedOrderProcessorInterface $updatedOrderProcessor,
        private readonly PostUpdateChangesCheckerInterface $postUpdateChangesChecker,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $eventBus,
    ) {
    }

    public function update(Request $request, int $orderId): void
    {
        $order = $this->oldOrderProvider->prepareToUpdate($orderId);
        /** @var EditableOrderInterface $oldOrder */
        $oldOrder = clone $order;

        $updatedOrder = $this->updatedOrderProvider->provideFromOldOrderAndRequest($order, $request);
        $this->updatedOrderProcessor->process($updatedOrder);
        $this->postUpdateChangesChecker->check($oldOrder, $updatedOrder);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new OrderUpdated($orderId));
        if ($updatedOrder->isAlreadyPaid()) {
            $this->eventBus->dispatch(new PaidOrderTotalChanged($orderId, $oldOrder->getTotal(), $updatedOrder->getTotal()));
        }
    }
}
