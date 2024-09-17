<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Updater;

use Setono\SyliusOrderEditPlugin\Exception\NewOrderWrongTotalException;
use Symfony\Component\HttpFoundation\Request;

interface OrderUpdaterInterface
{
    /**
     * @throws NewOrderWrongTotalException
     * @throws \InvalidArgumentException
     */
    public function update(Request $request, int $orderId): void;
}
