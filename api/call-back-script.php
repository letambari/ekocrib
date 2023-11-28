<?php



//currency converter start
                          
                          $apiUrl = 'https://api-sandbox.nowpayments.io/v1/estimate?amount=' . $outcome_amount . '&currency_from=' . $outcome_currency . '&currency_to=' . $request_converted_currency . '';
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
                          $approved = 'Approved';
                          
                                        
                            if ($payment_status === 'finished' || $payment_status === 'partially_paid') {
                              $sql = "UPDATE users SET balance = (balance + '$estimated_amount') WHERE id = '$order_id'";

                              if ($conn->query($sql) === TRUE) {

                                  $sql = "UPDATE deposit SET payment_status = ?, status_type = ?, actually_paid = ?, actually_paid_at_fiat = ?, usd_eqv = ?, trans_charge = ? WHERE payment_id = ?";

                                  // Bind the parameters to the placeholders
                                  $stmt = mysqli_prepare($conn, $sql);
                                  mysqli_stmt_bind_param(
                                      $stmt,
                                      "sssssss",
                                      $payment_status,
                                      $approved,
                                      $actually_paid,
                                      $actually_paid_at_fiat,
                                      $estimated_amount,
                                      $charge_amount,
                                      $payment_id
                                  );
                                  mysqli_stmt_execute($stmt);
                                  error_log("user balance & deposit Database Updated");
                              } else {
                                  echo "Error updating record: " . $conn->error;
                                  error_log("Error updating record: " . $conn->error);
                              }
                          }
                          
                          

   // get the actual message that should come with the message.
                  if ($payment_status === 'finished') {
                      $payment_message = "The funds have reached your personal address and the payment is finished";
                  } elseif ($payment_status === 'partially_paid') {
                      $payment_message = "It shows that the customer sent the less than the actual price. Appears when the funds have arrived in your wallet";
                  } elseif ($payment_status === 'waiting') {
                      $payment_message = "waiting for the customer to send the payment. The initial status of each payment";
                  } elseif ($payment_status === 'confirming') {
                      $payment_message = "The transaction is being processed on the blockchain. Appears when NOWPayments detect the funds from the user on the blockchain";
                  } elseif ($payment_status === 'confirmed') {
                      $payment_message = "The process is confirmed by the blockchain. Customer’s funds have accumulated enough confirmations";
                  } elseif ($payment_status === 'sending') {
                      $payment_message = "The funds are being sent to your personal wallet. We are in the process of sending the funds to you";
                  } elseif ($payment_status === 'failed') {
                      $payment_message = "The payment wasn't completed due to the error of some kind";
                  } elseif ($payment_status === 'refunded') {
                      $payment_message = "The funds were refunded back to the user";
                  } elseif ($payment_status === 'expired') {
                      $payment_message = "the user didn't send the funds to the specified address in the 7 days time window";
                  }

?>