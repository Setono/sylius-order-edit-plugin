<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Updated;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Symfony\Component\HttpFoundation\Request;

interface OrderUpdaterInterface
{
    /**
     * @throws NewOrderWrongTotalException
     *
     * @throw \InvalidArgumentException
     */
    public function update(Request $request, int $orderId): void;
}
