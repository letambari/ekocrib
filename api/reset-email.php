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
    $mail->Host = 'server327.web-hosting.com'; // Replace with your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'ekocrib@thalajaatdatabase.online'; // Replace with your SMTP username
    $mail->Password = '@ekocrib.com'; // Replace with your SMTP password
    $mail->SMTPSecure = 'ssl'; // You can also use 'ssl' if needed
    $mail->Port = 465; // Check your SMTP server's documentation for the correct port

    // Configure email content
    $mail->setFrom('ekocrib@thalajaatdatabase.online', 'Spcebank Business'); // Replace with your email and name
    $mail->addAddress($email, 'Spacebank'); // Replace with the recipient's email and name
    $mail->Subject = 'Reset Password OTP Verification';
    $mail->Body = 'Hello, reset verification code is: ' . $otp . '. or verify using the link ' . $web_url . '/main/reset-password?device=' . $device . '&otp=' . $otp . '';

    // Send the email
    $mail->send();
    //echo 'Email sent successfully';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ' . $mail->ErrorInfo;
}
