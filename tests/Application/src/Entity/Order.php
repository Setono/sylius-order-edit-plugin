<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderTrait;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends \Sylius\Component\Core\Model\Order implements EditableOrderInterface
{
    use EditableOrderTrait;
}
