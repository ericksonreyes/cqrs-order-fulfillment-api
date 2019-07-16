<?php

namespace App\Services\EventStorages\MySQL;

use App\Models\Command\EventModel;
use App\Services\EventFactory;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventRepository;
use EricksonReyes\DomainDrivenDesign\Infrastructure\IdentityGenerator;

class MySqlEventStorage implements EventRepository
{
    /**
     * @var EventModel
     */
    private $events;

    /**
     * @var EventFactory
     */
    private $factory;

    /**
     * @var IdentityGenerator
     */
    private $identityGenerator;

    /**
     * EloquentEventStoreRepository constructor.
     * @param EventModel $events
     * @param EventFactory $factory
     * @param IdentityGenerator $identityGenerator
     */
    public function __construct(EventModel $events, EventFactory $factory, IdentityGenerator $identityGenerator)
    {
        $this->events = $events;
        $this->factory = $factory;
        $this->identityGenerator = $identityGenerator;
    }

    /**
     * @param string $eventId
     * @return Event|null
     */
    public function findById(string $eventId): ?Event
    {
        $models = $this->events->where('event_id', $eventId)->get();

        foreach ($models as $model) {
            $event = $this->factory->makeEventFromName(
                $model->event_name,
                json_decode($model->event_data, true)
            );

            if ($event instanceof Event) {
                return $event;
            }
        }

        return null;
    }


    /**
     * @param string $entityId
     * @return Event[]
     */
    public function findAllByEntityId(string $entityId): array
    {
        $models = $this->events->where('entity_id', $entityId)->get();

        $events = [];
        foreach ($models as $model) {
            $event = $this->factory->makeEventFromName(
                $model->event_name,
                json_decode($model->event_data, true)
            );

            if ($event instanceof Event) {
                $events[] = $event;
            }
        }

        return $events;
    }


    /**
     * @param Event $domainEvent
     * @throws \Exception
     */
    public function store(Event $domainEvent): void
    {
        $newEvent = new EventModel();
        $newEvent->event_id = $this->identityGenerator->nextIdentity('event-');
        $newEvent->event_name = $domainEvent->eventName();
        $newEvent->happened_on = time();
        $newEvent->context_name = $domainEvent->entityContext();
        $newEvent->entity_type = $domainEvent->entityType();
        $newEvent->entity_id = $domainEvent->entityId();
        $newEvent->event_data = json_encode($domainEvent->toArray());
        $newEvent->event_meta_data = json_encode($_SERVER);
        $newEvent->save();
    }
}
