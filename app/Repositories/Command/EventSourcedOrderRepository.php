<?php

namespace App\Repositories\Command;

use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventRepository;
use Fulfillment\Domain\Model\Order\Order;
use Fulfillment\Domain\Model\Order\OrderInterface;
use Fulfillment\Domain\Model\Order\Repository\OrderRepository;

/**
 * Class EventSourcedCustomerRepository
 * @package App\Repositories\Command
 */
class EventSourcedOrderRepository implements OrderRepository
{

    /**
     * @var EventRepository
     */
    private $eventRepository;


    /**
     * EventSourcedAccountRepository constructor.
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param string $orderId
     * @return OrderInterface|null
     */
    public function findById(string $orderId): ?OrderInterface
    {
        $events = $this->eventRepository->findAllByEntityId($orderId);
        if (count($events) > 0) {
            $order = new Order($orderId);
            foreach ($events as $event) {
                $order->replayThis($event);
            }
            return $order;
        }

        return null;
    }

    /**
     * @param OrderInterface $order
     */
    public function store(OrderInterface $order): void
    {
        if ($order instanceof EventSourcedEntity) {
            foreach ($order->storedEvents() as $storedEvent) {
                $this->eventRepository->store($storedEvent);
            }
        }
    }
}
