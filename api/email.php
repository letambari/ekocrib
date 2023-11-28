<?php
// Include the PHPMailer library
require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';
require 'PHPMailer/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Configure the SMTP settings
    $mail->isSMTP();
    $mail->Host = $server_host; // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = $server_username; // Replace with your SMTP username
    $mail->Password = $server_password; // Replace with your SMTP password
    $mail->SMTPSecure = $server_ssl; // You can also use 'ssl' if needed
    $mail->Port = $email_port; // Check your SMTP server's documentation for the correct port

    // Configure email content
    $mail->setFrom($server_username, 'Spacebank'); // Replace with your email and name
    $mail->addAddress($email, 'Ekocrib'); // Replace with the recipient's email and name
    $mail->Subject = $email_subject;

    // Set the email format to HTML
    $mail->isHTML(true);

    // Set your HTML email content
    $mail->Body = $email_message;

    // Send the email
    $mail->send();
    //echo 'Email sent successfully';
} catch (Exception $e) {
     error_log('Email could not be sent. Error: ' . $mail->ErrorInfo);
}
?>
