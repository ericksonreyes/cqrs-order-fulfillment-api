<?php

namespace Fulfillment\Application\Handler;

use Fulfillment\Application\AcceptOrder;
use Fulfillment\Domain\Model\Order\Exceptions\OrderNotFoundError;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;
use InvalidArgumentException;

/**
 * Class AcceptOrderHandler
 * @package Fulfillment\Application\Handler
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AcceptOrderHandler
{

    /* @var OrderRepository
     */
    protected $repository;


    /**
     * AcceptOrderHandler constructor.
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
     * @param AcceptOrder $command
     */
    public function handleThis(AcceptOrder $command): void
    {
        $existingOrder = $this->repository()->findById($command->orderId());

        if ($existingOrder instanceof OrderInterface === false) {
            throw new OrderNotFoundError(OrderInterface::ERROR_ORDER_NOT_FOUND);
        }

        $existingOrder->accept($command->invoker());
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
