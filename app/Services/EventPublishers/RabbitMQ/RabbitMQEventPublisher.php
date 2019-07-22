<?php

namespace App\Services\EventPublishers\RabbitMQ;

use EricksonReyes\DomainDrivenDesign\Domain\Event;
use EricksonReyes\DomainDrivenDesign\Infrastructure\EventPublisher;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQEventPublisher implements EventPublisher
{

    private const DELIVERY_MODE_PERSISTENT = 2;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;


    /**
     * RabbitMQEventPublisher constructor.
     * @param AMQPStreamConnection $connection
     * @param string $exchangeName
     */
    public function __construct(AMQPStreamConnection $connection, string $exchangeName)
    {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
    }


    /**
     * @param Event $domainEvent
     */
    public function publish(Event $domainEvent): void
    {
        $this->connection->reconnect();
        $channel = $this->connection->channel();
        $data = array_merge(['context' => $domainEvent->entityContext()], $domainEvent->toArray());

        $message = new AMQPMessage(json_encode($data), ['delivery_mode' => self::DELIVERY_MODE_PERSISTENT]);
        $isPassive = false;
        $type = 'fanout';
        $isDurable = false;
        $isAutoDelete = false;
        $channel->exchange_declare($this->exchangeName, $type, $isPassive, $isDurable, $isAutoDelete);
        $channel->basic_publish($message, $this->exchangeName);
        $channel->close();
    }
}
