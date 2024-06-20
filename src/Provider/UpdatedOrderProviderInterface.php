<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

interface UpdatedOrderProviderInterface
{
    public function provideFromOldOrderAndRequest(OrderInterface $oldOrder, Request $request): EditableOrderInterface;
}
