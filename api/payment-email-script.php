<?php

$payment_messages = '<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Your payment of '.$price_currency.'<span style="color: green">(' . $price_amount . ')</span> is '.$payment_status.'</title>
  
  
  
  
  
</head>

<body>

  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Spacebank Business</title>
  </head>
  
  <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0"
  style="margin: 0pt auto; padding: 0px; background:#F4F7FA;">
    <table id="main" width="100%" height="100%" cellpadding="0" cellspacing="0" border="0"
    bgcolor="#F4F7FA">
      <tbody>
        <tr>
          <td valign="top">
            <table class="innermain" cellpadding="0" width="580" cellspacing="0" border="0"
            bgcolor="#F4F7FA" align="center" style="margin:0 auto; table-layout: fixed;">
              <tbody>
                <!-- START of MAIL Content -->
                <tr>
                  <td colspan="4">
                    <!-- Logo start here -->
                    <table class="logo" width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tbody>
                        <tr>
                          <td colspan="2" height="30"></td>
                        </tr>
                        <tr>
                          <td valign="top" align="center">
                            <a href="https://www.business.myspacebank.com" style="display:inline-block; cursor:pointer; text-align:center;">
                             <center> <img src="https://spacebank.thalajaatdatabase.online/images/logo/spacebankBlack1.png"
                              height="24" width="104" border="0" alt="Spacebank"></center>
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="30"></td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- Logo end here -->
                    <!-- Main CONTENT -->
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                    style="border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                      <tbody>
                        <tr>
                          <td height="40"></td>
                        </tr>
                        <tr style="font-family: -apple-system,BlinkMacSystemFont,&#39;Segoe UI&#39;,&#39;Roboto&#39;,&#39;Oxygen&#39;,&#39;Ubuntu&#39;,&#39;Cantarell&#39;,&#39;Fira Sans&#39;,&#39;Droid Sans&#39;,&#39;Helvetica Neue&#39;,sans-serif; color:#4E5C6E; font-size:14px; line-height:20px; margin-top:20px;">
                          <td class="content" colspan="2" valign="top" align="center" style="padding-left:90px; padding-right:90px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
                              <tbody>
                                <tr>
                                  <td align="center" valign="bottom" colspan="2" cellpadding="3">
                                    <img alt="Coinbase" width="80" src="https://www.coinbase.com/assets/app/succeed-green-dcb087e9c6e5265b4c49f75c9c2e1d08bc894bc54816d9a5a476611f631b2929.png"
                                    />
                                  </td>
                                </tr>
                                <tr>
                                  <td height="30" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td align="center">
                                    <div style="font-size: 22px; line-height: 32px; font-weight: 500; margin-left: 20px; margin-right: 20px; margin-bottom: 25px;">Your payment for '.$price_currency.'<span style="color: green">(' . $price_amount . ')</span> is '.$payment_status.'</div>
                                    <p style="font-size: 14px;">Your funds will be available in your Spacebank Business Account</p>
                                    <!-- <p style="font-size: 28px; font-weight: 400;">December 05, 2017</p>
                                    <p style="font-size: 14px; color:#9BA6B2;">We&#39;re unable to cancel started orders.
                                      <br/>Read more about why
                                      <a href="https://support.coinbase.com/customer/portal/articles/1403864"
                                      style="color: #2E7BC4">here</a>.</p> -->
                                  </td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td height="1" bgcolor="#DAE1E9"></td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td>
                                    <table style="width: 100%; border-collapse:collapse;">
                                      <tbody style="border: 0; padding: 0; margin-top:20px;">
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Transaction ID</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;"><span style="font-family:"Monaco", monospace;border:1px solid #DAE1E9;letter-spacing:2px;padding:5px 8px;border-radius:4px;background-color:#F4F7FA;color:#2E7BC4;">'.$payment_id.'</span>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td width="50%" valign="top" style="padding-bottom: 10px; padding-top: 10px;">Payment Status</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$payment_status.'</td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Created Date</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$created_at.'</td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Payment Amount</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$price_currency.'<span style="color: green">(' . $price_amount . ')</span></td>
                                        </tr>
                                        
                                         <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Order Description</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$order_description.'</span></td>
                                        </tr>
                                        
                                        
                                        <tr>
                                          <td height="24" &nbsp;=""></td>
                                        </tr>
                                        <tr>
                                          <td style="padding:0;margin:0;" height="1" bgcolor="#DAE1E9"></td>
                                          <td style="padding:0;margin:0;" height="1" bgcolor="#DAE1E9"></td>
                                        </tr>
                                        <tr>
                                          <td height="24" &nbsp;=""></td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Actually Paid Amount
                                            <br/>
                                          </td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;"> <strong>'.$actually_paid.'<span style="color: green">($' . $actually_paid_at_fiat . ')</strong>
                                            <br/>
                                          </td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Business Name</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$business_name.'</td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Business Email</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$businessemail.'</td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Payer Email</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$business_payer_email.'</td>
                                        </tr>
                                        <tr>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">Received Amount</td>
                                          <td style="padding-bottom: 10px; padding-top: 10px;">'.$outcome_currency.'<span style="color: green">($' . $outcome_amount . ')</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="10">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td>
                                    <a href="'.$button_link.'" style="display:block; font-size: 16px; padding:15px 25px; background-color:#3C90DF; color:#ffffff; border: 1px solid #2E7BC4; border-radius:4px; text-decoration:none; text-align:center; font-weight:500;"
                                    )>View Payment</a>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td height="1" bgcolor="#DAE1E9"></td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td>
                                    <div style="color:#48545d; font-size:14px; line-height:24px; text-align:center;">
                                      <p style="text-align: center; margin-bottom: 24px; font-size: 18px; font-weight: 500;">Frequently asked questions</p>
                                      <p>
                                        <a href="https://business.myspacebank.com#faq"
                                        style="color: #2E7BC4;">How long does a purchase or deposit take to complete?</a>
                                      </p>
                                      <p>
                                        <a href="https://business.myspacebank.com#faq"
                                        style="color: #2E7BC4;">How are fees applied when I buy or sell digital currency?</a>
                                      </p>
                                      <p>
                                        <a href="https://business.myspacebank.com#faq"
                                        style="color: #2E7BC4;">Can I cancel my payemnt?</a>
                                      </p>
                                    </div>
                                  </td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td height="1" bgcolor="#DAE1E9"></td>
                                </tr>
                                <tr>
                                  <td height="24" &nbsp;=""></td>
                                </tr>
                                <tr>
                                  <td align="center">
<span style="color:#9BA6B2; font-size:12px; line-height:19px;">
  <p>
    For customer service inquiries, please contact <a href="mailto:help@business.myspacebank.com" style="color: #2E7BC4" target="_blank">customer support</a>.
      Please include your reference code <strong>'.$payment_id.'</strong>.
  </p>

</span>

                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td height="60"></td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- Main CONTENT end here -->
                    <!-- PROMO column start here -->
                    <!-- Show mobile promo 75% of the time -->
                    <table id="promo" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:20px;">
                      <tbody>
                        <tr>
                          <td colspan="2" height="20"></td>
                        </tr>
                        <tr>
                          <td colspan="2" align="center"> <span style="font-size:14px; font-weight:500; margin-bottom:10px; color:#7E8A98; font-family: -apple-system,BlinkMacSystemFont,&#39;Segoe UI&#39;,&#39;Roboto&#39;,&#39;Oxygen&#39;,&#39;Ubuntu&#39;,&#39;Cantarell&#39;,&#39;Fira Sans&#39;,&#39;Droid Sans&#39;,&#39;Helvetica Neue&#39;,sans-serif;">Get the latest Coinbase App for your phone</span>

                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="20"></td>
                        </tr>
                        <tr>
                          <td valign="top" width="50%" align="right">
                            <a href="#"
                            style="display:inline-block;margin-right:10px;">
                              <img src="https://s3.amazonaws.com/app-public/Coinbase-email/iOS_app.png" height="40"
                              border="0" alt="Coinbase iOS mobile bitcoin wallet">
                            </a>
                          </td>
                          <td valign="top">
                            <a href="#"
                            style="display:inline-block;margin-left:5px;">
                              <img src="https://s3.amazonaws.com/app-public/Coinbase-email/Android_app.png"
                              height="40" border="0" alt="Coinbase Android mobile bitcoin wallet">
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" height="20"></td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- PROMO column end here -->
                    <!-- FOOTER start here -->
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tbody>
                        <tr>
                          <td height="10">&nbsp;</td>
                        </tr>
                        <tr>
                          <td valign="top" align="center"> <span style="font-family: -apple-system,BlinkMacSystemFont,&#39;Segoe UI&#39;,&#39;Roboto&#39;,&#39;Oxygen&#39;,&#39;Ubuntu&#39;,&#39;Cantarell&#39;,&#39;Fira Sans&#39;,&#39;Droid Sans&#39;,&#39;Helvetica Neue&#39;,sans-serif; color:#9EB0C9; font-size:10px;">&copy;
                            <a href="https://www.business.myspacebank.com/" target="_blank" style="color:#9EB0C9 !important; text-decoration:none;">Spacebank Business</a> 2017
                          </span>

                          </td>
                        </tr>
                        <tr>
                          <td height="50">&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- FOOTER end here -->
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>

</html>
  
  

</body>

</html>
';

?>