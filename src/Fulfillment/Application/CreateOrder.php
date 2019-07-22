<?php
namespace Fulfillment\Application;

use InvalidArgumentException;

/**
 * Class CreateOrder
 * @package Fulfillment\Application
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CreateOrder
{
    /**
     * @var string
     */
    private $invoker;

    /**
    /* @var string
    */
    protected $orderId;
    
    /**
    /* @var string
    */
    protected $customerId;
    
    /**
    /* @var array
    */
    protected $items;
    

    /**
     * CreateOrder constructor.
     *
     * @param string $invoker
     * @param string $orderId
     * @param string $customerId
     * @param array $items
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $invoker,
        string $orderId,
        string $customerId,
        array $items
    ) {
        $invoker = trim($invoker);
        if ($invoker === '') {
            throw new InvalidArgumentException('CreateOrder invoker is required.');
        }
        $this->invoker = $invoker;
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->items = $items;
    }

    /**
    * @return string
    */
    public function invoker(): string
    {
        return $this->invoker;
    }

    
    /**
    * @return string
    */
    public function orderId(): string
    {
        return $this->orderId;
    }


    /**
    * @return string
    */
    public function customerId(): string
    {
        return $this->customerId;
    }


    /**
    * @return array
    */
    public function items(): array
    {
        return $this->items;
    }
}
