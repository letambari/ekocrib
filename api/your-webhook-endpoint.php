<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $rawData = file_get_contents('php://input');

    // Parse the JSON data into an associative array
    $payloadData = json_decode($rawData, true);

    if ($payloadData !== null) {
        // Handle the data received from the webhook
        // You can process and store the data as needed
        
        // Access specific data fields by their keys
$payment_id = $payloadData['payment_id'];
$payment_status = $payloadData['payment_status'];
$price_amount = $payloadData['price_amount'];
$price_currency = $payloadData['price_currency'];
$outcome_amount = $payloadData['outcome_amount'];
$outcome_currency = $payloadData['outcome_currency'];
$order_id = $payloadData['order_id'];
$actually_paid_at_fiat = $payloadData['actually_paid_at_fiat'];
$actually_paid = $payloadData['actually_paid'];

        // For example, you can log it, save it to a database, or perform other actions.
        // Here, we'll simply print the data to the screen.
        $message = "Webhook data received:\n";
        //print_r($payloadData);
         error_log("Webhook data received:\n");
        
        require_once('test-webhook-mail.php');
    } else {
        // Failed to parse JSON data
        http_response_code(400); // Bad Request
        echo "Error: Invalid JSON data in the webhook request.";
         error_log("Error: Invalid JSON data in the webhook request.");
    }
} else {
    // Method not allowed
    http_response_code(405); // Method Not Allowed
    echo "Error: This endpoint only accepts POST requests.";
    error_log("Error: This endpoint only accepts POST requests.");
}
?>
