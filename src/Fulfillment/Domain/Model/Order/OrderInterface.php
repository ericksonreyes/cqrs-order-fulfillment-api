<?php

namespace Fulfillment\Domain\Model\Order;

/**
 * Interface OrderInterface
 * @package Fulfillment\Domain\Model\Order
 */
interface OrderInterface
{

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
     * @return int
     */
    public function postedOn(): int;

    /**
     * @return array
     */
    public function items(): array;

    /**
     * @param string $createdBy
     * @param array $items
     */
    public function create(string $createdBy, array $items): void;

    /**
     * @param string $acceptedBy
     */
    public function accept(string $acceptedBy): void;

    /**
     * @param string $shippedBy
     * @param string $shipper
     * @param string $trackingId
     * @param int $dateShipped
     */
    public function ship(string $shippedBy, string $shipper, string $trackingId, int $dateShipped): void;

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