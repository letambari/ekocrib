<?php

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ... (Previous code for database connection and API key check)
    // Database configuration
    include_once '../../main/wp-includes/web-contents.php';

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
            $response['status'] = 'Invalid characters in API Key';
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

                if ($result->num_rows < 1) {
                    // API Key is Invalid
                    $response['status'] = 'API Key is Invalid';
                    $stmt->close(); // Close the statement here
                    $conn->close(); // Close the connection here
                    exit();
                } else {
                    // API Key is Valid
                    $row = $result->fetch_assoc();
                    $api_display_name = $row['display_name'];
                    $api_email = $row['email'];
                    $api_user_type = $row['user_type'];
                }

                $stmt->close(); // Close the statement here
            }

            //$conn->close(); // Close the connection here

            // Validate user input
            $order_id = $requestData['order_id']; // Use a different variable name

            // Validate input
            $errors = [];

            if (empty($order_id)) {
                $errors[] = 'All fields are required.';
            } else {
                // API endpoint URL

                $query = "SELECT * FROM deposit WHERE order_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows < 1) {
                    respondWithError('No Deposit', 404);
                }

                // Fetch all currency values and store them in an array
                $deposits = array();
                while ($row = $result->fetch_assoc()) {
                    $deposits[] = $row;
                }

                header('Content-Type: application/json');
                echo json_encode($deposits);
            }

            // Handle errors
            if (!empty($errors)) {
                $response['errors'] = $errors;
            }
        }
    } else {
        // ... (Previous code for handling invalid request methods)
        $response['status'] = 'API Key is missing';
    }
} else {
    // Handle invalid request method (e.g., GET)
    header('HTTP/1.1 405 Method Not Allowed');
    $response['status'] = 'Invalid request method';
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
