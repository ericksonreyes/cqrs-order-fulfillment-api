<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQEventSubscriber
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var string
     */
    private $exchangeName;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $queue;

    /**
     * RabbitMQEventSubscriber constructor.
     * @param AMQPStreamConnection $connection
     * @param string $exchangeName
     */
    public function __construct(
        AMQPStreamConnection $connection,
        string $exchangeName
    )
    {
        $this->connection = $connection;
        $this->exchangeName = $exchangeName;
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @param string $queue
     */
    public function setQueue(string $queue): void
    {
        $this->queue = $queue;
    }


    /**
     * @throws \ErrorException
     */
    public function listen(): void
    {

        $isDurable = true;
        $queue = $this->queue ?? '';
        $isPassive = false;
        $consumerTag = '';
        $isAutoDelete = false;
        $isExclusive = false;
        $noAck = false;

        $channel = $this->connection->channel();
        $channel->exchange_declare(
            $this->exchangeName,
            'fanout',
            $isPassive,
            $isDurable,
            $isAutoDelete
        );

        [$queue_name, ,] = $channel->queue_declare(
            $queue,
            $isPassive,
            $isDurable,
            $isExclusive,
            $isAutoDelete
        );

        $channel->queue_bind($queue_name, $this->exchangeName);
        $channel->basic_consume(
            $queue_name,
            $consumerTag,
            false,
            $noAck,
            $isExclusive,
            false,
            $this->callback
        );

        while (count($channel->callbacks)) {
            $allowedMethods = null;
            $nonBlocking = false;
            $timeout = 0;
            $channel->wait($allowedMethods, $nonBlocking, $timeout);
        }

        $channel->close();
        $this->connection->reconnect();
    }
}
