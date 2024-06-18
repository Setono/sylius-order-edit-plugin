<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\OrderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;

final class OrderInitialTotalProcessor implements OrderInitialTotalProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function process(EditableOrderInterface $order): void
    {
        $order->setInitialTotal($order->getTotal());

        $this->entityManager->flush();
    }
}
