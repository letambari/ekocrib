<?php

$user_id = $_SESSION['user_id'];

// $_user = 39;
// echo $user_id;
// exit();
// Database configuration
include_once 'web-contents.php';

// Function to respond with an error message and HTTP status code
function respondWithError($message, $statusCode)
{
    http_response_code($statusCode);
    echo json_encode(array('error' => $message));
    exit();
}

// Create a database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    respondWithError('Connection failed: ' . $conn->connect_error, 500);
}

// You should have a user_id value defined somewhere before this point
// Replace with your actual user_id value

$query = "SELECT sum(amount) as total_amount FROM withdrawals WHERE user_id = ? AND withdraw_status = ?";
$stmt = $conn->prepare($query);
$withdraw_status = 'Success';

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("is", $_SESSION['user_id'], $withdraw_status);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('User not found', 404);
} else {
    $user_details = $result->fetch_assoc();
    $total_amount = $user_details['total_amount'];

    if ($total_amount == "null") {
        $total_amount =  0.00;
    } else {
        $total_amount =  $total_amount;
    }

    // Respond with the total amount
    json_encode(array('total_amount' => $total_amount));
}
//exit();




// getting the total deposit amount
$query = "SELECT sum(usd_eqv) as total_outcome_amount FROM deposit WHERE order_id = ? AND payment_status = ?";
$stmt = $conn->prepare($query);
$payment_status = 'finished';


if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("is", $_SESSION['user_id'], $payment_status);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('User not found', 404);
} else {
    $user_details = $result->fetch_assoc();
    $total_outcome_amount = $user_details['total_outcome_amount'];

    if ($total_outcome_amount == "null") {
        $total_outcome_amount =  0.00;
    } else {
        $total_outcome_amount =  $total_outcome_amount;
    }

    // Respond with the total amount
    $outcome_amountJSON = json_encode($total_outcome_amount);
}

//  echo $total_outcome_amount;
//  exit();
 
// getting all the total failed deposit amount
$query = "SELECT sum(price_amount) as failed_amount FROM deposit WHERE order_id = ? AND payment_status = ?";
$stmt = $conn->prepare($query);
$payment_status = 'failed';

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("is", $_SESSION['user_id'], $payment_status);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('User not found', 404);
} else {
    $user_details = $result->fetch_assoc();
    $failed_amount = $user_details['failed_amount'];

    if ($failed_amount == "") {
        $failed_amount =  0.00;
    } else {
        $failed_amount =  $failed_amount;
    }

    // Respond with the total amount
    //$outcome_amountJSON = json_encode($failed_amount);
}



// getting the percentage per day.

$today = date('Y-m-d H:i:s'); // Get today's date and time
$yesterday = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($today)));


// Getting the total deposit amount for yesterday
$query = "SELECT sum(usd_eqv) as outcome_amount FROM deposit WHERE order_id = ? AND payment_status = ? AND created_at > ?";
$stmt = $conn->prepare($query);
$payment_status = 'finished';

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("iss", $_SESSION['user_id'], $payment_status, $yesterday);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('Order not found', 404);
}

$user_details = $result->fetch_assoc();
$outcome_amount_yesterday = $user_details['outcome_amount'];


if ($outcome_amount_yesterday === null) {
    $outcome_amount_yesterday = 0.00;
}



// Calculate the start and end of today
$todayStart = date('Y-m-d 00:00:00');
$todayEnd = date('Y-m-d 23:59:59');

// Query to retrieve the total deposit amount for today
$query = "SELECT SUM(usd_eqv) as outcome_amount FROM deposit WHERE order_id = ? AND payment_status = ? AND created_at >= ? AND created_at <= ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("isss", $_SESSION['user_id'], $payment_status, $todayStart, $todayEnd);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('No deposits found for today', 404);
}

$user_details = $result->fetch_assoc();
$outcome_amount_today = $user_details['outcome_amount'];

if ($outcome_amount_today === null) {
    $outcome_amount_today = 0.00;
}


// Calculate the percentage difference
$percentage_difference = 0.00;

if ($outcome_amount_yesterday !== 0.00) {
    $percentage_difference = (($outcome_amount_today - $outcome_amount_yesterday) / $outcome_amount_yesterday) * 100;
}




if ($percentage_difference > 0) {

    $caret_color = 'sucess';
    $caret_direction = 'up';
} elseif ($percentage_difference < 0) {

    $caret_color = 'danger';
    $caret_direction = 'down';
} else {
    $caret_color = 'warning';
    $caret_direction = 'right';
}

// Respond with the percentage difference in JSON format
json_encode(array('PercentageDifference' => $percentage_difference));



//get the deposits per month

$depositData = array();

// Query deposits aggregated by month
$query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(usd_eqv) AS total_amount FROM deposit WHERE order_id = '$user_id' AND payment_status = '$payment_status'  GROUP BY month";
$result = $conn->query($query);

if ($result) {
    // Initialize an array to store deposit amounts for each month
    $monthData = array_fill(1, 12, 0);

    while ($row = $result->fetch_assoc()) {
        // Extract the month and total amount from the database result
        $month = intval(substr($row['month'], 5));
        $totalAmount = floatval($row['total_amount']);

        // Store the total amount in the correct month's position
        $monthData[$month] = $totalAmount;
    }

    // Copy the data into $depositData array
    $depositData = array_values($monthData);
}

// Convert deposit data to JSON format
$depositDataJSON = json_encode($depositData);

//exit();



// counting the total deposit amount
$query = "SELECT COUNT(*) as deposit_count FROM deposit WHERE order_id = ?";
$stmt = $conn->prepare($query);
$payment_status = 'finished';

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('User not found', 404);
} else {
    $user_details = $result->fetch_assoc();
    $deposit_count = $user_details['deposit_count'];

    if ($deposit_count == "null") {
        $deposit_count =  0.00;
    } else {
        $deposit_count =  $deposit_count;
    }

    // Respond with the total amount
    //$outcome_amountJSON = json_encode($outcome_amount);
}



// counting the total withdrawal amount
$query = "SELECT COUNT(*) as withdrawal_count FROM withdrawals WHERE user_id = ?";
$stmt = $conn->prepare($query);
$payment_status = 'Success';

if (!$stmt) {
    respondWithError('SQL error: ' . $conn->error, 500);
}

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    respondWithError('User not found', 404);
} else {
    $user_details = $result->fetch_assoc();
    $withdrawal_count = $user_details['withdrawal_count'];

    if ($withdrawal_count == "null") {
        $withdrawal_count =  0.00;
    } else {
        $withdrawal_count =  $withdrawal_count;
    }

    // Respond with the total amount
    //$outcome_amountJSON = json_encode($outcome_amount);
}


// Assuming you have the following totals
$percentage_total_outcome_amount = $total_outcome_amount;
$percentage_total_payout_amount = $total_amount;
$percentage_total_failed_amount = $failed_amount;

// Calculate the total transaction amount
$total_transaction = $percentage_total_outcome_amount + $percentage_total_payout_amount + $percentage_total_failed_amount;

// Calculate the percentages based on the total transaction amount
$percentage_total_outcome_amount = ($percentage_total_outcome_amount / $total_transaction) * 100;
$percentage_total_payout_amount = ($percentage_total_payout_amount / $total_transaction) * 100;
$percentage_total_failed_amount = ($percentage_total_failed_amount / $total_transaction) * 100;

// Ensure that the percentages do not exceed 100%
if ($percentage_total_outcome_amount + $percentage_total_payout_amount + $percentage_total_failed_amount > 100) {
    // You may want to adjust the percentages to make sure they don't exceed 100%
    // For example, you could proportionally scale down each percentage
    $scale_factor = 100 / ($percentage_total_outcome_amount + $percentage_total_payout_amount + $percentage_total_failed_amount);
    $percentage_total_outcome_amount *= $scale_factor;
    $percentage_total_payout_amount *= $scale_factor;
    $percentage_total_failed_amount *= $scale_factor;
}
// Close the database connection
$stmt->close();
//$conn->close();
