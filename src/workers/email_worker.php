<?php

require '../config/db.php';
require '../models/EmailModel.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// Open RabbitMQ connection
$connection = new AMQPStreamConnection(
    $_ENV['RABBITMQ_HOST'],
    $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USERNAME'],
    $_ENV['RABBITMQ_PASSWORD']
);

$channel = $connection->channel();
$channel->queue_declare('send_email_queue', false, false, false, false);

echo " [*] Email worker is running.\n";

$callback = function ($msg) {
    $email = new EmailModel();
    $email->execute(msg: $msg);
};


$channel->basic_consume('send_email_queue', '', false, true, false, false, $callback);

try {
    $channel->consume();
} catch (\Throwable $exception) {
    echo $exception->getMessage();
}
