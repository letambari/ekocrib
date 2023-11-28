<?php
// Include the Twilio PHP SDK
require_once 'vendor/autoload.php';

// Your Twilio Account SID and Auth Token
$accountSid = 'AC2f9828e339bb0e6d455ed90a5c3f7161';
$authToken = '44a4f8663d08aed2b9b220a3159bf9f7';

// Create a Twilio client
$client = new Twilio\Rest\Client($accountSid, $authToken);

// Recipient's WhatsApp number (include the country code, e.g., +1234567890)
$to = 'whatsapp:+234' . $phone . '';

// Sender's Twilio WhatsApp number (must be preconfigured in your Twilio account)
$from = 'whatsapp:+14155238886';

// Message content
$message = 'Hello, your reset verification code is: ' . $otp . '. or verify using the link ' . $web_url . '/main/reset-password?device=' . $device . '&otp=' . $otp . '';

try {
    // Send the WhatsApp message
    $client->messages->create(
        $to,
        [
            'from' => $from,
            'body' => $message,
        ]
    );

    //echo 'WhatsApp message sent successfully!';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
