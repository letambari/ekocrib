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
                    // Get the JSON request body
                    $requestBody = file_get_contents('php://input');

                    // Decode the JSON data
                    $requestData = json_decode($requestBody, true);

                    // Validate user input
                    $emailOrPhone = $requestData['emailOrPhone'];
                    $otp = $requestData['otp'];

                    // Validate input
                    $errors = [];

                    if (empty($emailOrPhone) || empty($otp)) {
                        $errors[] = 'All fields are required.';
                    } else {
                        // Create a new database connection
                        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                        // Check the connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $is_verified = 'not_verified';
                        $query = "SELECT * FROM users WHERE email = ? or phone = ? AND otp = ? AND is_verified = ?";
                        $stmt = $conn->prepare($query);

                        if (!$stmt) {
                            $response['status'] = 'SQL error: ' . $conn->error;
                        } else {
                            $stmt->bind_param("ssis", $emailOrPhone, $emailOrPhone, $otp, $is_verified);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Check if the OTP is valid
                            if ($result->num_rows > 0) {

                                $row = $result->fetch_assoc();

                                // Access the user ID from the row
                                $user_id = $row['id'];
                                $first_name = $row['email'];

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



                                // Function to generate a random 16-digit alphanumeric string
                                function generateRandomAlphanumericString($length = 16)
                                {
                                    $bytes = random_bytes($length);
                                    return bin2hex($bytes);
                                }

                                // Generate a 16-digit alphanumeric string
                                $randomString = generateRandomAlphanumericString(16);

                                // Output the generated string

                                // Mark OTP as verified
                                $updateQuery = "UPDATE users SET is_verified = ?, api_key = ? WHERE $device = ? AND otp = ?";
                                $updateStmt = $conn->prepare($updateQuery);

                                $email_text = 'Your Ekocrib Account has been verified, you are welcome.';
                                $email_subject = 'Your Ekocrib Verification Successful';
                                $button_text = 'Login';


                                if (!$updateStmt) {

                                    $response = array('status' => 'failed', 'message' => $conn->error);
                                } else {
                                    $updateStmt->bind_param("ssss", $otp_method, $randomString, $emailOrPhone, $otp);
                                    $updateStmt->execute();
                                    $updateStmt->close(); // Close the update statement

                                    // Determine whether it's an email or phone registration
                                    if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                        $email = $emailOrPhone;
                                        $phone = null;
                                        $otp_method = 'email';
                                        $device = $emailOrPhone;
                                        require_once('../email-script.php');
                                        require_once('../email_verify.php');
                                    } else {
                                        $email = null;
                                        $phone = $emailOrPhone;
                                        $otp_method = 'whatsapp number';
                                        $device = $emailOrPhone;
                                        require_once('../chat_verify.php');
                                    }

                                    // require_once('jwt-token.php');

                                    $response = array('status' => 'success', 'message' => $email_subject);
                                }
                            } else {

                                $response = array('status' => 'failed', 'message' => 'Invalid OTP or OTP already used.');
                            }

                            //   $stmt->close(); // Close the statement here
                        }

                        //  $conn->close(); // Close the connection
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
