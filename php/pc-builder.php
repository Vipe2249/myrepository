<?php
session_start();

require_once('../db/dbcon.php');

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Check if 'add-to-cart' parameter is present in the URL and sanitize input
if (isset($_GET['add-to-cart'])) {
    $product_sku = sanitizeInput($_GET['add-to-cart']);
    
    // Prepare the query to prevent SQL injection
    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $product_sku);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check if the product is already in the cart
        if (isset($_SESSION['cart'][$product_sku])) {
            // If the product is already in the cart, increase its quantity
            $_SESSION['cart'][$product_sku]['quantity']++;
        } else {
            // If the product is not in the cart, add it to the cart
            $_SESSION['cart'][$product_sku] = array(
                'name' => sanitizeInput($product['name']),
                'price' => sanitizeInput($product['price']),
                'quantity' => 1,
                'image_url' => sanitizeInput($product['image_url']) // Sanitize image_url before adding to the cart
            );
        }
    }
}

// Calculate total quantity in the cart
$total_quantity = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
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
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
    <style></style>
</head>
<body>
    <?php include("../header/header.php"); ?>
    <div class="page-content">
        <div class="container">
            <div class="catnotfound">
                <h2>Under Construction</h2>
            </div>
        </div>
    </div>
    <?php include("../header/footer.php"); ?>
</body>
</html>