<?php

namespace App\Services\EventSubscribers\ProjectionGenerators;

use App\Models\Query\Order;
use App\Models\Query\OrderItem;
use App\Services\EventSubscribers\Projector;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Fulfillment\Application\CancelOrder;
use Fulfillment\Application\CreateOrder;
use Fulfillment\Application\Handler\CancelOrderHandler;
use Fulfillment\Application\Handler\CreateOrderHandler;
use Fulfillment\Domain\Model\Order\Event\OrderWasAccepted;
use Fulfillment\Domain\Model\Order\Event\OrderWasCancelled;
use Fulfillment\Domain\Model\Order\Event\OrderWasCompleted;
use Fulfillment\Domain\Model\Order\Event\OrderWasPlaced;
use Fulfillment\Domain\Model\Order\Event\OrderWasShipped;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountProjectionGenerator implements Projector
{
    /**
     * @return string
     */
    public function name(): string
    {
        return 'AccountProjectionGenerator';
    }

    /**
     * @param Event $event
     * @return bool
     * @throws \Exception
     */
    public function project(Event $event): bool
    {
        /**
         * @var $container ContainerInterface
         */
        $container = app()->get(ContainerInterface::class);
        $wasProjected = false;

        if ($event instanceof OrderWasPlaced) {
            $order = Order::where('id', $event->entityId())->first() ?? new Order();
            $order->id = $event->entityId();
            $order->status = OrderInterface::ORDER_STATUS_PENDING;
            $order->customerId = $event->customerId();
            $order->postedOn = $event->happenedOn();

            if ($wasProjected = $order->save()) {
                $items = [];
                foreach ($event->items() as $itemArray) {
                    if (!OrderItem::where('id', $itemArray['id'])->first()) {
                        $item = new OrderItem();
                        $item->id = $itemArray['id'];
                        $item->orderId = $event->entityId();
                        $item->productId = $itemArray['productId'];
                        $item->price = $itemArray['price'];
                        $item->quantity = $itemArray['quantity'];

                        if ($item->save()) {
                            $items[] = [
                                'id' => $itemArray['id'],
                                'productId' => $itemArray['productId'],
                                'price' => $itemArray['price'],
                                'quantity' => $itemArray['quantity']
                            ];
                        }
                    }
                }

                /**
                 * There might be a chance that this event may be raised from the public shopping cart.
                 */
                $command = new CreateOrder($event->raisedBy(), $event->entityId(), $event->customerId(), $items);
                $handler = new CreateOrderHandler($container->get('order_repository'));
                $handler->handleThis($command);
            }
        }

        if ($event instanceof OrderWasAccepted) {
            $order = Order::where('id', $event->entityId())->first();
            if ($order) {
                $order->status = OrderInterface::ORDER_STATUS_ACCEPTED;
                $wasProjected = $order->save();
            }
        }

        if ($event instanceof OrderWasCancelled) {
            $order = Order::where('id', $event->entityId())->first();
            if ($order) {
                $order->status = OrderInterface::ORDER_STATUS_CANCELLED;
                $order->cancellationReason = $event->reason();
                $wasProjected = $order->save();

                /**
                 * There might be a chance that this event may be raised from the public shopping cart.
                 */
                $command = new CancelOrder($event->raisedBy(), $event->entityId(), $event->reason());
                $handler = new CancelOrderHandler($container->get('order_repository'));
                $handler->handleThis($command);
            }
        }

        if ($event instanceof OrderWasShipped) {
            $order = Order::where('id', $event->entityId())->first();
            if ($order) {
                $order->shipper = $event->shipper();
                $order->dateShipped = $event->dateShipped();
                $order->trackingId = $event->trackingId();
                $order->status = OrderInterface::ORDER_STATUS_SHIPPED;
                $wasProjected = $order->save();
            }
        }

        if ($event instanceof OrderWasCompleted) {
            $order = Order::where('id', $event->entityId())->first();
            if ($order) {
                $order->status = OrderInterface::ORDER_STATUS_COMPLETED;
                $wasProjected = $order->save();
            }
        }

        return $wasProjected;
    }
}
