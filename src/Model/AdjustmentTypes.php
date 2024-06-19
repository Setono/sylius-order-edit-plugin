<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Model;

final class AdjustmentTypes
{
    public const SETONO_ADMIN_ORDER_DISCOUNT = 'setono_admin_order_discount';

    public const SETONO_ADMIN_ORDER_ITEM_DISCOUNT = 'setono_admin_order_item_discount';

    private function __construct()
    {
    }
}
