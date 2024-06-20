<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Preparer;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderPreparer implements OrderPreparerInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderInventoryOperatorInterface $orderInventoryOperator,
    ) {
    }

    public function prepareToUpdate(int $orderId): EditableOrderInterface
    {
        /** @var EditableOrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::isInstanceOf($order, EditableOrderInterface::class);

        $this->orderInventoryOperator->cancel($order);

        return $order;
    }
}
