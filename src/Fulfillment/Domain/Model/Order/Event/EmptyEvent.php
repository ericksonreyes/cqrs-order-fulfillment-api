<?php


namespace Fulfillment\Domain\Model\Order\Event;

use DateTimeImmutable;
use EricksonReyes\DomainDrivenDesign\Domain\AccountableEvent;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Exception;

/**
 * Class EmptyEvent
 * @package Fulfillment\Domain\Model\Order\Event
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
abstract class EmptyEvent implements AccountableEvent
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


    /**
     * OrderWasCompleted constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $raisedBy
     * @param string $entityId
     * @return OrderWasCompleted
     * @throws Exception
     */
    public static function raise(
        string $raisedBy,
        string $entityId
    ): self {
        $event = new static();

        $event->happenedOn = new DateTimeImmutable();
        $event->raisedBy = $raisedBy;
        $event->entityId = $entityId;

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
}
