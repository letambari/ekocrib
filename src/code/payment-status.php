<?php

include '../../main/wp-includes/web-contents.php';


// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    respondWithError('Connection failed: ' . $conn->connect_error, 500);
}



// Check if email and passwords are provided in the POST request
if (isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];

    $query = "SELECT * FROM deposit WHERE payment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    function respondWithError($message, $statusCode)
    {
        http_response_code($statusCode);
        echo json_encode(array('error' => $message));
        exit;
    }

    // Fetch all currency values and store them in an array
    $deposits = array();
    while ($row = $result->fetch_assoc()) {
        // $deposits[] = $row;
        $order_id = $row['order_id'];
    }


    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('No Deposit', 404);
    }


    // Fetch all currency values and store them in an array
    $deposits = array();
    while ($row = $result->fetch_assoc()) {
        // $deposits[] = $row;

        $api_key = $row['api_key'];
    }

    // API endpoint URL
    $apiUrl = $web_url . '/api/payment/payment-status.php';

    // Create an associative array with the data to be sent in the POST request
    $postData = array(
        'api_key' => $api_key,
        'payment_id' => $payment_id
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
    //exit();
    // Check if the cURL request was successful
    if ($response === false) {
        echo 'cURL error: ' . curl_error($curl);
    } else {
        // Output the API response
        $res =  json_decode($response);
        echo $res->message;

        // session_start();
        // $user_id = $_SESSION['payment_id'] = $res->status->user_id;
    }

    // Close the cURL session
    curl_close($curl);
} else {
    // Handle cases where email or passwords are not provided
    echo 'Email and passwords are required.';
}
