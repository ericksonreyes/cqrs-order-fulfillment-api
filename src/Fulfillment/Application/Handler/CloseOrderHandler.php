<?php

namespace Fulfillment\Application\Handler;

use Fulfillment\Application\CloseOrder;
use Fulfillment\Domain\Model\Order\Exceptions\OrderNotFoundError;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use InvalidArgumentException;

/**
 * Class CloseOrderHandler
 * @package Fulfillment\Application\Handler
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CloseOrderHandler
{

    /* @var OrderRepository
     */
    protected $repository;


    /**
     * CloseOrderHandler constructor.
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
     * @param CloseOrder $command
     */
    public function handleThis(CloseOrder $command): void
    {
        $existingOrder = $this->repository()->findById($command->orderId());

        if ($existingOrder instanceof OrderInterface === false) {
            throw new OrderNotFoundError(OrderInterface::ERROR_ORDER_NOT_FOUND);
        }

        $existingOrder->close($command->invoker());
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
