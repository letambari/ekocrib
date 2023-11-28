<?php
// Allow cross-origin resource sharing (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Set error reporting and display_errors in development
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight requests
    header('HTTP/1.1 200 OK');
    exit();
}
class API
{
    private $db;

    function __construct()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Database configuration
        include_once '../../main/wp-includes/web-contents.php';

        // Create a database connection with error handling
        $this->db = new mysqli($db_host, $db_user, $db_pass, $db_name);
        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }

    function checkAPIKey($api_key)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        // Remove special characters and spaces
        $api_key = preg_replace("/[^a-zA-Z0-9]/", '', $api_key);

        // Check if the API key is alphanumeric
        if (!ctype_alnum($api_key)) {

            $response = array('status' => 'failed', 'message' => 'Invalid characters in API Key');
        } else {
            // Use a prepared statement to prevent SQL injection
            $stmt = $this->db->prepare("SELECT email, user_type FROM users WHERE api_key = ?");

            // Check if the statement was prepared successfully
            if ($stmt) {
                $stmt->bind_param("s", $api_key);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // API Key is Valid
                    $row = $result->fetch_assoc();
                    $email = $row['email'];
                    $user_type = $row['user_type'];

                    $response = array('status' => 'success', 'message' => 'API Key is Valid', 'email' => $email, 'user_type' => $user_type);
                } else {
                    // API Key is Invalid

                    $response = array('status' => 'failed', 'message' => 'API Key is Invalid');
                }

                $stmt->close();
            } else {
                // Debugging: Print the result of the query and the provided API key

                $response = array('status' => 'failed', 'message' => $this->db->error);
            }
        }

        // Close the database connection
        $this->db->close();

        // Encode the response as JSON and return it
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}

// Usage example
$api = new API();
$api_key = $_GET['api_key'];
$api->checkAPIKey($api_key);
