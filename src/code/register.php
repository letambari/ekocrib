<?php
include_once '../../main/wp-includes/web-contents.php';
// Check if email and passwords are provided in the POST request
if (isset($_POST['first-name']) && isset($_POST['last-name']) && isset($_POST['business-name']) && isset($_POST['display-name']) && isset($_POST['confirmpassword']) && isset($_POST['emailOrPhone']) && isset($_POST['password'])) {
    $first = $_POST['first-name'];
    $last = $_POST['last-name'];
    $business = $_POST['business-name'];
    $display = $_POST['display-name'];
    $emailOrPhone = $_POST['emailOrPhone'];
    $confirmpassword = $_POST['confirmpassword'];
    $password = $_POST['password'];

    // API endpoint URL
    $apiUrl = '' . $web_url . '/api/register.php';

    // Replace with your actual API key
    $apiKey = $site_api_key;

    // Create an associative array with the data to be sent in the POST request
    $postData = array(
        'api_key' => $apiKey,
        'first-name' => $first,
        'last-name' => $last,
        'business-name' => $business,
        'display-name' => $display,
        'emailOrPhone' => $emailOrPhone,
        'password' => $password,
        'confirmPassword' => $confirmpassword,
        'user-type' => "Small_business"
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
        //echo $response;
        $res =  json_decode($response);
        echo  $res->status->message;
        // echo $array['status'];
    }

    // Close the cURL session
    curl_close($curl);
} else {
    // Handle cases where email or passwords are not provided
    echo 'Email and passwords are required....';
}
