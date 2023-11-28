<?php

// Include the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use \Firebase\JWT\JWT;

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
                    $password = $requestData['password'];

                    // Validate input
                    $errors = [];

                    if (empty($emailOrPhone) || empty($password)) {
                        $errors[] = 'Email/Phone and password are required.';
                    } else {
                        // Perform user authentication here
                        $query = "SELECT * FROM users WHERE (email = ? OR phone = ?) AND is_verified != ? LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $is_verified = 'not_verified';

                        // Prepare statement
                        if ($stmt) {
                            $stmt->bind_param("sss", $emailOrPhone, $emailOrPhone, $is_verified);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Process authentication result
                            if ($result->num_rows == 1) {
                                $row = $result->fetch_assoc();
                                $hashedPassword = $row['passwords'];
                                $user_email = $row["email"];
                                $user_type = $row["user_type"];
                                $key = $jwt_key;
                                if (password_verify($password, $hashedPassword)) {
                                    // Password is correct, user is authenticated

                                    // Generate JWT token
                                    $token = generateJwtToken($row['id']);

                                    $user_id = $row["id"];

                                    // $response['status'] = array(
                                    //     'message' => 'successful',
                                    //     'user_id' =>  $user_id,
                                    //     'token' => $token
                                    // );

                                    $response = array('status' => 'success', 'message' => 'Login successful', 'token' => $token);
                                } else {
                                    // Password is incorrect

                                    $response = array('status' => 'failed', 'message' => 'Login failed. Invalid password.');
                                }
                            } else {
                                // No user found with the provided email/phone

                                $response = array('status' => 'failed', 'message' => 'Login failed. Your account is not verified.');
                            }
                        } else {

                            $response = array('status' => 'failed', 'message' => $conn->error);
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

/**
 * Generate a JWT token for the given user ID.
 *
 * @param int $userId
 * @return string
 */
/**
 * Generate a JWT token for the given user ID.
 *
 * @param int $userId
 * @return string
 */
function generateJwtToken($userId)
{

    global $key; // Replace with your secret key
    $algorithm = 'HS256'; // Replace with the desired algorithm, e.g., 'HS256'
    global $user_email;
    global $user_type;
    $token = array(
        'iss' => $user_email,
        'aud' => $user_type,
        'iat' => time(),
        'exp' => time() + (60 * 60), // Expires in 1 hour
        'user_id' => $userId,
    );

    return JWT::encode($token, $key, $algorithm);
}
