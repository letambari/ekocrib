<?php
require_once __DIR__ . '/../vendor/autoload.php';
include_once '../../main/wp-includes/web-contents.php';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\Key;

// Allow cross-origin resource sharing (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Function to respond with an error message and HTTP status code
function respondWithError($message, $statusCode)
{
    http_response_code($statusCode);
    echo json_encode(['status' => 'failed', 'message' => $message]);
    exit();
}

/** 
 * Get header Authorization
 * */
function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

/**
 * get access token from header
 * */
function getBearerToken()
{
    $headers = getAuthorizationHeader();
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Get the JSON request body
$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

// Check if the Authorization header is present
$jwt_token = getBearerToken();

if (empty($jwt_token)) {
    respondWithError('JWT Token not provided', 401);
}

// Function to validate and decode the JWT token
function validateJWTToken($token, $key)
{
    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return ['status' => 'success', 'data' => $decoded];
    } catch (ExpiredException $e) {
        return ['status' => 'failed', 'message' => 'Token has expired'];
    } catch (SignatureInvalidException $e) {
        return ['status' => 'failed', 'message' => 'Invalid token signature'];
    } catch (BeforeValidException $e) {
        return ['status' => 'failed', 'message' => 'Token cannot be used yet'];
    } catch (Exception $e) {
        return ['status' => 'failed', 'message' => 'Token is invalid'];
    }
}

$key = $jwt_key; // same key used in the registration endpoint
$validation = validateJWTToken($jwt_token, $key);

// Check the status of the validation
if ($validation['status'] === 'success') {
    // Token is valid, proceed with further processing
    $decoded_data = $validation['data'];
    // Your additional logic here
    // ...
    // End of your additional logic

    // Example: Extracting user ID from decoded data
    $user_id = $decoded_data->user_id;

    // Perform a query to fetch user details by user_id
    $query = "SELECT * FROM house_details WHERE owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('You have no property yet', 404);
    }

    $house_details = $result->fetch_assoc();
    $response = [
        'status' => 'success',
        'house_data' => $house_details,
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Token validation failed
    respondWithError($validation['message'], 401);
}

// Close the database connection
$conn->close();
