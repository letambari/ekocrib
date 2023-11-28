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
header("Access-Control-Allow-Methods: PATCH, OPTIONS");
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
    // Example: Extracting user ID from decoded data
    $user_id = $decoded_data->user_id;

    // Perform a query to fetch user details by user_id
    // Validate user input
    $building_name = $requestData['building_name'];
    $house_type = $requestData['house_type'];
    $owner_id = $user_id;
    $house_status = $requestData['house_status'];
    $position = $requestData['position'];
    $privacy = $requestData['privacy'];
    $cloudinary_image = $requestData['cloudinary-image'];
    $address = $requestData['address'];
    $city = $requestData['city'];
    $state = $requestData['state'];
    $country = $requestData['country'];
    // Validate input
    $errors = [];
    if (empty($building_name) || empty($owner_id)) {
        $errors[] = 'All fields are required.';
    } else {
        // Create a new database connection
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "SELECT * FROM users WHERE id = ? AND (user_type != ? OR user_type != ?)";
        $stmt = $conn->prepare($query);
        $Renter = 'Renter';
        $Staff = 'Staff';

        if (!$stmt) {
            $response['status'] = 'SQL error: ' . $conn->error;
        } else {
            $stmt->bind_param("iss", $owner_id, $Renter, $Staff);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows < 1) {
                // the owner of the property does not exist.


                $response = array('status' => 'failed', 'message' => 'The owner of this property does not exist, please create an agent/landlord account before adding a property');
            } else {

                $curl = curl_init();
                // Extract the image URL from the request
                $imageURL = $requestData['cloudinary-image'];

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

                $insertQuery = "INSERT INTO house_details (house_type, building_name, owner_id, house_status, position, privacy, house_image, address, city, state, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertQuery);

                if (!$insertStmt) {
                    $response['status'] = 'SQL error: ' . $conn->error;
                } else {
                    $insertStmt->bind_param("ssissssssss", $house_type, $building_name, $owner_id, $house_status, $position, $privacy, $image_url, $address, $city, $state, $country);
                    if ($insertStmt->execute()) {

                        $response = array('status' => 'success', 'message' => 'Your Property was Uploaded Successfully');
                    } else {

                        $response = array('status' => 'failed', 'message' => 'Your Property was not Uploaded Successfully');
                    }

                    $insertStmt->close(); // Close the insert statement
                }
            }
        }
    }

    // Handle errors
    if (!empty($errors)) {
        $response = array('errors' => $errors);
    }
} else {
    // Token validation failed
    respondWithError($validation['message'], 401);
}

// Close the database connection
$conn->close();

// Output the response
header('Content-Type: application/json');
echo json_encode($response);
