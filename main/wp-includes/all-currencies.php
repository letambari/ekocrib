<?php


$currency = '';
$dashboard_currency = '';
$deposit_currencies = '';
$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login");
}


$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => '' . $web_url . '/api/payment/get-currency.php',
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
// echo $response;


$res =  json_decode($response);

// Assuming $res is the API response
foreach ($res as $item) {
    $id = $item->id;
    $currency_full_name = $item->currency_full_name;
    $currency_short_name = $item->currency_short_name;
    $currency_use_name = $item->use_name;
    $currency_logo = $item->logo;

    // Now you can use these variables as needed

    $currency .= '<div class="col-md-2 col-lg-2">
    <div class="preview">
        <a href="#">
            <i class="cc ' . $currency_short_name . '" title="' . $currency_short_name . '"></i>
            <span class="name">' . $currency_full_name . '</span>
        </a>
    </div>
</div>';



    $dashboard_currency .= ' <div class="d-flex pb-15 mb-15 bb-1 bb-dashed">
<div class="" style="width: 50px;    margin-right: 13px;">
    <img src="../../images/currencies/' . $currency_logo . '" class="avatar b-1" alt="" />
</div>
<div class="text-overflow" style="margin-left: 5px;">
    <a href="#">
        <p class="mb-0 fw-500 text-overflow">' . $currency_full_name . '</p>
        <p class="mb-0 fw-500">' . $currency_use_name . '</p>
    </a>
</div>
</div>';


    $deposit_currencies .= '<option value="' . $currency_use_name . '"><i class="cc ' . $currency_short_name . '" title="' . $currency_short_name . '">' . $currency_full_name . '</option>';
}
