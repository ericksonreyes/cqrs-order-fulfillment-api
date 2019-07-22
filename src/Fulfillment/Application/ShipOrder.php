<?php
namespace Fulfillment\Application;

use InvalidArgumentException;
use DateTimeInterface;

/**
 * Class ShipOrder
 * @package Fulfillment\Application
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ShipOrder
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
    protected $shipper;
    
    /**
    /* @var string
    */
    protected $trackingId;
    
    /**
    /* @var DateTimeInterface
    */
    protected $dateShipped;
    

    /**
     * ShipOrder constructor.
     *
     * @param string $invoker
     * @param string $orderId
     * @param string $shipper
     * @param string $trackingId
     * @param DateTimeInterface $dateShipped
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $invoker,
        string $orderId,
        string $shipper,
        string $trackingId,
        DateTimeInterface $dateShipped
    ) {
        $invoker = trim($invoker);
        if ($invoker === '') {
            throw new InvalidArgumentException('ShipOrder invoker is required.');
        }
        $this->invoker = $invoker;
        $this->orderId = $orderId;
        $this->shipper = $shipper;
        $this->trackingId = $trackingId;
        $this->dateShipped = $dateShipped;
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
    public function shipper(): string
    {
        return $this->shipper;
    }


    /**
    * @return string
    */
    public function trackingId(): string
    {
        return $this->trackingId;
    }


    /**
    * @return DateTimeInterface
    */
    public function dateShipped(): DateTimeInterface
    {
        return $this->dateShipped;
    }
}
