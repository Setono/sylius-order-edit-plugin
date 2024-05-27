<?php

declare(strict_types=1);

namespace Setono\SyliusOrderEditPlugin\Controller;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface, EventSubscriberInterface
{
    /** @var array<string, int> The quantities of the items in the order */
    private array $quantities = [];

    public function __construct(
        private readonly ResourceUpdateHandlerInterface $decorated,
        private readonly StateMachineInterface $stateMachine,
        private readonly OrderProcessorInterface $orderProcessor,
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'populateQuantities',
        ];
    }

    public function populateQuantities(PreSetDataEvent $event): void
    {
        /** @var OrderInterface|mixed $order */
        $order = $event->getData();
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            Assert::notNull($variant);

            if (!$variant->isTracked()) {
                continue;
            }

            $this->quantities[(string) $variant->getCode()] = $item->getQuantity();
        }
    }

    /**
     * @param ResourceInterface|OrderInterface $resource
     *
     * @throws UpdateHandlingException
     */
    public function handle(
        ResourceInterface $resource,
        RequestConfiguration $requestConfiguration,
        ObjectManager $manager,
    ): void {
        // This handler will only handle orders updated through the admin interface
        $route = $requestConfiguration->getRequest()->attributes->getString('_route');
        if ('sylius_admin_order_update' !== $route) {
            $this->decorated->handle($resource, $requestConfiguration, $manager);

            return;
        }

        try {
            $this->giveBack();

            // The resource should be an order now
            Assert::isInstanceOf($resource, OrderInterface::class);

            $requestConfiguration->getParameters()->set('state_machine', [
                'graph' => 'sylius_order',
                'transition' => 'edit',
            ]);

            $this->stateMachine->apply($requestConfiguration, $resource);

            $this->orderProcessor->process($resource);

            $requestConfiguration->getParameters()->set('state_machine', [
                'graph' => 'sylius_order',
                'transition' => 'create',
            ]);

            $this->stateMachine->apply($requestConfiguration, $resource);

            $requestConfiguration->getParameters()->remove('state_machine');
        } catch (\Throwable $e) {
            // todo save the exception message in the flashbag under a namespace to be able to output it in the form
            throw new UpdateHandlingException(
                message: $e->getMessage(),
                previous: $e,
            );
        }

        $this->decorated->handle($resource, $requestConfiguration, $manager);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function giveBack(): void
    {
        foreach ($this->quantities as $code => $quantity) {
            $variant = $this->productVariantRepository->findOneBy(['code' => $code, 'tracked' => true]);
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            $onHold = (int) $variant->getOnHold();

            Assert::greaterThanEq(
                $onHold - $quantity,
                0,
                sprintf(
                    'Trying to decrease on hold value from %d to %d for product "%s" (%s) which is not possible',
                    $onHold,
                    $onHold - $quantity,
                    (string) $variant->getProduct()?->getName(),
                    $variant->getName() ?? 'No variant name',
                ),
            );

            $variant->setOnHold($onHold - $quantity);
        }
    }
}
