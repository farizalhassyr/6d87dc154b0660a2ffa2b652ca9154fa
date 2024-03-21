<?php

define("PROJECT_ROOT_PATH", __DIR__ . "/../");
require_once '../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailModel
{
    // Function to insert email into DB
    private function insert($emailAttributes = [])
    {
        global $pdo;

        $query = $pdo->prepare(
            "INSERT INTO emails (sender, recepient, subject, message)  
                    VALUES ('$emailAttributes[sender]','$emailAttributes[recepient]','$emailAttributes[subject]','$emailAttributes[message]' )"
        );

        try {
            // execute query
            $query->execute();
            // error handler
        } catch (PDOException $e) {
            $errorInfo = $e->errorInfo;
            print_r($errorInfo);
        }
    }

    // Function to send email to the recepient
    private function send($emailAttributes = [])
    {
        $recepient = $emailAttributes["recepient"];
        $subject = $emailAttributes["subject"];
        $message = $emailAttributes["message"];

        $senderEmail = $_ENV['MAIL_USERNAME'];
        $senderName = $_ENV['MAIL_NAME'];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Sender information
            $mail->setFrom($senderEmail, $senderName);
            $mail->addReplyTo($senderEmail, $senderName);

            // Recipient
            $mail->addAddress($recepient);

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = "<h2>$message</h2>";
            // Send email
            $mail->send();
        } catch (Exception) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    // Function to run both insert and send methods
    public function execute($msg)
    {
        // normalize $msg->body parameter
        $emailAttributes = json_decode($msg->body, true);

        $this->insert(emailAttributes: $emailAttributes);
        $this->send(emailAttributes: $emailAttributes);
    }
}
