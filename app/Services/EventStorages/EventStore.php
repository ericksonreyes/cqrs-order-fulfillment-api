<?php

namespace App\Services\EventStorages;

use EricksonReyes\DomainDrivenDesign\Domain\Event;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventPublisher;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventRepository;

class EventStore
{

    /**
     * @var EventRepository[]
     */
    private $repositories = [];

    /**
     * @var EventPublisher[]
     */
    private $publishers = [];

    /**
     * EventStoreRepository constructor.
     * @param EventRepository $repository
     */
    public function __construct(EventRepository $repository)
    {
        $this->repositories[] = $repository;
    }

    /**
     * @param EventRepository $repository
     */
    public function addRepository(EventRepository $repository): void
    {
        $this->repositories[] = $repository;
    }

    /**
     * @param EventPublisher $publisher
     */
    public function addPublisher(EventPublisher $publisher): void
    {
        $this->publishers[] = $publisher;
    }


    /**
     * @param Event $domainEvent
     * @return mixed
     */
    public function store(Event $domainEvent): void
    {
        foreach ($this->repositories as $repository) {
            $repository->store($domainEvent);
        }

        foreach ($this->publishers as $publisher) {
            $publisher->publish($domainEvent);
        }
    }

    /**
     * @param string $entityId
     * @return Event[]
     */
    public function getEventsFor(string $entityId): array
    {
        return $this->repositories[0]->findAllByEntityId($entityId);
    }
}
