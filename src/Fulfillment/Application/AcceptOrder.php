<?php
namespace Fulfillment\Application;

use InvalidArgumentException;

/**
 * Class AcceptOrder
 * @package Fulfillment\Application
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class AcceptOrder
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
     * AcceptOrder constructor.
     *
     * @param string $invoker
     * @param string $orderId
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $invoker,
        string $orderId
    ) {
        $invoker = trim($invoker);
        if ($invoker === '') {
            throw new InvalidArgumentException('AcceptOrder invoker is required.');
        }
        $this->invoker = $invoker;
        $this->orderId = $orderId;
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
}
