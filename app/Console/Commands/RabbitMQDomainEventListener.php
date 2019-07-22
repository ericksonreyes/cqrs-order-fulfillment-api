<?php


namespace App\Console\Commands;

use App\Services\EventFactory;
use App\Services\EventSubscribers\Projector;
use App\Services\EventSubscribers\Projectors;
use App\Services\RabbitMQEventSubscriber;
use EricksonReyes\DomainDrivenDesign\Domain\Event;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RabbitMQDomainEventListenerCommand
 * @package App\Console\Commands
 */
abstract class RabbitMQDomainEventListener extends Command
{
    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var RabbitMQEventSubscriber
     */
    private $eventSubscriber;

    /**
     * @var Projectors
     */
    private $projectors;

    /**
     * @var Container
     */
    private $container;


    /**
     * RabbitMQDomainEventListener constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->container = app()->get(ContainerInterface::class);
        $this->projectors = new Projectors();
        $this->eventFactory = $this->container()->get('event_factory');
        $this->eventSubscriber = $this->container()->get('event_subscriber');

        $this->eventSubscriber->setCallback(function ($message) {
            $this->callback($message);
        });
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        while (true) {
            try {
                $this->comment(' ' . $this->description() . '  ');
                $this->preventDuplicateProcess();

                $projectors = [];
                foreach ($this->eventProjectors()->projectors() as $projector) {
                    $projectors[] = [
                        'name' => $projector->name()
                    ];
                }
                $this->table(['Available listeners'], $projectors);

                $this->line(' [*] Waiting for events. To exit press CTRL+C ');
                $this->eventSubscriber()->listen();
            } catch (Exception $exception) {
                $this->line('');
                $this->error(
                    ' An error occurred: ' . $exception->getMessage() .
                    "\n Exception: " . get_class($exception) .
                    "\n File: " . $exception->getFile() .
                    "\n Line: " . $exception->getLine() .
                    "\n Trace: " . $exception->getTraceAsString()
                );
                $this->line('');
            }
        }
    }

    /**
     * @param $message
     */
    public function callback($message): void
    {
        $data = json_decode($message->body, true);
        $formattedDate = date('Y-m-d h:i:s', $data['happenedOn']);

        $this->warn(
            " [ ] {$formattedDate} {$data['context']}.{$data['entityType']}.{$data['eventName']} event was raised "
        );

        if ($this->projectors instanceof Projectors) {
            $event = $this->eventFactory()->makeEventFromName($data['eventName'], $data);

            if ($event instanceof Event) {
                foreach ($this->projectors->projectors() as $projector) {
                    $time = date('Y-m-d h:i:s');
                    if ($projector->project($event)) {
                        $this->info(" [√] {$time} Projected by {$projector->name()}. ");
                    }
                }
            }
        }
    }

    /**
     * @param Projector $projector
     */
    public function addProjector(Projector $projector): void
    {
        $this->projectors[] = $projector;
    }

    /**
     * @return string
     */
    protected function signature(): string
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    protected function description(): ?string
    {
        return $this->description;
    }

    /**
     * @return RabbitMQEventSubscriber
     */
    protected function eventSubscriber(): ?RabbitMQEventSubscriber
    {
        return $this->eventSubscriber;
    }

    /**
     * @return EventFactory
     */
    protected function eventFactory(): ?EventFactory
    {
        return $this->eventFactory;
    }

    /**
     * @return Projectors
     */
    protected function eventProjectors(): Projectors
    {
        return $this->projectors;
    }

    /**
     * @param Projectors $eventProjectors
     */
    protected function setEventProjectors(Projectors $eventProjectors): void
    {
        $this->projectors = $eventProjectors;
    }

    /**
     * @return Container
     */
    protected function container(): Container
    {
        return $this->container;
    }

    /**
     *
     */
    private function preventDuplicateProcess(): void
    {
        // Initialize variables
        $found = 0;
        $commands = array();

        // Get running processes.
        exec('ps x', $commands);

        // If processes are found
        if (count($commands) > 0) {
            foreach ($commands as $command) {
                if (strpos($command, $this->signature()) !== false) {
                    $found++;
                }
            }
        }

        // If the instance of the file is found more than once.
        if ($found > 1) {
            $this->line('');
            $this->error(' An error occurred: Another process is running. ');
            $this->line('');
            die();
        }
    }
}
