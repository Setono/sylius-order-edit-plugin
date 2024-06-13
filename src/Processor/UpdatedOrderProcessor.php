<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class UpdatedOrderProcessor
{
    public function __construct(
        private OrderProcessorInterface $orderProcessor,
        private OrderInventoryOperatorInterface $orderInventoryOperator,
        private OrderProcessorInterface $afterCheckoutOrderPaymentProcessor,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function process(int $initialTotal, OrderInterface $newOrder): void
    {
        $newOrder->setState(OrderInterface::STATE_CART);
        $this->orderProcessor->process($newOrder);
        $this->afterCheckoutOrderPaymentProcessor->process($newOrder);
        $newOrder->setState(OrderInterface::STATE_NEW);
        $this->orderInventoryOperator->hold($newOrder);

        if ($initialTotal < $newOrder->getTotal()) {
            throw NewOrderWrongTotalException::occur();
        }

        $this->entityManager->flush();
    }
}
