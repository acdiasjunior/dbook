<?php
declare (strict_types = 1);

namespace App\Command;

use App\Service\RabbitMQService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
use PhpAmqpLib\Message\AMQPMessage;

class EmailWorkerCommand extends Command
{

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $rabbitMQService = RabbitMQService::getInstance();

        $timestamp = fn() => FrozenTime::now()->format('Y-m-d H:i:s');

        $callback = function (AMQPMessage $msg) use ($io, $timestamp) {
            $task = json_decode($msg->getBody(), true);

            $io->out("{$timestamp()}: Processing email task for: {$task['email']}");

            try {
                $mailer = new Mailer('default');
                $mailer->setTo($task['email'])
                    ->setSubject($task['subject'])
                    ->deliver($task['body']);

                $io->success("{$timestamp()}: Email sent to {$task['email']}");

                $msg->ack();
            } catch (\Exception $e) {
                $io->error("{$timestamp()}: Failed to send email to {$task['email']}: " . $e->getMessage());
            }
        };

        $rabbitMQService->consumeQueue('email_queue', $callback);

        $io->out("{$timestamp()}: Email worker is waiting for tasks. Press Ctrl+C to exit.");

        while (true) {
            $rabbitMQService->waitForMessages();
        }
    }

}
