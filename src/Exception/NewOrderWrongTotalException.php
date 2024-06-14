<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Exception;

final class NewOrderWrongTotalException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('New order total is greater than the initial order total');
    }
}
