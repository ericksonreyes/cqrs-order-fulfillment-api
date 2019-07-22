<?php


namespace Fulfillment\Domain\Model\Order\Repository;

use Fulfillment\Domain\Model\Order\OrderInterface;

/**
 * Interface OrderRepository
 * @package Fulfillment\Domain\Model\Order\Repository
 */
interface OrderRepository
{

    /**
     * @param string $orderId
     * @return OrderInterface|null
     */
    public function findById(string $orderId): ?OrderInterface;

    /**
     * @param OrderInterface $order
     */
    public function store(OrderInterface $order): void;
}