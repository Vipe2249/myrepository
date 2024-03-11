<?php

session_start();
/**
 * @param array $data
 * @param null $passPhrase
 * @return string
 */
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
    'return_url' => 'http://www.yourdomain.co.za/return.php',
    'cancel_url' => 'http://www.yourdomain.co.za/cancel.php',
    'notify_url' => 'http://www.yourdomain.co.za/notify.php',
    // Buyer details
    'name_first' => 'First Name',
    'name_last'  => 'Last Name',
    'email_address'=> 'test@test.com',
    // Transaction details
    'm_payment_id' => '1234', //Unique payment ID to pass through to notify_url
    'amount' => number_format( sprintf( '%.2f', $cartTotal ), 2, '.', '' ),
    'item_name' => 'Order#123'
);

$signature = generateSignature($data, $passphrase);
$data['signature'] = $signature;

// If in testing mode make use of either sandbox.payfast.co.za or www.payfast.co.za
$testingMode = true;
$pfHost = $testingMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
$htmlForm = '<form action="https://'.$pfHost.'/eng/process" method="post">';
foreach($data as $name=> $value)
{
    $htmlForm .= '<input name="'.$name.'" type="hidden" value=\''.$value.'\' />';
}
$htmlForm .= '<button type="submit" class="placeorder">Place Order </button></form>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interval Page</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="street-address" style="width: 100%;">
                    <strong>Street Address</strong>
                    <input id="deliveryStreetAddress" style="width: 100%;" type="text" placeholder="Street Address and house number">
                    <input id="deliveryApartment" style="width: 100%;" type="text" placeholder="Apartment, suite, etc (optional)">
                    <strong>City</strong>
                    <input id="deliveryCity" style="width: 100%;" type="text" placeholder="Town / City">
                    <select id="deliveryProvince" style="width: 100%;" name="">
                        <option>Select an option</option>
                        <option>Eastern Cape</option>
                        <option>Free State</option>
                        <option>Gauteng</option>
                        <option>KwaZulu-Natal</option>
                        <option>Limpopo</option>
                        <option>Mpumalanga</option>
                        <option>Northern Cape</option>
                        <option>North West</option>
                        <option>Western Cape</option>
                    </select>
                    <input id="deliveryPostalCode" style="width: 100%;" type="text" placeholder="Postal / Zip Code">
                </div>
<div class="summary-shipping"></div>
<div id="result"></div>

<script>
function getFormData() {
    return {
        collection_address: {
            type: "business",
            company: "uAfrica.com",
            street_address: "1188 Lois Avenue",
            local_area: "Menlyn",
            city: "Pretoria",
            zone: "Gauteng",
            country: "ZA",
            code: "0181",
            lat: -25.7863272,
            lng: 28.277583
        },
        delivery_address: {
            type: "business",
            company: "uAfrica.com",
            street_address: document.getElementById("deliveryStreetAddress").value,
            local_area: "Menlyn",
            city: document.getElementById("deliveryCity").value,
            zone: document.getElementById("deliveryProvince").value,
            country: "ZA",
            code: document.getElementById("deliveryPostalCode").value,
        },
        parcels: [
            {
                submitted_length_cm: 42.5, // You might need to capture this data from the form if available
                submitted_width_cm: 38.5, // You might need to capture this data from the form if available
                submitted_height_cm: 5.5, // You might need to capture this data from the form if available
                submitted_weight_kg: 3 // You might need to capture this data from the form if available
            }
        ],
        declared_value: 1500, // You might need to capture this data from the form if available
        collection_min_date: "2021-05-21", // You might need to capture this data from the form if available
        delivery_min_date: "2021-05-21" // You might need to capture this data from the form if available
    };
}

// Function to make the API request
function getShippingRates() {
    const formData = getFormData();
    const apiUrl = "https://api.shiplogic.com/v2/rates";
    const bearerToken = "a601d99c75fc4c64b5a64288f97d52b4"; // Change this to your actual bearer token

    fetch(apiUrl, {
        method: "POST",
        headers: {
            "Authorization": `Bearer ${bearerToken}`,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // Handle the response data here
        console.log(data);

        // Extract the rate from the response
        const rate = data.rates[0].rate; // Assuming there's only one rate in the array
        document.querySelector(".summary-shipping").innerHTML = `R${rate}`
        
        // Now you can use the 'rate' variable as needed
        console.log("Rate:", rate);
        
        // Send the rate value via AJAX to process.php
        $.ajax({
            type: 'POST',
            url: 'process.php',
            data: {variable: rate},
            success: function(response){
                $('#result').html(response);
            }
        });
        
    })
    .catch(error => {
        // Handle errors here
        console.error("Error:", error);
    });
    
}

// Call getShippingRates initially
getShippingRates();

// Set interval to call getShippingRates every 5 seconds
setInterval(getShippingRates, 5000);

</script>

</body>
</html>