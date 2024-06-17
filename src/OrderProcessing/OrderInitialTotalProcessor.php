<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderInitialTotalProcessor
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function process(OrderInterface&InitialTotalAwareOrderInterface $order): void
    {
        $order->setInitialTotal($order->getTotal());

        $this->entityManager->flush();
    }
}
