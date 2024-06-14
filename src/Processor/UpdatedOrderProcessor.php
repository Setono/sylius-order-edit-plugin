<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Processor;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class UpdatedOrderProcessor implements UpdatedOrderProcessorInterface
{
    public function __construct(
        private readonly OrderProcessorInterface $orderProcessor,
        private readonly OrderInventoryOperatorInterface $orderInventoryOperator,
        private readonly OrderProcessorInterface $afterCheckoutOrderPaymentProcessor,
    ) {
    }

    public function process(OrderInterface $newOrder): OrderInterface
    {
        $newOrder->setState(OrderInterface::STATE_CART);
        $this->orderProcessor->process($newOrder);
        $this->afterCheckoutOrderPaymentProcessor->process($newOrder);
        $newOrder->setState(OrderInterface::STATE_NEW);
        $this->orderInventoryOperator->hold($newOrder);

        return $newOrder;
//        if ($initialTotal < $newOrder->getTotal()) {
//            throw new NewOrderWrongTotalException();
//        }
//
//        $this->entityManager->flush();
    }
}
