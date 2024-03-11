<?php
session_start();

require_once('../db/dbcon.php');

// Check if 'add-to-cart' parameter is present in the URL
if(isset($_GET['add-to-cart'])) {
    // Get the SKU of the product to add to cart
    $product_sku = $_GET['add-to-cart'];
    
    // Fetch the product details from the database
    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $product_sku);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check if the product is already in the cart
        if(isset($_SESSION['cart'][$product_sku])) {
            // If the product is already in the cart, increase its quantity
            $_SESSION['cart'][$product_sku]['quantity']++;
        } else {
            // If the product is not in the cart, add it to the cart
            $_SESSION['cart'][$product_sku] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image_url' => $product['image_url'] // Add image_url to the cart
            );
        }
    }
}
// Calculate total quantity in the cart
$total_quantity = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_quantity += $item['quantity'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Found</title>
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script><style type="text/css" id="operaUserStyle"></style>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
    <style>
    </style>
</head>
<body>

    <?php include("../header/header.php");?>
    <div class="page-content">
    <div class="container">
        <div class="catnotfound">
            <h2>The category or product you searched for does not match any of our records</h2>
        </div>
    </div>
    </div>
    <?php include("../header/footer.php");?>
</body>
</html>