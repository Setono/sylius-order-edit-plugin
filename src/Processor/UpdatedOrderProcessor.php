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

    public function process(OrderInterface $updatedOrder): OrderInterface
    {
        $updatedOrder->setState(OrderInterface::STATE_CART);
        $this->orderProcessor->process($updatedOrder);
        $this->afterCheckoutOrderPaymentProcessor->process($updatedOrder);
        $updatedOrder->setState(OrderInterface::STATE_NEW);
        $this->orderInventoryOperator->hold($updatedOrder);

        return $updatedOrder;
    }
}
