<?php 

// Function to generate a random 16-digit alphanumeric string
function generateRandomAlphanumericString($length = 16) {
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}

// Generate a 16-digit alphanumeric string
$randomString = generateRandomAlphanumericString(16);

// Output the generated string
echo $randomString;
