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
    $mail->Host = 'server327.web-hosting.com'; // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'ekocrib@thalajaatdatabase.online'; // Replace with your SMTP username
    $mail->Password = '@ekocrib.com'; // Replace with your SMTP password
    $mail->SMTPSecure = 'ssl'; // You can also use 'ssl' if needed
    $mail->Port = 465; // Check your SMTP server's documentation for the correct port

    // Configure email content
    $mail->setFrom('ekocrib@thalajaatdatabase.online', 'Spacebank Business'); // Replace with your email and name
    $mail->addAddress($email, 'Users'); // Replace with the recipient's email and name
    $mail->Subject = 'Spacebank Business - Webhook Transaction Notification';
    $mail->Body = 'Hello  here is the transaction_id: '.$payment_id.', and it was '.$payment_status.'';

    // Send the email
    $mail->send();
    
    //echo 'Email sent successfully';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
}
