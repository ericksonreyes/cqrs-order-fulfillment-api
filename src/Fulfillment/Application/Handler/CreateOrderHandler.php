<?php

namespace Fulfillment\Application\Handler;

use Fulfillment\Application\CreateOrder;
use Fulfillment\Domain\Model\Order\Exceptions\DuplicateOrderIdError;
use Fulfillment\Domain\Model\Order\Order;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use InvalidArgumentException;

/**
 * Class CreateOrderHandler
 * @package Fulfillment\Application\Handler
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CreateOrderHandler
{

    /* @var OrderRepository
     */
    protected $repository;


    /**
     * CreateOrderHandler constructor.
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
     * @param CreateOrder $command
     * @throws \Exception
     */
    public function handleThis(CreateOrder $command): void
    {
        if ($this->repository()->findById($command->orderId())) {
            throw new DuplicateOrderIdError(OrderInterface::ERROR_DUPLICATE_ORDER_ID);
        }

        $newOrder = new Order($command->orderId());
        $newOrder->create($command->invoker(), $command->customerId(), $command->items());
        $this->repository()->store($newOrder);
    }


    /**
     * @return OrderRepository
     */
    public function repository(): OrderRepository
    {
        return $this->repository;
    }
}
