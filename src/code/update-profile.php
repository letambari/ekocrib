<?php
include '../../main/wp-includes/web-contents.php';
// Check if email and passwords are provided in the POST request
if (isset($_POST['emailOrPhone'])) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $business = $_POST['business_name'];
    $display = $_POST['display_name'];
    $emailOrPhone = $_POST['emailOrPhone'];
    $about = $_POST['about'];
    $country = $_POST['country'];
    $acct_name = $_POST['acct_name'];
    $acct_number = $_POST['acct_number'];
    $acct_bank = $_POST['acct_bank'];
    $website = $_POST['website'];

    // API endpoint URL
    $apiUrl = '' . $web_url . '/api/update-profile.php';

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
        'about' => $about,
        'country' => $country,
        'acct_name' => $acct_name,
        'acct_number' => $acct_number,
        'acct_bank' => $acct_bank,
        'website' => $website,
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
        // echo $response;
        $res =  json_decode($response);
        echo  $res->status->message;
    }

    // Close the cURL session
    curl_close($curl);
} else {
    // Handle cases where email or passwords are not provided
    echo 'Email and passwords are required....';
}
