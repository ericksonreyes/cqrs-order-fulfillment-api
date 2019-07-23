<?php

namespace Fulfillment\Domain\Model\Order\Event;

use DateTimeImmutable;
use EricksonReyes\DomainDrivenDesign\Domain\AccountableEvent;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Exception;

/**
 * Class OrderWasCancelled
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrderWasCancelled implements AccountableEvent
{
    /**
     * @var string
     */
    protected $reason;

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


    /**
     * OrderWasCancelled constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $raisedBy
     * @param string $entityId
     * @param string $reason
     * @return OrderWasCancelled
     * @throws Exception
     */
    public static function raise(
        string $raisedBy,
        string $entityId,
        string $reason
    ): self
    {
        $event = new static();

        $event->happenedOn = new DateTimeImmutable();
        $event->raisedBy = $raisedBy;
        $event->entityId = $entityId;
        $event->reason = $reason;
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
        $event->reason = $array['data']['reason'];
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
                'reason' => $this->reason(),
                'raisedBy' => $this->raisedBy(),
                'entityId' => $this->entityId(),
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
        return 'OrderWasCancelled';
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
    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function raisedBy(): string
    {
        return $this->raisedBy;
    }
}
