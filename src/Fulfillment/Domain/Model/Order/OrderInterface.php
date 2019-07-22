<?php

namespace Fulfillment\Domain\Model\Order;

use DateTimeInterface;

/**
 * Interface OrderInterface
 * @package Fulfillment\Domain\Model\Order
 */
interface OrderInterface
{
    public const ORDER_STATUS_PENDING = 'Pending';

    public const ORDER_STATUS_ACCEPTED = 'Accepted';

    public const ORDER_STATUS_SHIPPED = 'Shipped';

    public const ORDER_STATUS_CANCELLED = 'Cancelled';

    public const ORDER_STATUS_COMPLETED = 'Completed';

    public const ERROR_EMPTY_ORDER = 'Your order is empty.';

    public const ERROR_ORDER_NOT_FOUND = 'Order not found.';

    public const ERROR_DUPLICATE_ORDER_ID = 'Duplicate order identifier.';

    public const ERROR_MISSING_CUSTOMER_ID = 'Your order has no customer information.';

    public const ERROR_MISSING_CREATED_BY = 'Need to know who created with order.';

    public const ERROR_MISSING_SHIPPED_BY = 'Need to know who shipped this order.';

    public const ERROR_MISSING_SHIPPER = 'Need to know who is the shipper of this order.';

    public const ERROR_MISSING_TRACKING_ID = 'Need to know the tracking identifier of this order.';

    public const ERROR_MISSING_CANCELLED_BY = 'Need to know who cancelled this order.';

    public const ERROR_MISSING_CLOSED_BY = 'Need to know who closed this order.';

    public const ERROR_ORDER_WAS_ACCEPTED = 'This order was already accepted.';

    public const ERROR_ORDER_WAS_CANCELLED = 'This order was already cancelled.';

    public const ERROR_ORDER_WAS_COMPLETED = 'This order was already completed.';

    public const ERROR_ORDER_WAS_SHIPPED = 'This order was already shipped.';

    public const ERROR_ORDER_WAS_NOT_ACCEPTED = 'This order was not accepted.';

    public const ERROR_ORDER_WAS_NOT_SHIPPED = 'This order was not shipped.';

    /**
     * @return string
     */
    public function id(): string;

    /**
     * @return string
     */
    public function customerId(): string;

    /**
     * @return string
     */
    public function status(): string;

    /**
     * @return DateTimeInterface
     */
    public function postedOn(): DateTimeInterface;

    /**
     * @return array
     */
    public function items(): array;

    /**
     * @param string $createdBy
     * @param string $customerId
     * @param array $items
     */
    public function create(string $createdBy, string $customerId, array $items): void;

    /**
     * @param string $acceptedBy
     */
    public function accept(string $acceptedBy): void;

    /**
     * @param string $shippedBy
     * @param string $shipper
     * @param string $trackingId
     * @param DateTimeInterface $dateShipped
     */
    public function ship(string $shippedBy, string $shipper, string $trackingId, DateTimeInterface $dateShipped): void;

    /**
     * @param string $cancelledBy
     * @param string $reason
     */
    public function cancel(string $cancelledBy, string $reason): void;

    /**
     * @param string $closedBy
     */
    public function close(string $closedBy): void;
}
