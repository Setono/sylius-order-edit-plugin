<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderInitialTotalProcessor
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function process(OrderInterface $order): void
    {
        $order->setInitialTotal($order->getTotal());

        $this->entityManager->flush();
    }
}
