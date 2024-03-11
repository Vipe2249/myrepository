<?php 
session_start();



$total_price = 0; // Initialize total price variable
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_price += ($item['quantity'] * $item['price']);
    }
}

// Check if the variable is sent via POST request
if(isset($_POST['variable'])) {
    // Retrieve the value sent from JavaScript
    $jsVariable = $_POST['variable'];
    
    // Assuming $total_price is already defined or calculated somewhere
    
    // Add the JavaScript variable to $total_price and echo the result
    $total = $total_price + $jsVariable;
    echo $total;
}


?>