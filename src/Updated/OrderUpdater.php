<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Updated;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Checker\PostUpdateChangesCheckerInterface;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Setono\SyliusOrderEditPlugin\Preparer\OrderPreparerInterface;
use Setono\SyliusOrderEditPlugin\Processor\UpdatedOrderProcessorInterface;
use Setono\SyliusOrderEditPlugin\Provider\UpdatedOrderProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderUpdater implements OrderUpdaterInterface
{
    public function __construct(
        private readonly OrderPreparerInterface $oldOrderProvider,
        private readonly UpdatedOrderProviderInterface $updatedOrderProvider,
        private readonly UpdatedOrderProcessorInterface $updatedOrderProcessor,
        private readonly PostUpdateChangesCheckerInterface $postUpdateChangesChecker,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function update(Request $request, int $orderId): void
    {
        $order = $this->oldOrderProvider->prepareToUpdate($orderId);
        /** @var InitialTotalAwareOrderInterface $oldOrder */
        $oldOrder = clone $order;

        $updatedOrder = $this->updatedOrderProvider->provideFromOldOrderAndRequest($order, $request);
        $this->updatedOrderProcessor->process($updatedOrder);
        $this->postUpdateChangesChecker->check($oldOrder, $updatedOrder);
        $this->entityManager->flush();
    }
}
