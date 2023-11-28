<?php

// Allow cross-origin resource sharing (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, API-Key");  // Add API-Key to the allowed headers

// Set error reporting and display_errors in development
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight requests
    header('HTTP/1.1 200 OK');
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once '../../main/wp-includes/web-contents.php';
    // Get the API key from the headers (case-insensitive check)
    $api_key = isset($_SERVER['HTTP_API_KEY']) ? $_SERVER['HTTP_API_KEY'] : null;

    // Check if the API key is included in the headers
    if ($api_key === null) {
        $response = array('status' => 'failed', 'message' => 'API not found');
    } else {
        // Remove special characters and spaces
        $api_key = preg_replace("/[^a-zA-Z0-9]/", '', $api_key);

        // Check if the API key is alphanumeric
        if (!ctype_alnum($api_key)) {
            $response = array('status' => 'failed', 'message' => 'Invalid characters in API Key');
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
                $response = array('status' => 'failed', 'message' => 'SQL error: ' . $conn->error);
            } else {
                $stmt->bind_param("s", $api_key);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows < 1) {
                    // API Key is Invalid
                    $response = array('status' => 'failed', 'message' => 'Invalid API Key');
                } else {
                    // Continue with the rest of your code
                    // Get the JSON request body
                    $requestBody = file_get_contents('php://input');

                    // Decode the JSON data
                    $requestData = json_decode($requestBody, true);

                    // Validate user input
                    $user_type = $requestData['user_type'];
                    $emailOrPhone = $requestData['emailOrPhone'];
                    $password = $requestData['password'];
                    $confirmPassword = $requestData['confirmPassword'];

                    // Validate input
                    $errors = [];

                    if (empty($emailOrPhone) || (empty($password) && empty($emailOrPhone)) || empty($user_type)) {
                        $errors[] = 'All fields are required.';
                    } else {
                        if ($password !== $confirmPassword) {
                            $errors[] = 'Password does not match';
                        }

                        // Check if both email and password are provided
                        if (!empty($emailOrPhone) && !empty($password)) {
                            // Use a prepared statement to prevent SQL injection
                            $query = "SELECT * FROM users WHERE email = ? OR phone = ?";
                            $stmt = $conn->prepare($query);

                            if (!$stmt) {
                                $response = array('status' => 'failed', 'message' => 'SQL error: ' . $conn->error);
                            } else {
                                $stmt->bind_param("ss", $emailOrPhone, $emailOrPhone);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $otp = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                                // Check if any rows are returned
                                if ($result->num_rows > 0) {
                                    // Email or phone number is already registered
                                    $response = array('status' => 'failed', 'message' => 'Email or phone number is already registered.');
                                } else {
                                    // Determine whether it's an email or phone registration
                                    if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                        $email = $emailOrPhone;
                                        $phone = null;
                                        $otp_method = 'email';
                                        $device = $emailOrPhone;

                                        $email_text = 'kindly click on the button below and verify your spacebank business account.';
                                        $email_subject = 'Email OTP Verification';
                                        $button_text = 'Verify Your Account';
                                        $button_link = $web_url . '/main/validate-otp?device=' . $device . '&otp=' . $otp;
                                        require_once('../email-script.php');
                                        require_once('../email.php');
                                    } else {
                                        $email = null;
                                        $phone = $emailOrPhone;
                                        $otp_method = 'whatsapp number';
                                        $device = $emailOrPhone;
                                        require_once('../chat.php');
                                    }

                                    // Hash the password
                                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                                    // Use a prepared statement to prevent SQL injection
                                    $insertQuery = "INSERT INTO users (email, phone, passwords, user_type, otp) VALUES (?, ?, ?, ?, ?)";
                                    $insertStmt = $conn->prepare($insertQuery);

                                    if (!$insertStmt) {
                                        $response = array('status' => 'failed', 'message' => 'SQL error: ' . $conn->error);
                                    } else {
                                        $insertStmt->bind_param("ssssi", $email, $phone, $hashedPassword, $user_type, $otp);

                                        if ($insertStmt->execute()) {
                                            $lastInsertedID = $conn->insert_id;

                                            if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                                $email = $emailOrPhone;
                                                $phone = null;
                                                $otp_method = 'email';
                                                $device = $emailOrPhone;

                                                $email_text = 'kindly click on the button below and verify your spacebank business account.';
                                                $email_subject = 'Email OTP Verification';
                                                $button_text = 'Verify Your Account';
                                                $button_link = $web_url . '/main/validate-otp?device=' . $device . '&otp=' . $otp;
                                                require_once('../email-script.php');
                                                require_once('../email.php');
                                            } else {
                                                $email = null;
                                                $phone = $emailOrPhone;
                                                $otp_method = 'whatsapp number';
                                                $device = $emailOrPhone;
                                                require_once('../chat.php');
                                            }

                                            $response = array('status' => 'success', 'message' => 'Check your ' . $email . ' for your verification code');
                                        } else {
                                            $response = array('status' => 'failed', 'message' => 'Registration failed');
                                        }

                                        $insertStmt->close(); // Close the insert statement
                                    }
                                }

                                // $stmt->close(); // Close the statement
                            }
                        }
                    }

                    // Handle errors
                    if (!empty($errors)) {
                        $response = array('status' => 'failed', 'message' => $errors);
                    }
                }

                $stmt->close(); // Close the statement
            }

            $conn->close(); // Close the connection
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle invalid request method (e.g., GET)
    header('HTTP/1.1 405 Method Not Allowed');
    $response = array('status' => 'failed', 'message' => 'Invalid request method');
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
