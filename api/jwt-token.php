<?php
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

// Your secret key to sign the token
$secretKey = 'your-secret-key';

// Sample user data (you can replace this with your own user data)
$userData = [
    'user_id' => $user_id,
    'username' => $device,
];

// Set the expiration time for the token (e.g., 1 hour from now)
$expirationTime = time() + 3600; // 1 hour

// Create the token
$token = [
    'iat' => time(),          // Issued at (current timestamp)
    'exp' => $expirationTime, // Expiration time
    'data' => $userData,     // User data
];

// Encode the token using your secret key
$jwt = JWT::encode($token, $secretKey, 'HS256');
