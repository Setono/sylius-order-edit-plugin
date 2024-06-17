<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrder;
use Setono\SyliusOrderEditPlugin\Entity\InitialTotalAwareOrderInterface;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="sylius_order")
 */
class Order extends \Sylius\Component\Core\Model\Order implements InitialTotalAwareOrderInterface
{
    use InitialTotalAwareOrder;
}
