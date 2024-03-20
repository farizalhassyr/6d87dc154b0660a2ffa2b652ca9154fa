<?php
require '../vendor/autoload.php';
require 'fetch_user.php';
include '../models/EmailModel.php';

$dotenv = Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $recepientEmail = $_POST["recepientEmail"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];

    $senderEmail = $_ENV['MAIL_USERNAME'];
    $senderName = $_ENV['MAIL_NAME'];

    $emailAttributes = [
        'recepient' => $recepientEmail,
        'sender' => $senderEmail,
        'subject' => $subject,
        'message' => $message
    ];

    $homepageUrl = $_ENV['BASE_URL'];
    $emailFormPage = $_ENV['BASE_URL'] . 'forms/mail_form.php';

    if (!isset($_SESSION['user'])) {
        print_r($_SESSION);
        alert("Please login first.");

        // Back to email form page
        echo "<script>window.location.href='$homepageUrl';</script>";
    } else {
        // Open RabbitMQ connection
        $connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USERNAME'],
            $_ENV['RABBITMQ_PASSWORD']
        );

        $channel = $connection->channel();
        $channel->queue_declare('send_email_queue', false, false, false, false);
        // Encode $emailAttributes into JSON and store into $msg
        $msg = new AMQPMessage(json_encode($emailAttributes));
        // Publish queue
        $channel->basic_publish($msg, '', 'send_email_queue');

        $channel->close();
        $connection->close();
        alert("Email is being sent");

        // Back to email form page
        echo "<script>window.location.href='$emailFormPage';</script>";
    }
}

function alert($msg)
{
    echo "<script type='text/javascript'>alert('$msg');</script>";
}
