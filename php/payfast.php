<?php



/**
 * 
 * 
 * 
 * @param array $data
 * @param null $passPhrase
 * @return string
 */

 
// Assuming $jsVariable is available from process.php
$jsVariable = isset($jsVariable) ? $jsVariable : 0; // Default value if $jsVariable is not set

// Update the data array with $jsVariable
$data['custom_int1'] = $jsVariable;


function generateSignature($data, $passPhrase = null) {
    // Create parameter string
    $pfOutput = '';
    foreach( $data as $key => $val ) {
        if($val !== '') {
            $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
        }
    }
    // Remove last ampersand
    $getString = substr( $pfOutput, 0, -1 );
    if( $passPhrase !== null ) {
        $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
    }
    return md5( $getString );
} 

// here

$cartTotal = $total;
$passphrase = 'jt7NOE43FZPn';
$data = array(
    // Merchant details
    'merchant_id' => '10000100',
    'merchant_key' => '46f0cd694581a',
    'return_url' => 'http://localhost/infinityware/php/index.php',
    'cancel_url' => 'http://www.yourdomain.co.za/cancel.php',
    'notify_url' => 'http://localhost/infinityware/php/notify.php',
    // Buyer details
    'name_first' => 'First Name',
    'name_last'  => 'Last Name',
    'email_address'=> 'megawluke@gmail.com',
    // Transaction details
    'm_payment_id' => '1234', //Unique payment ID to pass through to notify_url
    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
    'item_name' => 'Order#123',
    'payment_method' => 'cc'
);

$signature = generateSignature($data, $passphrase);
$data['signature'] = $signature;
$htmlForm = "";
// If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
$testingMode = true;
$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
foreach($data as $name=> $value)
{
    $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
}
$htmlForm .= '<button type="submit" class="placeorder">Place Order</button>';

