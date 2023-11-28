<?php


if (empty($price_amount) || empty($price_currency)) {
    $errors[] = 'All fields are required.';
} else {
    // API endpoint URL
    $request_converted_currency = 'usd';
    $apiUrl = 'https://api-sandbox.nowpayments.io/v1/estimate?amount=' . $price_amount . '&currency_from=' . $price_currency . '&currency_to=' . $request_converted_currency . '';
    $apiKey = '3EVC2MX-EYR4HXJ-J7JYGRH-FPP1DNB';

    $postData = array(
        'x-api-key: ' . $apiKey
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => $postData,
    ));

    $res = curl_exec($curl);
    $curlResponse = json_decode($res);
    $response = $curlResponse;
}
