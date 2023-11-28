<?php
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database configuration
    include_once '../main/wp-includes/web-contents.php';

    // Get the JSON request body
    $requestBody = file_get_contents('php://input');

    // Decode the JSON data
    $requestData = json_decode($requestBody, true);

    // Check if the API key is present in the decoded data
    if (isset($requestData['api_key'])) {
        $api_key = $requestData['api_key'];

        // Remove special characters and spaces
        $api_key = preg_replace("/[^a-zA-Z0-9]/", '', $api_key);

        // Check if the API key is alphanumeric
        if (!ctype_alnum($api_key)) {
            $response['status'] = array(
                'message' => 'Invalid characters in API Key',
            );
        } else {
            // Create a database connection
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Validate user input
            $user_id = $requestData['user_id'];
            $transaction_pin = $requestData['transaction_pin'];
            $user_password = $requestData['user_password'];

            // Validate input
            $errors = [];

            if (empty($user_password) || empty($user_id) || empty($transaction_pin)) {
                $errors[] = 'All fields are required.';
            } else {
                // Use a prepared statement to prevent SQL injection
                $query = "SELECT * FROM users WHERE api_key = ?";
                $stmt = $conn->prepare($query);

                if (!$stmt) {
                    $response['status'] = 'SQL error: ' . $conn->error;
                } else {
                    $stmt->bind_param("s", $api_key);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows < 0) {
                        // API Key is Invalid
                        $response['status'] = array(
                            'message' => 'API Key is missing',
                        );
                        $stmt->close();
                        $conn->close();
                        exit();
                    } else {
                        // API Key is Valid
                        $row = $result->fetch_assoc();
                        $api_display_name = $row['display_name'];
                        $api_email = $row['email'];
                        $api_user_type = $row['user_type'];
                    }

                    $stmt->close();
                }

                // Check the user's balance and perform withdrawal
                $is_verified = 'email_verified';
                $query = "SELECT * FROM users WHERE id = ? AND is_verified = ?";
                $stmt = $conn->prepare($query);

                if (!$stmt) {
                    $response['status'] = 'SQL error: ' . $conn->error;
                } else {
                    $stmt->bind_param("is", $user_id, $is_verified);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $user_id = $row['id'];
                        $balance = $row['balance'];
                        $email = $row['email'];
                        $passwords = $row['passwords'];
                        $database_transaction_pin = $row['transaction_pin'];
                        $business_name = $row['business_name'];
                        $first_name = $row['first_name'];
                        $last_name = $row['last_name'];




                        if (password_verify($user_password, $passwords)) {

                            if ($email != "") {
                                $emailOrPhone = $email;
                            } else {
                                $emailOrPhone = $phone;
                            }

                            if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                $email = $emailOrPhone;
                                $phone = null;
                                $otp_method = 'email_verified';
                                $device = 'email';
                            } else {
                                $email = null;
                                $phone = $emailOrPhone;
                                $otp_method = 'phone_verified';
                                $device = 'phone';
                            }
                            // Perform the withdrawal and update the database
                            $updateQuery = "UPDATE users SET transaction_pin = ? WHERE id = ?";
                            $updateStmt = $conn->prepare($updateQuery);
                            $email_text = $msg = $business_name . ', Your transaction pin has been updated, if this is not authorized by you, kindly contact our support.';
                                    $email_subject = 'Transaction Pin';
                                    $button_text = 'Login to your account';
                                    $button_link = 'https://spacebank.thalajaatdatabase.online/main/transaction-pin';

                            if (!$updateStmt) {
                                $response['status'] = 'SQL error: ' . $conn->error;
                            } else {
                                $updateStmt->bind_param("ii", $transaction_pin, $user_id);
                                $updateStmt->execute();
                                $updateStmt->close();

                                // Handle withdrawal notification based on email or phone
                                if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                    $email = $emailOrPhone;
                                    $phone = null;
                                    $otp_method = 'email';
                                    $device = $emailOrPhone;
                                    require_once('email-script.php');
                                require_once('email.php');
                                } else {
                                    $email = null;
                                    $phone = $emailOrPhone;
                                    $otp_method = 'whatsapp number';
                                    $device = $emailOrPhone;
                                    $subject = 'Transaction Pin';
                                    $msg = "Your transaction pin has been updated, if this is not authorized by you, kindly contact our support.";
                                    require_once('chat_profile_update.php');
                                }

                                $response['status'] = array(
                                    'message' => 'success',
                                );
                            }
                        } else {

                            $response['status'] = array(
                                'message' => 'Invalid password',
                            );
                        }
                    } else {
                        $response['status'] = array(
                            'message' => 'Update not Successful',
                        );
                    }

                    $stmt->close();
                }

                $conn->close();
            }

            // Handle errors
            if (!empty($errors)) {
                $response = array('errors' => $errors);
            }
        }
    } else {
        // Handle the case when API key is missing in the request
        $response['status'] = array(
            'message' => 'API Key is missing',
        );
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
