<?php

namespace Fulfillment\Domain\Model\Order\Event;

use DateTimeImmutable;
use EricksonReyes\DomainDrivenDesign\Domain\AccountableEvent;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Exception;

/**
 * Class OrderWasPlaced
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrderWasPlaced implements AccountableEvent
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
    protected $customerId;

    /* @var array
     */
    protected $items;


    /**
     * OrderWasPlaced constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $raisedBy
     * @param string $entityId
     * @param string $customerId
     * @param array $items
     * @return OrderWasPlaced
     * @throws Exception
     */
    public static function raise(
        string $raisedBy,
        string $entityId,
        string $customerId,
        array $items
    ): self
    {
        $event = new static();

        $event->happenedOn = new DateTimeImmutable();
        $event->raisedBy = $raisedBy;
        $event->entityId = $entityId;
        $event->customerId = $customerId;
        $event->items = $items;

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
        $event->customerId = $array['data']['customerId'];
        $event->items = $array['data']['items'];
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
                'customerId' => $this->customerId(),
                'items' => $this->items()
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
        return 'OrderWasPlaced';
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
