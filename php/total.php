<?php 
session_start();



$total_price = 0; 
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_price += ($item['quantity'] * $item['price']);
    }
}


if(isset($_POST['variable'])) {

    $jsVariable = $_POST['variable'];

    $total = $total_price + $jsVariable;
    echo $total;
}


?>
