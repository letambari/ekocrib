<?php
include_once '../../main/wp-includes/web-contents.php';
// Check if email and passwords are provided in the POST request
if (isset($_POST['emailORphone']) && isset($_POST['password'])) {
    $emailORphone = $_POST['emailORphone'];
    $password = $_POST['password'];

    // API endpoint URL
    $apiUrl = '' . $web_url . '/api/payment/get-currency.php';

    // Replace with your actual API key
    $apiKey = $site_api_key;

    // Create an associative array with the data to be sent in the POST request
    $postData = array(
        'api_key' => $apiKey,
        'emailOrPhone' => $emailORphone,
        'password' => $password
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
        CURLOPT_CUSTOMREQUEST => 'GET',
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
        $res =  json_decode($response);
        echo $res->status->message;

        session_start();
        $user_id = $_SESSION['user_id'] = $res->status->user_id;
    }

    // Close the cURL session
    curl_close($curl);
} else {
    // Handle cases where email or passwords are not provided
    echo 'Email and passwords are required.';
}
