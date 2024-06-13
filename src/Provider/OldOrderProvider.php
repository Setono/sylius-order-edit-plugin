<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OldOrderProvider
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderInventoryOperatorInterface $orderInventoryOperator,
    ) {
    }

    public function provide(int $orderId): OrderInterface
    {
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);

        $this->orderInventoryOperator->cancel($order);

        return $order;
    }
}
