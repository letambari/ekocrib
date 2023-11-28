<?php
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../../main/wp-includes/web-contents.php';

    $requestBody = file_get_contents('php://input');
    $requestData = json_decode($requestBody, true);

    if (!isset($requestData['api_key'])) {
        $response['status'] = 'API Key is missing';
        sendResponse($response);
    }

    $api_key = $requestData['api_key'];
    $api_key = preg_replace("/[^a-zA-Z0-9]/", '', $api_key);

    if (!ctype_alnum($api_key)) {
        $response['status'] = 'Invalid characters in API Key';
        sendResponse($response);
    }

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        $response['status'] = 'Connection failed: ' . $conn->connect_error;
        sendResponse($response);
    }

    $query = "SELECT * FROM users WHERE api_key = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        $response['status'] = 'SQL error: ' . $conn->error;
        sendResponse($response);
    }

    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        $response['status'] = 'API Key is Invalid';
        $stmt->close();
        $conn->close();
        sendResponse($response);
    } else {
        $row = $result->fetch_assoc();
        $api_display_name = $row['display_name'];
        $api_email = $row['email'];
        $api_user_type = $row['user_type'];
        $stmt->close();
    }

    $requiredFields = ['price_amount', 'price_currency', 'pay_currency', 'ipn_callback_url', 'order_id', 'order_description', 'business_payer_email', 'trans_hash'];
    $errors = [];

    // Validate the required fields
    foreach ($requiredFields as $field) {
        if (empty($requestData[$field])) {
            $errors[] = 'Field "' . $field . '" is required.';
        }
    }

    // Check if there are any errors
    if (!empty($errors)) {
        // Handle errors and send a response
        $response['errors'] = $errors;
        sendResponse($response);
        exit();
    }


    $business_payer_email = $requestData['business_payer_email'];
    $trans_hash = $requestData['trans_hash'];
    $outcome_amount = $requestData['price_amount'];
    $outcome_currency = $requestData['price_currency'];
    $request_converted_currency = $requestData['pay_currency'];

    include 'payment-convertion.php';

    $price_amount = $estimated_amount_in_asking_currency;

    // Now, you can access the optional fields

    // echo $price_amount;
    // exit();

    $url = '' . $now_web_url . '/payment';
    $pay_api_key = $now_api_key;
    $data = [
        "price_amount" => $price_amount,
        "price_currency" => $requestData['price_currency'],
        "pay_currency" => $requestData['pay_currency'],
        "ipn_callback_url" => $web_url . '/api/callback.php',
        "order_id" => $requestData['order_id'],
        "order_description" => $requestData['order_description']
    ];

    $headers = [
        'Content-Type: application/json',
        'x-api-key: ' . $pay_api_key
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $curlResponse = curl_exec($curl);


    if (curl_error($curl)) {
        $response['status'] = 'cURL Error: ' . curl_error($curl);
        sendResponse($response);
    }

    $curlResponse = json_decode($curlResponse);

    if ($curlResponse) {
        $payment_id = $curlResponse->payment_id;
        $payment_status = $curlResponse->payment_status;
        $pay_address = $curlResponse->pay_address;
        $price_amount = $curlResponse->price_amount;
        $price_currency = $curlResponse->price_currency;
        $pay_amount = $curlResponse->pay_amount;
        $pay_currency = $curlResponse->pay_currency;
        $order_id = $curlResponse->order_id;
        $order_description = $curlResponse->order_description;
        $ipn_callback_url = $curlResponse->ipn_callback_url;
        $created_at = $curlResponse->created_at;
        $updated_at = $curlResponse->updated_at;
        $payin_extra_id = $curlResponse->payin_extra_id;
        $purchase_id = $curlResponse->purchase_id;

        require_once "../phpqrcode/phpqrcode/qrlib.php";
        $localDirectory = $web_url . '/api/phpqrcode/qrcode_image/';
        // QR code configuration
        $url = $pay_address;
        $errorCorrectionLevel = QR_ECLEVEL_M; // error correction level (L, M, Q, H)
        $matrixPointSize = 10; // QR code size in pixels
        $margin = 4; // QR code margin in modules
        $imageName = $payment_id . '-' . $order_id . "qrcode.png";
        // Generate QR code image and save to file
        $filename = "../phpqrcode/qrcode_image/" . $imageName . "";
        QRcode::png($url, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);
        $qrcode_address_url = $localDirectory . '' . $imageName;

        $sql = "INSERT INTO deposit (payment_id, business_payer_email, payment_status, pay_address, price_amount, price_currency, pay_amount, actually_paid, pay_currency, order_id, order_description, purchase_id, created_at, updated_at, outcome_amount, outcome_currency, trans_hash, qrcode_address_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssssssssssssssss", $payment_id, $business_payer_email, $payment_status, $pay_address, $price_amount, $price_currency, $pay_amount, $actually_paid, $pay_currency, $order_id, $order_description, $purchase_id, $created_at, $updated_at, $outcome_amount, $outcome_currency, $requestData['trans_hash'], $qrcode_address_url);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            //mysqli_close($conn);

            $curlResponse->qrcode = $qrcode_address_url;
            
            
            
            $query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // $deposits[] = $row;
    $business_name = $row['business_name'];
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
    $user_type = $row['user_type'];
    
}
            
            $dateString = $created_at;

                    // Create a DateTime object from the provided string
                    $date = new DateTime($dateString);
                    
                    // Format the date as desired
                    $created_at = $date->format('F j, Y');
            
             $email_text = $business_name . ', Your Payment invoice of $' . $price_amount . ' was created successfully, you are welcome. <br> kindly contact our support if you did not initiate this transaction';
                                    $email_subject = $subject = 'Payment Invoice';
                                    $button_text = 'Click Here to view Invoice';
                                    $button_link = 'https://spacebank.thalajaatdatabase.online/main/payment-portal/?trans_hash='.$trans_hash;
            
             require_once('../email-script.php');
            require_once('../email.php');
            
                $invoice_email_text = $business_payer_email . ', a payment invoice totaling $' . $price_amount . ' has been generated for you on ' . $created_at . ' by '.$first_name.' '.$last_name.'  from '.$business_name.'. <br>If you are not familiar with this transaction, please reach out to our support team';
                                    $invoice_email_subject = $subject = 'Payment Invoice';
                                    $invoice_button_text = 'Click Here to view Invoice';
                                    $invoice_button_link = 'https://spacebank.thalajaatdatabase.online/main/payment-portal/?trans_hash='.$trans_hash;
            
            require_once('../invoice-email-script.php');
            require_once('../invoice-email.php');

            sendResponse($curlResponse);
            
            
        } else {
            $response['errors'] = "Error inserting record: " . mysqli_error($conn);
            sendResponse($response);
        }
    }

    // curl_close($curl);
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    $response['status'] = 'Invalid request method';
    sendResponse($response);
}

function sendResponse($response)
{
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
