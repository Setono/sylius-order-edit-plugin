# Sylius Order Edit Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Edit orders inside your admin interface.

## Install

```shell
composer require setono/sylius-order-edit-plugin
```

### Import routing

```yaml
# config/routes/setono_sylius_order_edit.yaml
setono_sylius_order_edit:
    resource: "@SetonoSyliusOrderEditPlugin/Resources/config/routes.yaml"
```

If you're using Sylius 1.10, import also additional product variant route:

```yaml
setono_sylius_order_edit_product_variant:
    resource: "@SetonoSyliusOrderEditPlugin/Resources/config/routes/product_variant.yaml"
```

### Extend the `Order` entity

```php
<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderInterface;
use Setono\SyliusOrderEditPlugin\Entity\EditableOrderTrait;
use Sylius\Component\Core\Model\Order as BaseOrder;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements EditableOrderInterface
{
    use EditableOrderTrait;
}
```

### Update your database schema

```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

The plugin adds a new field to the `Order` entity named `initialTotal` which will contain the order total and is used when editing orders.

To set the `initialTotal` field for existing orders, you can add the following to your migration:

```php
<?php
// ...

public function up(Schema $schema): void
{
    // ...
    $this->addSql('UPDATE sylius_order SET initial_total = total');
}

// ...
```

### Done!

You should be able to edit orders in your admin interface. Enjoy :tada:

[ico-version]: https://poser.pugx.org/setono/sylius-order-edit-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-order-edit-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-order-edit-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-order-edit-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-order-edit-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-order-edit-plugin
[link-github-actions]: https://github.com/Setono/sylius-order-edit-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-order-edit-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-order-edit-plugin/master
