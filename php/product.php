<?php
session_start();
require_once('../db/dbcon.php');

if(isset($_GET['url'])) {
    $product_url = $_GET['url'];
    $query = "SELECT * FROM products WHERE url = '$product_url'";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
    }
} else {

}

if(isset($_GET['add-to-cart'])) {

    $product_sku = $_GET['add-to-cart'];
    

    $query = "SELECT * FROM products WHERE sku = '$product_sku'";
    $result = mysqli_query($con, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        

        if(isset($_SESSION['cart'][$product_sku])) {

            $_SESSION['cart'][$product_sku]['quantity']++;
        } else {

            $_SESSION['cart'][$product_sku] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image_url' => $product['image_url'] 
            );
        }
    }
}

$query = "SELECT * FROM products";
$result = mysqli_query($con, $query);

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
    <title><?php echo $product['name']; ?></title>
    
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
</head>
<body>
<div class="page-content">
    <?php include("../header/header.php"); ?>
    <div class="container">
        <?php if(isset($product)) { ?>
            <div class="product-page">
                <div class="product-details">
                    <div class="product-image-gallery">
                        <img class="product-image" height="800px" src="<?php echo $product['image_url']; ?>" alt="">
                    </div>
                    <div class="product-summary">
                        <p class="product-price">R<?php echo $product['price']; ?></p>
                        <h1 class="product-title"><?php echo $product['name']; ?></h1>
                        <div class="product-short-description">
                            <?php echo $product['short_description']; ?>
                        </div>
                        <div class="product-additional-info">
                        <div class="additional-info-item">
                        <svg width="15" height="15" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.6 0 12 0zm6.2 9.5-7.6 7.6c-.4.4-1.1.4-1.5 0l-3.3-3.3c-.4-.4-.4-1.1 0-1.5.4-.4 1.1-.4 1.5 0l2.5 2.5L16.7 8c.4-.4 1.1-.4 1.5 0 .4.4.4 1.1 0 1.5z"></path>
                        </svg> <span>In stock with Supplier</span> <br>
                        </div>
                    
                        <div class="additional-info-item">
                        <svg width="15" height="15" viewBox="0 0 24 24">
                            <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.6 0 12 0zm6.2 9.5-7.6 7.6c-.4.4-1.1.4-1.5 0l-3.3-3.3c-.4-.4-.4-1.1 0-1.5.4-.4 1.1-.4 1.5 0l2.5 2.5L16.7 8c.4-.4 1.1-.4 1.5 0 .4.4.4 1.1 0 1.5z"></path>
                        </svg> <span >Handling time before dispatch; 1 - 2 business days</span>
                        </div>
                    </div>
                        <div class="cart-actions">
                            <form method="GET" action="">
                                <input type="hidden" name="add-to-cart" value="<?php echo $product['sku']; ?>">
                                <button class="product-addtocart" type="submit">ADD TO CART</button>
                            </form>
                        </div>
                        <div class="product-meta">
                            <span><strong>SKU:</strong> <span><?php echo $product['sku']; ?></span></span>
                        </div>
                    </div>
                </div>
                <div class="product-page-description">
                    <div class="product-description-buttons">
                        <button class="description-buttons" onclick="showTab('description')">Description</button><br>
                        <button class="description-buttons" onclick="showTab('specifications')">Specifications</button>
                    </div>
                    <div id="description-tab" class="product-page-description-article">
                        <p><?php echo $product['description']; ?></p>
                    </div>
                    <div id="specifications-tab" class="product-page-description-article" style="display: none;">
                        <?php echo $product['specifications']; ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <p>Product not found.</p>
        <?php } ?>
    </div>
    <?php include("../header/footer.php");?>
    <script>
        function showTab(tabName) {

            var tabs = document.querySelectorAll('.product-page-description-article');
            tabs.forEach(function(tab) {
                tab.style.display = 'none';
            });


            document.getElementById(tabName + '-tab').style.display = 'block';
        }


        var anchorTags = document.querySelectorAll('.product-description-buttons a');
        anchorTags.forEach(function(tag) {
            tag.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });
    </script>
</body>
</html>
