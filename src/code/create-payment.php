<?php
include '../../main/wp-includes/login-check.php';
include '../../main/wp-includes/web-contents.php';
include '../../main/wp-includes/profile-details.php';
// Check if email and passwords are provided in the POST request

if (isset($_POST['amount_from'])) {

    function generateRandomAlphanumericString($length = 16)
    {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }

    // Generate a 16-digit alphanumeric string
    $trans_hash = generateRandomAlphanumericString(16);

    $stored_hash = $_SESSION['trans_hash'] = $trans_hash;

    $amount_from = $_POST['amount_from'];
    $currency_to = $_POST['currency_to'];
    $currency_from = $_POST['currency_from'];
    $order_description = $_POST['order_description'];
    $sender_email = $_POST['sender_email'];
    $trans_hash = $stored_hash;
    $order_id = $_POST['order_id'];
    $ipn_callback_url = 'https://myspacebank.com';
}
if (empty($amount_from) || empty($currency_to)  || empty($currency_from) || empty($order_description) || empty($trans_hash) || empty($order_id)) {

    echo 'Please enter complete payment details';
    //exit();
}

// API endpoint URL
$apiUrl = '' . $web_url . '/api/payment/create-payment.php';

// Replace with your actual API key
$apiKey = $site_api_key;

// Create an associative array with the data to be sent in the POST request
$postData = array(
    'api_key' => $user_api_key,
    'price_amount' => $amount_from,
    'price_currency' => $currency_to,
    'pay_currency' => $currency_from,
    'order_description' => $order_description,
    'trans_hash' => $trans_hash,
    'order_id' => $order_id,
    'business_payer_email' => $sender_email,
    'ipn_callback_url' => $ipn_callback_url
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
$res = curl_exec($curl);

$curlResponse = json_decode($res);


// Function to check if "statuscode" exists within the JSON structure
function hasStatusCode($data)
{
    if (is_object($data)) {
        foreach ($data as $key => $value) {
            if ($key === 'status') {
                return true;
            } elseif (is_object($value) || is_array($value)) {
                if (hasStatusCode($value)) {
                    return true;
                }
            }
        }
    } elseif (is_array($data)) {
        foreach ($data as $item) {
            if (hasStatusCode($item)) {
                return true;
            }
        }
    }
    return false;
}

if (hasStatusCode($curlResponse)) {
    $res =  json_decode($res);
    echo $res->status;
    //echo 'success';
} else {
    $res =  json_decode($res);
    echo "successful";
    $curlResponse;
    //
}

// Close the cURL session
curl_close($curl);
