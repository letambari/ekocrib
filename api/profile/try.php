<?php
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'localhost/ekocrib/api/profile/?user_id=11',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJpbm5vY2VudGRlc3RpbnkyMjhAZ21haWwuY29tIiwiYXVkIjoiUmVudGVyIiwiaWF0IjoxNzAxMTM1NTE2LCJleHAiOjE3MDExMzkxMTYsInVzZXJfaWQiOjExfQ.uWPanPAxjdzslKzQcuy7ynmxLgzfMmy0j1Fgb0wRPTE'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
