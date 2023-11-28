<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if user_id is included in the GET request
    if (!isset($_GET['user_id'])) {
        respondWithError('User ID not provided in the request', 400);
    }

    // Retrieve user details based on the provided user_id
    $requested_user_id = $_GET['user_id'];

    // Perform a query to fetch user details by user_id
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $requested_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        respondWithError('User not found', 404);
    }

    $user_details = $result->fetch_assoc();
    $user_details['user_id'] = $requested_user_id;

    header('Content-Type: application/json');
    echo json_encode($user_details);
}
