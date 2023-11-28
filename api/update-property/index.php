<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
header("Access-Control-Allow-Methods: PATCH, GET, OPTIONS");
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


$propertyID = $_GET['propertyID'];

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
    // Example: Extracting user ID from decoded data
    $user_id = $decoded_data->user_id;

    // Perform a query to fetch user details by user_id
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('User not found', 404);
    }


    // Perform a query to fetch house details by propertyID
    $query = "SELECT * FROM house_details WHERE id = ? AND owner_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $propertyID, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('Property not found', 404);
    }

    // Continue with the update logic for other request methods (e.g., PATCH)

    // Validate input and update only the provided fields
    $updateFields = ['house_type', 'building_name', 'owner_id', 'house_status', 'position', 'privacy', 'house_image', 'address', 'city', 'state', 'country'];
    $updateData = [];

    foreach ($updateFields as $field) {
        if (isset($requestData[$field])) {
            $updateData[$field] = $requestData[$field];
        }
    }

    $curl = curl_init();
    // Extract the image URL from the request
    $imageURL = $requestData['house_image'];

    // Define Cloudinary API credentials and upload preset
    $cloudName = "ekocrib-v1";
    $apiKey = "421715199176685";
    $apiSecret = "75Cs-4G54PXmJyYU7l-S59FfHEQ";
    $uploadPreset = "dgb6case";

    // Build the Cloudinary upload URL
    $cloudinaryUploadURL = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

    // Prepare the POST data for cURL
    $postData = [
        'file' => $imageURL,
        'upload_preset' => $uploadPreset,
        'api_key' => $apiKey,
    ];

    curl_setopt_array($curl, array(
        CURLOPT_URL => $cloudinaryUploadURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode("{$apiKey}:{$apiSecret}")
        ),
    ));

    $curlResponse = curl_exec($curl);

    if (curl_error($curl)) {
        $response['status'] = 'cURL Error: ' . curl_error($curl);
        sendResponse($response);
    }

    $curlResponse = json_decode($curlResponse);

    if ($curlResponse) {
        $image_url = $curlResponse->url;
    }

    // Check if there are fields to update
    if (empty($updateData)) {
        respondWithError('No fields to update', 400);
    }

    // Update house details
    $updateQuery = "UPDATE house_details SET ";
    $updateParams = [];

    foreach ($updateData as $field => $value) {
        $updateQuery .= "`$field` = ?, ";
        $updateParams[] = &$updateData[$field];
    }

    // Remove the trailing comma and space
    $updateQuery = rtrim($updateQuery, ', ');

    $updateQuery .= " WHERE id = ? AND owner_id = ?";
    $updateParams[] = &$propertyID;
    $updateParams[] = &$user_id;

    $updateStmt = $conn->prepare($updateQuery);

    if (!$updateStmt) {
        respondWithError('SQL error: ' . $conn->error, 500);
    }

    // Dynamically bind parameters
    $types = str_repeat('s', count($updateParams));
    $updateStmt->bind_param($types, ...$updateParams);

    // Execute the update statement
    $updateStmt->execute();

    // Check for successful update
    if ($updateStmt->affected_rows > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Property Details Updated'
        ]);
    } else {
        respondWithError('Profile not updated', 500);
    }

    // Close the update statement
    $updateStmt->close();
} else {
    // Token validation failed
    respondWithError($validation['message'], 401);
}

// Close the database connection
$conn->close();
