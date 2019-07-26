<?php

namespace Fulfillment\Domain\Model\Order\Event;

use DateTimeImmutable;
use DateTimeInterface;
use EricksonReyes\DomainDrivenDesign\Domain\AccountableEvent;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Exception;

/**
 * Class OrderWasShipped
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrderWasShipped implements AccountableEvent
{

    /**
     * @var string
     */
    protected $raisedBy;

    /**
     * @var string
     */
    protected $entityId;

    /**
     * @var DateTimeImmutable
     */
    protected $happenedOn;

    /* @var string
     */
    protected $shipper;

    /* @var string
     */
    protected $trackingId;

    /* @var int
     */
    protected $dateShipped;


    /**
     * OrderWasShipped constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $raisedBy
     * @param string $entityId
     * @param string $shipper
     * @param string $trackingId
     * @param DateTimeInterface $dateShipped
     * @return OrderWasShipped
     * @throws Exception
     */
    public static function raise(
        string $raisedBy,
        string $entityId,
        string $shipper,
        string $trackingId,
        DateTimeInterface $dateShipped
    ): self {
        $event = new static();

        $event->happenedOn = new DateTimeImmutable();
        $event->raisedBy = $raisedBy;
        $event->entityId = $entityId;
        $event->shipper = $shipper;
        $event->trackingId = $trackingId;
        $event->dateShipped = $dateShipped;

        return $event;
    }

    /**
     * @param array $array
     * @return Event
     */
    public static function fromArray(array $array): Event
    {
        $event = new static();
        $event->happenedOn = DateTimeImmutable::createFromFormat('U', (string)$array['happenedOn']);
        $event->raisedBy = $array['data']['raisedBy'];
        $event->entityId = $array['data']['entityId'];
        $event->shipper = $array['data']['shipper'];
        $event->trackingId = $array['data']['trackingId'];
        $event->dateShipped = DateTimeImmutable::createFromFormat('U', (string)$array['data']['dateShipped']);
        return $event;
    }

    /**
     * @return string
     */
    public function entityContext(): string
    {
        return static::staticEntityContext();
    }

    /**
     * @return string
     */
    public static function staticEntityContext(): string
    {
        return 'Fulfillment';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName(),
            'happenedOn' => $this->happenedOn()->getTimestamp(),
            'entityType' => $this->entityType(),
            'entityId' => $this->entityId(),
            'data' => [
                'raisedBy' => $this->raisedBy(),
                'entityId' => $this->entityId(),
                'shipper' => $this->shipper(),
                'trackingId' => $this->trackingId(),
                'dateShipped' => $this->dateShipped()->getTimestamp()
            ]
        ];
    }

    /**
     * @return string
     */
    public function eventName(): string
    {
        return static::staticEventName();
    }

    /**
     * @return string
     */
    public static function staticEventName(): string
    {
        return 'OrderWasShipped';
    }

    /**
     * @return DateTimeImmutable
     */
    public function happenedOn(): DateTimeImmutable
    {
        return $this->happenedOn;
    }

    /**
     * @return string
     */
    public function entityType(): string
    {
        return static::staticEntityType();
    }

    /**
     * @return string
     */
    public static function staticEntityType(): string
    {
        return 'Order';
    }

    /**
     * @return string
     */
    public function entityId(): string
    {
        return $this->entityId;
    }

    /**
     * @return string
     */
    public function raisedBy(): string
    {
        return $this->raisedBy;
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
