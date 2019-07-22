<?php

namespace Fulfillment\Domain\Model\Order;

use DateTimeImmutable;
use DateTimeInterface;
use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use Exception;
use Fulfillment\Domain\Model\Order\Event\OrderWasAccepted;
use Fulfillment\Domain\Model\Order\Event\OrderWasCancelled;
use Fulfillment\Domain\Model\Order\Event\OrderWasCompleted;
use Fulfillment\Domain\Model\Order\Event\OrderWasPlaced;
use Fulfillment\Domain\Model\Order\Event\OrderWasShipped;
use Fulfillment\Domain\Model\Order\Exceptions\AnonymousOrderCommandError;
use Fulfillment\Domain\Model\Order\Exceptions\EmptyOrderError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingCustomerIdError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingShipperError;
use Fulfillment\Domain\Model\Order\Exceptions\MissingTrackingIdError;
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
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Order extends EventSourcedEntity implements OrderInterface
{
    /**
     * @var string
     */
    protected $customerId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var DateTimeInterface
     */
    protected $postedOn;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    private $id;

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
     * @return DateTimeInterface
     */
    public function postedOn(): DateTimeInterface
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
     * @throws Exception
     */
    public function create(string $createdBy, string $customerId, array $items): void
    {
        if (trim($createdBy) === '') {
            throw new AnonymousOrderCommandError(OrderInterface::ERROR_MISSING_CREATED_BY);
        }

        if (trim($customerId) === '') {
            throw new MissingCustomerIdError(OrderInterface::ERROR_MISSING_CUSTOMER_ID);
        }

        if (count($items) < 1) {
            throw new EmptyOrderError(OrderInterface::ERROR_EMPTY_ORDER);
        }

        $event = OrderWasPlaced::raise($createdBy, $this->id(), $customerId, $items);
        $this->storeAndReplayThis($event);
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @param string $acceptedBy
     * @throws Exception
     */
    public function accept(string $acceptedBy): void
    {
        if (trim($acceptedBy) === '') {
            throw new AnonymousOrderCommandError(OrderInterface::ERROR_MISSING_CREATED_BY);
        }

        $event = OrderWasAccepted::raise($acceptedBy, $this->id());
        $this->storeAndReplayThis($event);
    }

    /**
     * @param string $shippedBy
     * @param string $shipper
     * @param string $trackingId
     * @param DateTimeInterface $dateShipped
     * @throws Exception
     */
    public function ship(string $shippedBy, string $shipper, string $trackingId, DateTimeInterface $dateShipped): void
    {
        if (trim($shippedBy) === '') {
            throw new AnonymousOrderCommandError(OrderInterface::ERROR_MISSING_SHIPPED_BY);
        }

        if (trim($shipper) === '') {
            throw new MissingShipperError(OrderInterface::ERROR_MISSING_SHIPPER);
        }

        if (trim($trackingId) === '') {
            throw new MissingTrackingIdError(OrderInterface::ERROR_MISSING_TRACKING_ID);
        }
        $event = OrderWasShipped::raise($shippedBy, $this->id(), $shipper, $trackingId, $dateShipped);
        $this->storeAndReplayThis($event);
    }

    /**
     * @param string $cancelledBy
     * @param string $reason
     * @throws Exception
     */
    public function cancel(string $cancelledBy, string $reason): void
    {
        if (trim($cancelledBy) === '') {
            throw new AnonymousOrderCommandError(OrderInterface::ERROR_MISSING_CANCELLED_BY);
        }

        $event = OrderWasCancelled::raise($cancelledBy, $this->id(), $reason);
        $this->storeAndReplayThis($event);
    }

    /**
     * @param string $closedBy
     * @throws Exception
     */
    public function close(string $closedBy): void
    {
        if (trim($closedBy) === '') {
            throw new AnonymousOrderCommandError(OrderInterface::ERROR_MISSING_CLOSED_BY);
        }
        $event = OrderWasCompleted::raise($closedBy, $this->id());
        $this->storeAndReplayThis($event);
    }

    /**
     * @param OrderWasPlaced $event
     * @throws Exception
     */
    protected function replayOrderWasPlaced(OrderWasPlaced $event): void
    {
        $this->customerId = $event->customerId();
        $this->items = $event->items();
        $this->status = 'Pending';
        $this->postedOn = new DateTimeImmutable();
    }

    /**
     * @param OrderWasAccepted $event
     */
    protected function replayOrderWasAccepted(OrderWasAccepted $event): void
    {
        $this->status = OrderInterface::ORDER_STATUS_ACCEPTED;
    }

    /**
     * @param OrderWasShipped $event
     */
    protected function replayOrderWasShipped(OrderWasShipped $event): void
    {
        $this->status = OrderInterface::ORDER_STATUS_SHIPPED;
    }

    /**
     * @param OrderWasCancelled $event
     */
    protected function replayOrderWasCancelled(OrderWasCancelled $event): void
    {
        $this->status = OrderInterface::ORDER_STATUS_CANCELLED;
    }

    /**
     * @param OrderWasCompleted $event
     */
    protected function replayOrderWasCompleted(OrderWasCompleted $event): void
    {
        $this->status = OrderInterface::ORDER_STATUS_COMPLETED;
    }
}
