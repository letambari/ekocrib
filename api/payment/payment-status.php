<?php

$response = array();
$statusCode = '';
$request_converted_currency = 'usd';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ... (Previous code for database connection and API key check)

    // Database configuration
    include_once '../../main/wp-includes/web-contents.php';

    // Get the JSON request body
    $requestBody = file_get_contents('php://input');

    // Decode the JSON data
    $requestData = json_decode($requestBody, true);
    $approved = "Approved";

    // Check if the API key is present in the decoded data
    if (isset($requestData['api_key'])) {
        $api_key = $requestData['api_key'];

        // Remove special characters and spaces
        $api_key = preg_replace("/[^a-zA-Z0-9]/", '', $api_key);

        // Check if the API key is alphanumeric
        if (!ctype_alnum($api_key)) {
            $response['status'] = 'Invalid characters in API Key';
        } else {
            // Create a database connection
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Use a prepared statement to prevent SQL injection
            $query = "SELECT * FROM users WHERE api_key = ?";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                $response['status'] = 'SQL error: ' . $conn->error;
            } else {
                $stmt->bind_param("s", $api_key);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows < 1) {
                    // API Key is Invalid
                    $response['status'] = 'API Key is Invalid';
                    $stmt->close(); // Close the statement here
                    $conn->close(); // Close the connection here
                    exit();
                } else {
                    // API Key is Valid
                    $row = $result->fetch_assoc();
                    $api_display_name = $row['display_name'];
                    $api_email = $row['email'];
                    $api_user_type = $row['user_type'];
                }

                $stmt->close(); // Close the statement here
            }

            // Close the connection
            //$conn->close();

            // Validate user input
            $request_payment_id = $requestData['payment_id']; // Use a different variable name

            // Validate input
            $errors = [];

            if (empty($request_payment_id)) {
                $errors[] = 'All fields are required.';
            } else {
                // API endpoint URL
                $apiUrl = '' . $now_web_url . '/payment/' . $request_payment_id;
                $apiKey = $now_api_key;

                $postData = array(
                    'x-api-key: ' . $apiKey
                );

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $apiUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => $postData,
                ));

                $res = curl_exec($curl);
                $curlResponse = json_decode($res);

                // Function to check if "statuscode" exists within the JSON structure
                function hasStatusCode($data)
                {
                    if (is_object($data)) {
                        foreach ($data as $key => $value) {
                            if ($key === 'statusCode') {
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
                    $response = $curlResponse;
                } else {
                    $payment_id = $curlResponse->payment_id;
                    $payment_status = $curlResponse->payment_status;
                    $pay_address = $curlResponse->pay_address;
                    $price_amount = $curlResponse->price_amount;
                    $price_currency = $curlResponse->price_currency;
                    $pay_amount = $curlResponse->pay_amount;
                    $pay_currency = $curlResponse->pay_currency;
                    $order_id = $curlResponse->order_id;
                    $order_description = $curlResponse->order_description;
                    $created_at = $curlResponse->created_at;
                    $updated_at = $curlResponse->updated_at;
                    $payin_extra_id = $curlResponse->payin_extra_id;
                    $purchase_id = $curlResponse->purchase_id;
                    $actually_paid = $curlResponse->actually_paid;
                    $outcome_amount = $curlResponse->outcome_amount;
                    $outcome_currency = $curlResponse->outcome_currency;
                    $type = $curlResponse->type;


                    // get the actual message that should come with the message.
                    if ($payment_status === 'finished') {
                        $payment_message = "The funds have reached your personal address and the payment is finished";
                    } elseif ($payment_status === 'partially_paid') {
                        $payment_message = "It shows that the customer sent the less than the actual price. Appears when the funds have arrived in your wallet";
                    } elseif ($payment_status === 'waiting') {
                        $payment_message = "waiting for the customer to send the payment. The initial status of each payment";
                    } elseif ($payment_status === 'confirming') {
                        $payment_message = "The transaction is being processed on the blockchain. Appears when NOWPayments detect the funds from the user on the blockchain";
                    } elseif ($payment_status === 'confirmed') {
                        $payment_message = "The process is confirmed by the blockchain. Customer’s funds have accumulated enough confirmations";
                    } elseif ($payment_status === 'sending') {
                        $payment_message = "The funds are being sent to your personal wallet. We are in the process of sending the funds to you";
                    } elseif ($payment_status === 'failed') {
                        $payment_message = "The payment wasn't completed due to the error of some kind";
                    } elseif ($payment_status === 'refunded') {
                        $payment_message = "The funds were refunded back to the user";
                    } elseif ($payment_status === 'refunded') {
                        $payment_message = "the user didn't send the funds to the specified address in the 7 days time window";
                    }
                    
                    $curlResponse->payment_message = $payment_message;
                        $curlResponse->outcome_usd = $estimated_amount;
                        $curlResponse->message = 'success';
                        $response = $curlResponse;
                }
            }
        }
    } else {
        // ... (Previous code for handling invalid request methods)
        $response['status'] = 'API Key is missing';
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle invalid request method (e.g., GET)
    header('HTTP/1.1 405 Method Not Allowed');
    $response['status'] = 'Invalid request method';
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
