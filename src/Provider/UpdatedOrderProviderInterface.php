<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

interface UpdatedOrderProviderInterface
{
    public function provideFromOldOrderAndRequest(OrderInterface $oldOrder, Request $request): OrderInterface;
}
