# Sylius Order Edit Plugin

[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-github-actions]][link-github-actions]
[![Code Coverage][ico-code-coverage]][link-code-coverage]
[![Mutation testing][ico-infection]][link-infection]

Edit orders inside your admin interface.

## Important (read before using)

Editing orders is a big thing and can have a lot of implications. Before you use this plugin,
there are some things you should check and/or be aware of:

1. **State machine**: Right now this plugin only works if you use the Winzou State Machine as your Sylius state machine.
   You can check this by running `php bin/console debug:container sylius.resource_controller.state_machine`.
   The `class` should be `Sylius\Bundle\ResourceBundle\Controller\StateMachine`.
2. **Flow**: The flow of editing an order looks like this:
   1. You press edit in the order overview.
   2. You edit the order and click `Save changes`.
   3. The order is now transitioned to the `cart` state.
   4. The `sylius.order_processing.order_processor` service is run.
   5. The order is transitioned to the `new` state.
   6. The order is saved
   
    You can see this flow implemented in the `\Setono\SyliusOrderEditPlugin\Controller\ResourceUpdateHandler` class.

What does this mean for you?

First of all, check your state machine implementation. If you don't use the Winzou State Machine, you can't use this plugin.

Secondly, when transitioning the order from `new` to `cart` and back again, the callbacks on the state machine are triggered.
This plugin handles the callbacks added by Sylius, but if you've made any custom callbacks, you need to make sure they are compatible with this flow.

## Install

TODO

## Usage

TODO

[ico-version]: https://poser.pugx.org/setono/sylius-order-edit-plugin/v/stable
[ico-license]: https://poser.pugx.org/setono/sylius-order-edit-plugin/license
[ico-github-actions]: https://github.com/Setono/sylius-order-edit-plugin/workflows/build/badge.svg
[ico-code-coverage]: https://codecov.io/gh/Setono/sylius-order-edit-plugin/graph/badge.svg
[ico-infection]: https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FSetono%2Fsylius-order-edit-plugin%2Fmaster

[link-packagist]: https://packagist.org/packages/setono/sylius-order-edit-plugin
[link-github-actions]: https://github.com/Setono/sylius-order-edit-plugin/actions
[link-code-coverage]: https://codecov.io/gh/Setono/sylius-order-edit-plugin
[link-infection]: https://dashboard.stryker-mutator.io/reports/github.com/Setono/sylius-order-edit-plugin/master
