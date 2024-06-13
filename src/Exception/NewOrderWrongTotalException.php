<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Exception;

final class NewOrderWrongTotalException extends \RuntimeException
{
    public static function occur(): self
    {
        return new self('New order total is greater than the initial order total');
    }
}
