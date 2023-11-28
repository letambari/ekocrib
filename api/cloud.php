<?php

$curl = curl_init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON request body
    $requestBody = file_get_contents('php://input');

    // Decode the JSON data
    $requestData = json_decode($requestBody, true);

    // Ensure the "cloudinary-image" key is present in the request
    if (isset($requestData['cloudinary-image'])) {
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

        $response = curl_exec($curl);

        curl_close($curl);

        // Return JSON response
        header('Content-Type: application/json');
        echo $response;
        
           $res =  json_decode($response);
        $image_url = $res->url;
        $string = $image_url;

// Remove white spaces
$image_url = str_replace(' ', '', $string);

$image_url;
    } else {
        // Handle the case when "cloudinary-image" is missing in the request
        $response['status'] = array(
            'message' => 'The "cloudinary-image" key is missing in the request',
        );

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    // Handle invalid request method (e.g., GET)
    header('HTTP/1.1 405 Method Not Allowed');
    $response['status'] = 'Invalid request method';

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
