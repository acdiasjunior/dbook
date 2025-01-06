<?php
declare (strict_types = 1);

namespace App\Service;

use Cake\Core\Configure;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private static ?RabbitMQService $instance = null;
    private ?AMQPStreamConnection $connection = null;
    private $channel;

    private function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            host: Configure::readOrFail('dbook.rabbitmq.host'),
            port: Configure::readOrFail('dbook.rabbitmq.port'),
            user: Configure::readOrFail('dbook.rabbitmq.user'),
            password: Configure::readOrFail('dbook.rabbitmq.pass'),
        );

        $this->channel = $this->connection->channel();
    }

    public static function getInstance(): RabbitMQService
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function sendToQueue(string $queue, array $data): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $message = new AMQPMessage(
            json_encode($data),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, '', $queue);
    }

    public function consumeQueue(string $queue, callable $callback): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
    }

    public function waitForMessages(): void
    {
        $this->channel->wait();
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }

        if ($this->connection) {
            $this->connection->close();
        }
    }
}
