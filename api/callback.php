<?php

// Your IPN Secret Key
$ipn_secret = 'gxuG/ywEWlLPOCdiVOo49sWnKl+CrLGv';
$webhook_endpoint = '';
// Log initialization message
error_log("IPN Listener has started and is waiting for data.");
// Function to check the validity of an IPN request
function check_ipn_request_is_valid($ipn_secret)
{
        include_once '../main/wp-includes/web-contents.php';
        
    $error_msg = "Unknown error";
    $auth_ok = false;
    $request_data = null;
    if (isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) && !empty($_SERVER['HTTP_X_NOWPAYMENTS_SIG'])) {
        $received_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];
        $request_json = file_get_contents('php://input');
        $request_data = json_decode($request_json, true);
        ksort($request_data);
        $sorted_request_json = json_encode($request_data, JSON_UNESCAPED_SLASHES);
        if ($request_json !== false && !empty($request_json)) {
            $hmac = hash_hmac("sha512", $sorted_request_json, "gxuG/ywEWlLPOCdiVOo49sWnKl+CrLGv");
            if ($hmac != $received_hmac) {
                // HMAC signature is valid
                $auth_ok = true;
                // Output a success message
                echo "Successful, IPN connected and waiting for payload data";
                error_log('Successful, IPN connected and waiting for payload data');
                 // Access the payload data from the request JSON
                $payload_data = $request_data;
                $payment_id = $payload_data['payment_id'];
                $payment_status = $payload_data['payment_status'];
                $price_amount = $payload_data['price_amount'];
                $price_currency = $payload_data['price_currency'];
                $outcome_amount = $payload_data['outcome_amount'];
                $outcome_currency = $payload_data['outcome_currency'];
                $request_converted_currency = 'usd';
                $order_id =  $payload_data['order_id'];
                $actually_paid_at_fiat =  $payload_data['actually_paid_at_fiat'];
                $actually_paid = $payload_data['actually_paid'];
                $order_description = $payload_data['order_description'];
                
                    // Database connection parameters
                    $servername = "localhost";  // Replace with your server name
                    $username = "thaltgtj_spacebank";  // Replace with your MySQL username
                    $password = "thaltgtj_spacebank";  // Replace with your MySQL password
                    $database = "thaltgtj_spacebank";  // Replace with your database name
                    
                    // Create a database connection
                    $conn = new mysqli($servername, $username, $password, $database);
                    
                    // Check the database connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    
                     $query = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $order_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                     $row = $result->fetch_assoc();
                    $businessemail = $row['email'];
                    $business_name = $row['business_name'];
                    $webhook_endpoint = $row['webhook_endpoint'];
                    $button_link = 'https://spacebank.thalajaatdatabase.online/main/deposit';
                    
                    // Payment ID you want to check
                    $payment_id = $payment_id; // Replace with the actual payment ID you want to check
                    
                    // SELECT query to check if the payment ID exists in the 'deposit' table
                    $query = "SELECT * FROM deposit WHERE payment_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $payment_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows < 1) {
                        
                        // The payment ID does not exist in the 'deposit' table
                      $payload_response = 'Payment ID is not valid.';
                      exit();
                        
                    } else {
                                              // The payment ID exists in the 'deposit' table
                    $row = $result->fetch_assoc();
                    $business_payer_email = $row['business_payer_email'];
                    $created_at = $row['created_at'];
                    
                   $dateString = $created_at;

                    // Create a DateTime object from the provided string
                    $date = new DateTime($dateString);
                    
                    // Format the date as desired
                    $created_at = $date->format('F j, Y');
                                              
                        $payload_response = 'Payment ID is valid.';
          
                    }
                    require_once('call-back-script.php');
              
              require_once('payment-email-script.php');
              
              require_once('webhook_listener.php');
              
              require_once('send-webhook.php');
               
            } else {
                // HMAC signature does not match
                error_log('HMAC signature does not match');
                 error_log('recieved'.$received_hmac);
                 error_log('hmac'.$hmac);
                echo "HMAC signature does not match"; // Output a message indicating the issue
            }
        } else {
            error_log('Error reading POST data');
            echo "Error reading POST data"; // Output a message indicating the issue
        }
    } else {
        error_log('No HMAC signature sent.');
        echo "No HMAC signature sent"; // Output a message indicating the issue
    }

    // Return the authentication status
    return $auth_ok;
}

// Your IPN secret key
$ipn_secret =  'gxuG/ywEWlLPOCdiVOo49sWnKl+CrLGv';

// Call the IPN handling function
$isValid = check_ipn_request_is_valid($ipn_secret);

if ($isValid) {
    // Valid IPN request
    // Handle the IPN data and update your database
    error_log('IPN request is valid');
} else {
    // Invalid IPN request
    // Handle the error
    error_log('IPN request is invalid');
}
