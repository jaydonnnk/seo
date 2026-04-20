<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

// Honeypot check
if (!empty($_POST['company'])) {
    echo 'Success';
    exit;
}

$name    = htmlspecialchars(trim($_POST['name']    ?? ''));
$email   = htmlspecialchars(trim($_POST['email']   ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

if (!$name || !$email || !$message) {
    echo 'Please fill in all required fields.';
    exit;
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo 'Invalid email address.';
    exit;
}

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require 'config.php';

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['email_user'];
    $mail->Password   = $config['email_pass'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('estoracore@gmail.com', 'EstoraCore Website');
    $mail->addAddress('estoracore@gmail.com');
    $mail->addReplyTo($email, $name);

    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    =
        "Name:    {$name}\n" .
        "Email:   {$email}\n\n" .
        "Message:\n{$message}";

    $mail->send();
    echo 'Success';
} catch (Exception $e) {
    echo 'Mailer error: ' . $mail->ErrorInfo;
}
