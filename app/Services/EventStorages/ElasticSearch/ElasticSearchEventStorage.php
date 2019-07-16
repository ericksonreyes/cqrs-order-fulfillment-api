<?php

namespace App\Services\EventStorages\ElasticSearch;

use App\Services\EventFactory;
use Elasticsearch\Client;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventRepository;

class ElasticSearchEventStorage implements EventRepository
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * ElasticSearchEventStorage constructor.
     * @param Client $client
     * @param EventFactory $eventFactory
     */
    public function __construct(Client $client, EventFactory $eventFactory)
    {
        $this->client = $client;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param Event $domainEvent
     * @return mixed
     */
    public function store(Event $domainEvent): void
    {
        $params = [
            'index' => $this->indexName($domainEvent->entityContext() . ':' . $domainEvent->entityType()),
            'type' => 'events',
            'body' => [
                'happenedOn' => $domainEvent->happenedOn()->getTimestamp(),
                'entityContext' => $domainEvent->entityContext(),
                'entityType' => $domainEvent->entityType(),
                'entityId' => $domainEvent->entityId(),
                'eventName' => $domainEvent->eventName(),
                'eventData' => json_encode($domainEvent->toArray()),
                'eventMetaData' => json_encode($_SERVER)
            ]
        ];
        $this->client->index($params);
    }

    public function findById(string $eventId): ?Event
    {
        $searchResult = $this->client->search([
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'term' => [
                                'eventId.keyword' => $eventId
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'happenedOn' => [
                        'order' => 'asc'
                    ]
                ]
            ]
        ]);
        if (isset($searchResult['hits']['hits'])) {
            $hits = $searchResult['hits']['hits'];

            if (count($hits) > 0) {
                $row = $hits[0]['_source'];
                return $this->eventFactory->makeEventFromName(
                    $row['eventName'],
                    json_decode($row['eventData'], true)
                );
            }
        }

        return null;
    }


    /**
     * @param string $contextName
     * @param string $entityType
     * @param string $entityId
     * @return Event[]
     */
    public function findAllByEntityId(string $entityId): array
    {
        $searchResult = $this->client->search([
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'term' => [
                                'entityId.keyword' => $entityId
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'happenedOn' => [
                        'order' => 'asc'
                    ]
                ]
            ]
        ]);
        if (isset($searchResult['hits']['hits'])) {
            $hits = $searchResult['hits']['hits'];
            $events = [];

            foreach ($hits as $hit) {
                $row = $hit['_source'];
                $events[] = $this->eventFactory->makeEventFromName(
                    $row['eventName'],
                    json_decode($row['eventData'], true)
                );
            }
            return $events;
        }

        return null;
    }

    /**
     * @param $entityId
     * @return Event[]|null
     */
    public function getEventsForId($entityId): ?array
    {
        $searchResult = $this->client->search([
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'term' => [
                                'entityId.keyword' => $entityId
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'happenedOn' => [
                        'order' => 'asc'
                    ]
                ]
            ]
        ]);
        if (isset($searchResult['hits']['hits'])) {
            $hits = $searchResult['hits']['hits'];
            $events = [];

            foreach ($hits as $hit) {
                $row = $hit['_source'];
                $events[] = $this->eventFactory->makeEventFromName(
                    $row['eventName'],
                    json_decode($row['eventData'], true)
                );
            }
            return $events;
        }

        return null;
    }


    /**
     * @param string $indexName
     * @return null|string|string[]
     */
    private function indexName(string $indexName)
    {
        return preg_replace('/[^A-Za-z0-9-_:]/', '', strtolower($indexName));
    }
}
