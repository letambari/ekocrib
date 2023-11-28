<?php
include '../../main/wp-includes/web-contents.php';
// Check if email and passwords are provided in the POST request
if (isset($_POST['user_password'])) {
    $user_id = $_POST['user_id'];
    $user_password = $_POST['user_password'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];

    $transaction_pin = $a . '' . $b . '' . $c . '' . $d;

    // Convert the string to an integer
    $transaction_pin = intval($transaction_pin);

    // API endpoint URL
    $apiUrl = '' . $web_url . '/api/update-transaction-pin.php';

    // Replace with your actual API key
    $apiKey = $site_api_key;

    // Create an associative array with the data to be sent in the POST request
    $postData = array(
        'api_key' => $apiKey,
        'user_id' => $user_id,
        'transaction_pin' => $transaction_pin,
        'user_password' => $user_password
    );

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    $curlOptions = array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    );

    curl_setopt_array($curl, $curlOptions);

    // Execute the cURL request and store the response
    $response = curl_exec($curl);


    // Check if the cURL request was successful
    if ($response === false) {
        echo 'cURL error: ' . curl_error($curl);
    } else {
        // Output the API response
        $response;
        $res =  json_decode($response);
        echo  $res->status->message;
    }

    // Close the cURL session
    curl_close($curl);
} else {
    // Handle cases where email or passwords are not provided
    echo 'Email and passwords are required....';
}
