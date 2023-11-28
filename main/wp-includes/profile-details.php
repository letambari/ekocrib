<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => '' . $web_url . '/api/home.php?user_id=' . $user_id . '',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
    "api_key": "' . $site_api_key . '"


}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key:  ' . $site_api_key
    ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;

$res =  json_decode($response);

$user_id = $res->id;
$user_email = $res->email;
$user_phone = $res->phone;
$first_name = $res->first_name;
$last_name = $res->last_name;
$country = $res->country;
$business_name = $res->business_name;
$display_name = $res->display_name;
$user_type = $res->user_type;
$about = $res->about;
$website = $res->website;
$acct_name = $res->acct_name;
$acct_number = $res->acct_number;
$acct_bank = $res->acct_bank;
$balance = $res->balance;
$ledger_balance = $res->ledger_balance;
$user_api_key = $res->api_key;
