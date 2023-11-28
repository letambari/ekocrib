<?php
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database configuration
    include_once '../main/wp-includes/web-contents.php';

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

            $response = array();
            $response['status'] = array(
                'message' => 'Invalid characters in API Key',
                // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
                // 'user_id' =>  $user_id,
                // 'email' => $email
            );
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

                if ($result->num_rows < 0) {
                    // API Key is In-valid
                    $response = array();
                    $response['status'] = array(
                        'message' => 'API Key is missing',
                        // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
                        // 'user_id' =>  $user_id,
                        // 'email' => $email
                    );

                    $stmt->close(); // Close the statement here
                    $conn->close(); // Close the connection here
                    exit();
                } else {
                    // API Key is Valid
                    $row = $result->fetch_assoc();
                    $api_email = $row['email'];
                    $api_user_type = $row['user_type'];
                }

                $stmt->close(); // Close the statement here
            }

            $conn->close(); // Close the connection here

            // Validate user input
            $emailOrPhone = $requestData['emailOrPhone'];
            $first_name = $requestData['first-name'];
            $last_name = $requestData['last-name'];
            $tel_phone = $requestData['phone'];
            $gender = $requestData['gender'];
            $dob = $requestData['dob'];
            $state = $requestData['state'];
            $country = $requestData['country'];
            
          

            // Validate input
            $errors = [];

            if (empty($emailOrPhone)) {
                $errors[] = 'All fields are required.';
            } else {
                // Create a new database connection
                $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $is_verified = 'not_verified';
                $query = "SELECT * FROM users WHERE email = ? or phone = ? AND is_verified = ?";
                $stmt = $conn->prepare($query);

                if (!$stmt) {

                    $response = array();
                    $response['status'] = array(
                        'message' => 'SQL error: ' . $conn->error,
                        // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
                        // 'user_id' =>  $user_id,
                        // 'email' => $email
                    );
                } else {
                    $stmt->bind_param("sss", $emailOrPhone, $emailOrPhone, $is_verified);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Check if the OTP is valid
                    if ($result->num_rows > 0) {

                        $row = $result->fetch_assoc();

                        // Access the user ID from the row
                        $user_id = $row['id'];

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
                        // Mark OTP as verified
                        $updateQuery = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, gender = ?, dob = ?, state = ?, country = ? WHERE email = ? or phone = ?";
                        $updateStmt = $conn->prepare($updateQuery);

                        if (!$updateStmt) {
                            $response['status'] = 'SQL error: ' . $conn->error;
                        } else {
                            $updateStmt->bind_param("sssssssss", $first_name, $last_name, $tel_phone, $gender, $dob, $state, $country, $emailOrPhone, $emailOrPhone);
                            $updateStmt->execute();
                            $updateStmt->close(); // Close the update statement
                                    $email_text = $msg = $business_name . ', your Spacebank Business Account profile has been updated, you are welcome, kindly contact our support if you did not make this update';
                                    $email_subject = 'Profile Update';
                                    $button_text = 'Login to your account';
                                    $button_link = 'https://spacebank.thalajaatdatabase.online/main/profile';

                            // Determine whether it's an email or phone registration
                            if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
                                $email = $emailOrPhone;
                                $phone = null;
                                $otp_method = 'email';
                                $device = $emailOrPhone;
                                require_once('email-script.php');
                                require_once('email.php');
                            } else {
                                $email = null;
                                $phone = $emailOrPhone;
                                $otp_method = 'whatsapp number';
                                $device = $emailOrPhone;
                                $subject = "Profile Update";
                                $msg = 'Hello ' . $business_name . ', your Spacebank Business Account profile has been updated, you are welcome.';
                                require_once('chat_profile_update.php');
                            }

                            // require_once('jwt-token.php');

                            $response = array();
                            $response['status'] = array(
                                'message' => 'Profile Updated Successfully',
                                // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
                                // 'user_id' =>  $user_id,
                                // 'email' => $email
                            );
                        }
                    } else {

                        $response = array();
                        $response['status'] = array(
                            'message' => 'Profile not updated',
                            // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
                            // 'user_id' =>  $user_id,
                            // 'email' => $email
                        );
                    }

                    $stmt->close(); // Close the statement here
                }

                $conn->close(); // Close the connection
            }

            // Handle errors
            if (!empty($errors)) {
                $response = array('errors' => $errors);
            }
        }
    } else {
        // Handle the case when API key is missing in the request

        $response = array();
        $response['status'] = array(
            'message' => 'API Key is missing',
            // 'email_message' => 'Check your ' . $otp_method . ' ' . $email . ' for your verification code.',
            // 'user_id' =>  $user_id,
            // 'email' => $email
        );
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle invalid request method (e.g., GET)
    header('HTTP/1.1 405 Method Not Allowed');
    $response['status'] = 'Invalid request method';
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
