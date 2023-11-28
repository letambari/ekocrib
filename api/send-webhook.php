<?php
// Target URL where you want to send the webhook
$webhookUrl = $webhook_endpoint;

                
// Payload data in JSON format
$payloadData = [
    'payment_id' => $payment_id,
    'payment_status' => $payment_status,
    'price_amount' => $price_amount,
    'price_currency' => $price_currency,
    'outcome_amount' => $outcome_amount,
    'outcome_currency' => $outcome_currency,
    'order_id' => $order_id,
    'actually_paid_at_fiat' => $actually_paid_at_fiat,
    'actually_paid' => $actually_paid
];

// Encode the payload as JSON
$payload = json_encode($payloadData);

// Set cURL options
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($payload)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors and handle them if needed
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
    error_log('cURL error: ' . curl_error($ch));
} else {
    // Handle the response from the webhook endpoint (if any)
    echo 'Webhook sent successfully. Response: ' . $response;
    error_log('Webhook sent successfully. Response: ' . $response);
}

// Close the cURL session
curl_close($ch);
?>
