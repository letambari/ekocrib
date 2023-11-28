<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => '' . $web_url . '/api/payment/get-all-deposit.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS => '{
"api_key": "' . $site_api_key . '",
"order_id": ' . $user_id . '


}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key: ' . $site_api_key
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$res = json_decode($response);

$deposits = '';
// Assuming $res is the API response
foreach ($res as $item) {

    $payment_id = $item->payment_id;
    $payment_status = $item->payment_status;
    // $pay_address = $item->pay_address;
    $price_amount = $item->price_amount;
    $price_currency = $item->price_currency;
    $pay_amount = $item->pay_amount;
    // $pay_currency = $item->pay_currency;
    // $order_id = $item->order_id;
    $order_description = $item->order_description;
    $created_at = $item->created_at;
    // $updated_at = $item->updated_at;
    // $payin_extra_id = $item->payin_extra_id;
    // $purchase_id = $item->purchase_id;
    // $actually_paid = $item->actually_paid;
    $outcome_amount = $item->outcome_amount;
    $outcome_currency = $item->outcome_currency;
    $trans_type = $item->trans_type;
    $business_payer_email = $item->business_payer_email;

    if ($payment_status == 'finished' || $payment_status == 'partially_paid ') {

        $color = 'success';
        $checked = 'checked';
    } elseif ($payment_status == 'confirming' || $payment_status == 'sending') {
        $color = 'info';
        $checked = '';
    } else {
        $color = 'warning';
        $checked = '';
    }


    // Now you can use these variables as needed

    $deposits .= ' <tr>
    <td>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="customCheck2" ' . $checked . ' readonly>
            <label class="form-check-label" for="customCheck2">&nbsp;</label>
        </div>
    </td>
    <td><a href="#" class="text-body fw-500">#' . $payment_id . '</a> </td>
    <td>
        ' . $created_at . '
    </td>
    <td>
        <p class="mb-0"><span class="badge badge-' . $color . '-light" style="color: black;"><i class="mdi mdi-bitcoin"></i> ' . $payment_status . '</span></p>
    </td>
    <td>
        $' . number_format($price_amount) . '
    </td>
    <td>
        ' . $price_currency . '
    </td>
    <td>
        ' . $business_payer_email . '
    </td>
    <td>
        ' . $outcome_amount . '
    </td>
    <td>
        ' . $outcome_currency . '
    </td>
    <td>
        ' . $trans_type . '
    </td>
    <td>
        ' . $order_description . '
    </td>

    <!-- <td>
        <p class="mb-0"><span class="badge badge-primary-light">Shipped</span></p>
    </td> 
    <td>
        <a href="javascript:void(0);" class="action-icon"> <i class="fa fa-eye"></i></a>
        <a href="javascript:void(0);" class="action-icon mx-2"> <i class="fa fa-pencil"></i></a>
        <a href="javascript:void(0);" class="action-icon"> <i class="fa fa-trash"></i></a>
    </td>-->
</tr>';
}
