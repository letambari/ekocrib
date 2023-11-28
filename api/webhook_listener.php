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
    $email = 'innocentdestiny228@gmail.com';
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
    $mail->addAddress($email, $businessemail,  $business_name); // Replace with the recipient's email and name
    $mail->Subject = $mail->Subject = 'Spacebank Business - Transaction Notification';

    // Set the email format to HTML
    $mail->isHTML(true);
    $mail->Body = $payment_messages;

    // Send the email
    $mail->send();
    
    
    
        // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    $email = $business_payer_email;
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
    $mail->Subject = 'Spacebank Business - Transaction Notification';
    
    $mail->isHTML(true);
    $mail->Body = $payment_messages;

    // Send the email
    $mail->send();
    //echo 'Email sent successfully';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
}
