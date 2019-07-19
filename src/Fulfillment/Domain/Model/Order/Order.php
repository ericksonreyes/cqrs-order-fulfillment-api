<?php

namespace Fulfillment\Domain\Model\Order;

use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use Fulfillment\Domain\Model\Order\Event\OrderWasAccepted;
use Fulfillment\Domain\Model\Order\Event\OrderWasCancelled;
use Fulfillment\Domain\Model\Order\Event\OrderWasCompleted;
use Fulfillment\Domain\Model\Order\Event\OrderWasPlaced;
use Fulfillment\Domain\Model\Order\Event\OrderWasShipped;
use InvalidArgumentException;

/**
 * Class Order
 * @package Fulfillment\Domain\Model\Order
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Order extends EventSourcedEntity implements OrderInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var int
     */
    protected $postedOn;

    /**
     * @var array
     */
    protected $items = [];


    /**
     * Order constructor.
     *
     * @param string $id
     * @throws InvalidArgumentException
     */
    public function __construct(string $id)
    {
        $id = trim($id);
        if ($id === '') {
            throw new InvalidArgumentException('Order id must not be empty.');
        }
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function customerId(): string
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function postedOn(): int
    {
        return $this->postedOn;
    }

    /**
     * @return array
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @param string $createdBy
     * @param string $customerId
     * @param array $items
     * @throws \Exception
     */
    public function create(string $createdBy, string $customerId, array $items): void
    {
        $event = OrderWasPlaced::raise($createdBy, $this->id(), $customerId, $items);
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasPlaced $event
     */
    protected function replayOrderWasPlaced(OrderWasPlaced $event): void {
        $this->customerId = $event->customerId();
        $this->items = $event->items();
        $this->status = 'Pending';
    }


    /**
     * @param string $acceptedBy
     * @throws \Exception
     */
    public function accept(string $acceptedBy): void
    {
        $event = OrderWasAccepted::raise($acceptedBy, $this->id());
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasAccepted $event
     */
    protected function replayOrderWasAccepted(OrderWasAccepted $event): void {
        $this->status = 'Accepted';
    }

    /**
     * @param string $shippedBy
     * @param string $shipper
     * @param string $trackingId
     * @param int $dateShipped
     * @throws \Exception
     */
    public function ship(string $shippedBy, string $shipper, string $trackingId, int $dateShipped): void
    {
        $event = OrderWasShipped::raise($shippedBy, $this->id(), $shipper, $trackingId, $dateShipped);
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasShipped $event
     */
    protected function replayOrderWasShipped(OrderWasShipped $event): void {
        $this->status = 'Shipped';
    }

    /**
     * @param string $cancelledBy
     * @param string $reason
     * @throws \Exception
     */
    public function cancel(string $cancelledBy, string $reason): void
    {
        $event = OrderWasCancelled::raise($cancelledBy, $this->id(), $reason);
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasCancelled $event
     */
    protected function replayOrderWasCancelled(OrderWasCancelled $event): void {
        $this->status = 'Cancelled';
    }

    /**
     * @param string $closedBy
     * @throws \Exception
     */
    public function close(string $closedBy): void
    {
        $event = OrderWasCompleted::raise($closedBy, $this->id());
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasCompleted $event
     */
    protected function replayOrderWasCompleted(OrderWasCompleted $event): void {
        $this->status = 'Completed';
    }


}
