<?php


// getting the api user
// $api_key = 'Decode-Code'; // Replace with your actual API key

// $curl = curl_init();

// curl_setopt_array($curl, array(
//     CURLOPT_URL => 'http://localhost/ekocrib-api/index.php?api_key=' . $api_key, // Include the API key in the URL
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'GET',
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;


//registration

// $curl = curl_init();

// curl_setopt_array($curl, array(
//     CURLOPT_URL => 'http://localhost/ekocrib-api/register.php',
//     CURLOPT_RETURNTRANSFER => true,
//     CURLOPT_ENCODING => '',
//     CURLOPT_MAXREDIRS => 10,
//     CURLOPT_TIMEOUT => 0,
//     CURLOPT_FOLLOWLOCATION => true,
//     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//     CURLOPT_CUSTOMREQUEST => 'POST',
//     CURLOPT_POSTFIELDS => json_encode(array(
//         'api_key' => '13db0a2eda129aa67f8b2c60e175e1fd',  // Replace with your API key
//         'emailOrPhone' => 'innocentde@gmail.com',
//         'password' => 'your_password_here',
//         'confirmPassword' => 'your_password_confirmation_here'
//     )),
//     CURLOPT_HTTPHEADER => array(
//         'Content-Type: application/json'
//     ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;


// Replace 'YOUR_JWT_TOKEN' with your actual JWT token

// Function to make a cURL GET request

$curl = curl_init();

$headers = array(
    'Authorization: Bearer YOUR_JWT_TOKEN', // Replace YOUR_JWT_TOKEN with your actual JWT token
    'x-api-key: 13db0a2eda129aa67f8b2c60e175e1fd' // Replace YOUR_API_KEY with your actual API key
);

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost/ekocrib-api/home.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => $headers,
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
