<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Exception;

final class OrderUpdateException extends \RuntimeException
{
    public function __construct(string $message = 'Something went wrong when updating order.')
    {
        parent::__construct($message);
    }
}
