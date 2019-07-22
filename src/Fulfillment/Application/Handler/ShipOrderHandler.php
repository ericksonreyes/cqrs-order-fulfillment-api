<?php

namespace Fulfillment\Application\Handler;

use Fulfillment\Application\ShipOrder;
use Fulfillment\Domain\Model\Order\Exceptions\OrderNotFoundError;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use InvalidArgumentException;

/**
 * Class ShipOrderHandler
 * @package Fulfillment\Application\Handler
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ShipOrderHandler
{

    /* @var OrderRepository
     */
    protected $repository;


    /**
     * ShipOrderHandler constructor.
     *
     * @param OrderRepository $repository
     * @throws InvalidArgumentException
     */
    public function __construct(
        OrderRepository $repository
    ) {
        $this->repository = $repository;
    }


    /**
     * @param ShipOrder $command
     */
    public function handleThis(ShipOrder $command): void
    {
        $existingOrder = $this->repository()->findById($command->orderId());

        if ($existingOrder instanceof OrderInterface === false) {
            throw new OrderNotFoundError(OrderInterface::ERROR_ORDER_NOT_FOUND);
        }

        $existingOrder->ship(
            $command->invoker(),
            $command->shipper(),
            $command->trackingId(),
            $command->dateShipped()
        );
        $this->repository()->store($existingOrder);
    }


    /**
     * @return OrderRepository
     */
    public function repository(): OrderRepository
    {
        return $this->repository;
    }
}
