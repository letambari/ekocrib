<?php
require '../vendor/autoload.php';

// Database configuration
include_once '../../main/wp-includes/web-contents.php';

// Function to respond with an error message and HTTP status code
function respondWithError($message, $statusCode)
{
    http_response_code($statusCode);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit();
}

// Get the JSON request body
$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

// Check if the API key is present in the decoded data
if (!isset($requestData['api_key'])) {
    respondWithError('API Key not provided', 401);
}

$api_key = preg_replace("/[^a-zA-Z0-9]/", '', $requestData['api_key']);

if (!ctype_alnum($api_key)) {
    respondWithError('Invalid characters in API Key', 401);
}

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    respondWithError('Connection failed: ' . $conn->connect_error, 500);
}

// Use a prepared statement to prevent SQL injection
$query = "SELECT * FROM users WHERE api_key = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("s", $api_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('API Key is Invalid', 401);
}

$row = $result->fetch_assoc();
$user_id = $row['id'];
$currency_status = 'Active';
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if user_id is included in the GET request

    // Perform a query to fetch user details by user_id
    $query = "SELECT * FROM currencies WHERE currency_status = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $currency_status);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('No Currency', 404);
    }

    // Fetch all currency values and store them in an array
    $currencies = array();
    while ($row = $result->fetch_assoc()) {
        $currencies[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($currencies);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle POST requests for other API actions
    // Add your logic here to handle different API endpoints and actions
} else {
    respondWithError('Unsupported HTTP method', 405);
}

// Close the database connection
$conn->close();
