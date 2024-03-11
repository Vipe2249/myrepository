<?php

session_start();

require_once('../db/dbcon.php');

// Check if 'add-to-cart' parameter is present in the URL
if(isset($_GET['add-to-cart'])) {
    // Get the SKU of the product to add to cart
    $product_sku = $_GET['add-to-cart'];
    
    // Fetch the product details from the database
    $query = "SELECT * FROM products WHERE sku = '$product_sku'";
    $result = mysqli_query($con, $query);
    
    if(mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        
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
$query = "SELECT * FROM products";
$result = mysqli_query($con, $query);

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
    <title>Document</title>
    <link rel="stylesheet" href="../styles/styles.css">
    
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script><style type="text/css" id="operaUserStyle"></style>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <style>
    </style>
</head>
<body>
    <?php include("./header.php");?>
    <div class="container">
        <div class="swiper-container swiper-initialized swiper-horizontal swiper-backface-hidden">
            <div class="swiper-wrapper js-swiper-wrapper" id="swiper-wrapper-d8711c96c49e104b7" aria-live="polite">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 5">
                        <div class="columns">
                            <div class="product-card">
                                <div class="img-container">
                                    <img src="<?php echo $row['image_url']; ?>" alt="">
                                </div>
                                <div class="product-card-bottom">
                                    <h3 class="product-card-category"></h3>
                                    <h3><?php echo $row['name']; ?></h3>
                                    <p class="product-card-price">R<?php echo $row['price']; ?></p>
                                </div>
                                <div class="addtocart">
                                    <a href="?add-to-cart=<?php echo $row['sku']; ?>"> 
                                        <button class="addtocart js-add-to-cart" data-product-id="<?php echo $row['sku']; ?>" data-quantity="1">
                                            ADD TO CART
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    
    <script>
        var swiper = new Swiper('.swiper-container', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // when window width is <= 0px
                0: {
                    slidesPerView: 2,
                },
                // when window width is <= 768px
                700: {
                    slidesPerView: 3,
                },
                // when window width is <= 992px
                992: {
                    slidesPerView: 5,
                },
            },
        });
    </script>

<script>
    
</script>
</body>
</html>
