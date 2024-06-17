<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ApiBundle\Event\OrderCompleted;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class OrderCompletedListener
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(OrderCompleted $event): void
    {
        $order = $this->orderRepository->findOneBy(['tokenValue' => $event->orderToken()]);
        if ($order === null) {
            return;
        }

        $order->setInitialTotal($order->getTotal());

        $this->entityManager->flush();
    }
}
