<!DOCTYPE html>
<html lang="en">

<?php
require '../vendor/autoload.php';
session_start();
// Set code into session once login. To prevent relogin when 'code' parameter is not included
if (isset($_GET['code'])) {
    $_SESSION['code'] = $_GET['code'];
}
?>

<head>
    <meta charset="UTF-8">
    <title>Email Form</title>
</head>

<body>
    <h1>Send a Message</h1>
    <form action="../methods/send_email.php" method="post">
        <label for="recepientEmail">Recepient's Email:</label>
        <input type="recepientEmail" name="recepientEmail" id="recepientEmail" required>
        <br><br>
        <label for="subject">Subject:</label>
        <input type="subject" name="subject" id="subject" required>
        <br><br>
        <label for="message">Message:</label>
        <textarea name="message" id="message" rows="5" required></textarea>
        <br><br>
        <button type="submit">Submit</button>
    </form>
</body>

</html>