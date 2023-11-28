<?php
// Include the PHPMailer library

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
    $mail->addAddress($business_payer_email, 'Customer'); // Replace with the recipient's email and name
    $mail->Subject = $email_subject;

    // Set the email format to HTML
    $mail->isHTML(true);

    // Set your HTML email content
    $mail->Body = $invoice_email_message;

    // Send the email
    $mail->send();
    //echo 'Email sent successfully';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
}
?>
