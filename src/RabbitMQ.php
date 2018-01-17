<?php

namespace RabbitMq;

use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ
{
    private $connection;

    private $channel;

    public function __construct(
        string $host,
        string $port,
        string $user,
        string $password,
        string $vhost = '/'
    )
    {
        $this->connection = new AMQPLazyConnection(
            $host,
            $port,
            $user,
            $password,
            $vhost
        );
    }

    public function connect(): void
    {
        $this->channel = $this->connection->channel();
    }

    public function declareQueue(
        string $queue,
        bool $passive = false,
        bool $durable = true,
        bool $exclusive = false,
        bool $autoDelete = false
    ): void
    {
        $this->channel->queue_declare(
            $queue,
            $passive,
            $durable,
            $exclusive,
            $autoDelete
        );
    }

    public function addToQueue(string $queue, string $messageContents): void
    {
        $message = new AMQPMessage($messageContents, [
            'content_type' => 'text/plain',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);

        $this->channel->basic_publish($message, $queue);
    }
}