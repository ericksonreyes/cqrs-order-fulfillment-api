<?php

namespace App\Repositories\Command;

use Acme\Banking\Account\Domain\AccountFactory;
use Acme\Banking\Account\Domain\AccountInterface;
use Acme\Banking\Account\Domain\Repository\AccountRepository;
use EricksonReyes\DomainDrivenDesign\EventSourcedEntity;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventRepository;

/**
 * Class EventSourcedCustomerRepository
 * @package App\Repositories\Command
 */
class EventSourcedAccountRepository implements AccountRepository
{

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var AccountFactory
     */
    private $accountFactory;

    /**
     * EventSourcedAccountRepository constructor.
     * @param EventRepository $eventRepository
     * @param AccountFactory $accountFactory
     */
    public function __construct(EventRepository $eventRepository, AccountFactory $accountFactory)
    {
        $this->eventRepository = $eventRepository;
        $this->accountFactory = $accountFactory;
    }

    /**
     * @param string $accountNumber
     * @return AccountInterface|null
     */
    public function findByAccountNumber(string $accountNumber): ?AccountInterface
    {
        $events = $this->eventRepository->findAllByEntityId($accountNumber);
        if (count($events) > 0) {
            $account = $this->accountFactory->create($accountNumber);
            if ($account instanceof EventSourcedEntity) {
                foreach ($events as $event) {
                    $account->replayThis($event);
                }
            }
            return $account;
        }

        return null;
    }

    /**
     * @param AccountInterface $account
     */
    public function store(AccountInterface $account): void
    {
        if ($account instanceof EventSourcedEntity) {
            foreach ($account->storedEvents() as $storedEvent) {
                $this->eventRepository->store($storedEvent);
            }
        }
    }
}
