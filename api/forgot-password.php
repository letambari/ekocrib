<?php
$response = array();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database configuration
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

            $response = array();
            $response['status'] = array(
                'message' => 'Invalid characters in API Key',
                'email' => '',
                // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
            );
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

                if ($result->num_rows < 0) {
                    // API Key is In-valid
                    $response = array();
                    $response['status'] = array(
                        'message' => 'API Key is Invalid',
                        'email' => '',
                        // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
                    );
                    $stmt->close(); // Close the statement here
                    $conn->close(); // Close the connection here
                    exit();
                } else {
                    // API Key is Valid
                    $row = $result->fetch_assoc();
                    $api_display_name = $row['display_name'];
                    $api_email = $row['email'];
                }

                $stmt->close(); // Close the statement here
            }

            $conn->close(); // Close the connection here

            // Validate user input

            $emailOrPhone = $requestData['emailOrPhone'];

            // Validate input
            $errors = [];
            //|| ($first_name) || ($last_name) || ($business_name) || ($display_name)
            if (empty($emailOrPhone)) {
                $errors[] = 'All fields are required.';
            } else {

                // Check if both email and password are provided
                if (!empty($emailOrPhone)) {
                    // Create a new database connection
                    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                    // Check the connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $query = "SELECT * FROM users WHERE email = ? OR phone = ?";
                    $stmt = $conn->prepare($query);

                    if (!$stmt) {
                        $response['status'] = 'SQL error: ' . $conn->error;
                    } else {
                        $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Check if any rows are returned
                        if ($result->num_rows < 1) {
                            // Email or phone number is already registered
                            //$response['status'] = 'Email or phone number does not exist.';
                            $response = array();
                            $response['status'] = array(
                                'message' => 'Email or phone number does not exist.',
                                'email' => 'please try again',
                                // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
                            );
                        } else {
                            // Determine whether it's an email or phone registration
                            if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                $email = $emailOrPhone;
                                $phone = null;
                            } else {
                                $email = null;
                                $phone = $emailOrPhone;
                            }

                            // Define these variables before binding
                            $username = ''; // You need to set this based on user input
                            $email = $email;    // You need to set this based on user input
                            $phone = $phone;    // You need to set this based on user input

                            $otp = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);


                            $insertQuery = "UPDATE users SET otp = ? WHERE email = ?";
                            $insertStmt = $conn->prepare($insertQuery);

                            if (!$insertStmt) {
                                $response['status'] = 'SQL error: ' . $conn->error;
                            } else {
                                $insertStmt->bind_param("is", $otp, $emailOrPhone);
                                if ($insertStmt->execute()) {
                                    $lastInsertedID = $conn->insert_id;


                                    // Determine whether it's an email or phone registration
                                    if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                        $email = $emailOrPhone;
                                        $phone = null;
                                        $otp_method = 'email';
                                        $device = $emailOrPhone;
                                        require_once('reset-email.php');
                                    } else {
                                        $email = null;
                                        $phone = $emailOrPhone;
                                        $otp_method = 'whatsapp number';
                                        $device = $emailOrPhone;
                                        require_once('reset-chat.php');
                                    }



                                    $response = array();
                                    $response['status'] = array(
                                        'message' => 'Reset successful.',
                                        'email' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code',
                                        // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
                                    );

                                    // $response['status'] = 'Registration successful.';
                                    // $response['status'] = 'Check your whatsapp number ' . $phone . ' for your verification code.';
                                } else {
                                    //$response['status'] = 'Reset Password failed.';
                                    $response = array();
                                    $response['status'] = array(
                                        'message' => 'Reset Password failed.',
                                        'email' => 'please try again',
                                        // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
                                    );
                                }

                                $insertStmt->close(); // Close the insert statement
                            }
                        }

                        $conn->close(); // Close the connection
                    }
                }
            }

            // Handle errors
            if (!empty($errors)) {
                $response = array('errors' => $errors);
            }
        }
    } else {
        // Handle the case when API key is missing in the request

        $response = array();
        $response['status'] = array(
            'message' => 'API Key is missing',
            'email' => '',
            // 'verification_link' => 'http://localhost/spacebank-gateway/main/reset-password.php?device=' . $device . '&otp=' . $otp . ''
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
