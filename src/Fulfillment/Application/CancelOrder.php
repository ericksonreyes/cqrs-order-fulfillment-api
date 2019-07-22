<?php
namespace Fulfillment\Application;

use InvalidArgumentException;

/**
 * Class CancelOrder
 * @package Fulfillment\Application
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CancelOrder
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
    protected $reason;
    

    /**
     * CancelOrder constructor.
     *
     * @param string $invoker
     * @param string $orderId
     * @param string $reason
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $invoker,
        string $orderId,
        string $reason
    ) {
        $invoker = trim($invoker);
        if ($invoker === '') {
            throw new InvalidArgumentException('CancelOrder invoker is required.');
        }
        $this->invoker = $invoker;
        $this->orderId = $orderId;
        $this->reason = $reason;
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
    public function reason(): string
    {
        return $this->reason;
    }
}
