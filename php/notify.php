<?php
// Tell Payfast that this page is reachable by triggering a header 200
http_response_code(200);

define('SANDBOX_MODE', true);
$pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';

// Posted variables from ITN
$pfData = $_POST;

// Sanitize posted variables
foreach ($pfData as $key => $val) {
    $pfData[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// Convert posted variables to a string
$pfParamString = '';
foreach ($pfData as $key => $val) {
    if ($key !== 'signature') {
        $pfParamString .= $key . '=' . urlencode($val) . '&';
    } else {
        break;
    }
}

$pfParamString = rtrim($pfParamString, '&');

function pfValidSignature($pfData, $pfParamString, $pfPassphrase = null) {
    // Calculate security signature
    if ($pfPassphrase === null) {
        $tempParamString = $pfParamString;
    } else {
        $tempParamString = $pfParamString . '&passphrase=' . urlencode($pfPassphrase);
    }

    $signature = md5($tempParamString);
    return ($pfData['signature'] === $signature);
}

function pfValidIP() {
    // List of valid IP addresses
    $validIps = [
        '154.0.161.0',  // Example IP address
        '154.0.161.1'   // Example IP address
    ];

    // Get referrer IP
    $referrerIp = $_SERVER['REMOTE_ADDR'];

    return in_array($referrerIp, $validIps);
}

function pfValidPaymentData($cartTotal, $pfData) {
    return abs((float) $cartTotal - (float) $pfData['amount_gross']) <= 0.01;
}

function pfValidServerConfirmation($pfParamString, $pfHost = 'sandbox.payfast.co.za') {
    // Use cURL (if available)
    if (function_exists('curl_version')) {
        // Variable initialization
        $url = 'https://' . $pfHost . '/eng/query/validate';

        // Create cURL object
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pfParamString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // Execute cURL
        $response = curl_exec($ch);
        curl_close($ch);

        return ($response === 'VALID');
    }
    return false;
}

$myFile = fopen('notify.txt', 'wb') or die();

$check1 = pfValidSignature($pfData, $pfParamString);
$check2 = pfValidIP();
$check3 = pfValidPaymentData("1105", $pfData);
$check4 = pfValidServerConfirmation($pfParamString, $pfHost);

if ($check1 && $check2 && $check3 && $check4) {
    // All checks have passed, the payment is successful
} else {
    // Some checks have failed, check payment manually and log for investigation
}
?>
