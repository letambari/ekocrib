<?php


$apiUrl = '' . $now_web_url . '/estimate?amount=' . $outcome_amount . '&currency_from=' . $outcome_currency . '&currency_to=' . $request_converted_currency . '';
$apiKey = $now_api_key;

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

$resp = curl_exec($curl);

// echo $resp;
// exit();
$curlRes = json_decode($resp);

$request_outcome_amount = $curlRes->amount_from;
$request_outcome_currency = $curlRes->currency_from;
$request_converted_currency = $curlRes->currency_to;
$estimated_amount = $curlRes->estimated_amount;

// Assuming $yourString contains the numeric string
// $request_outcome_amount = $request_outcome_amount;
// $request_outcome_amount = floatval($request_outcome_amount);

// Now, you can perform mathematical operations with $yourFloat


//getting the transaction charge and the value of the charge

$deposit_charge = 2.5;

$charge_amount = $request_outcome_amount * ($deposit_charge / 100);

$estimated_amount_in_asking_currency = $request_outcome_amount + $charge_amount;

//$our_charge = $request_outcome_amount - $charge_amount;
//getting the value of the estimated amount 
