<?php
session_start();
require_once('../db/dbcon.php');

// Check if 'add-to-cart' parameter is present in the URL
if(isset($_GET['add-to-cart'])) {
    // Get the SKU of the product to add to cart
    $product_sku = $_GET['add-to-cart'];
    
    // Sanitize the input
    $product_sku = mysqli_real_escape_string($con, $product_sku);
    
    // Fetch the product details from the database using prepared statements
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

// Fetch products with stock greater than 0 using prepared statements
$query = "SELECT * FROM products WHERE stock > ?";
$stock_limit = 0;
$stmt = $con->prepare($query);
$stmt->bind_param("i", $stock_limit);
$stmt->execute();
$result = $stmt->get_result();

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
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script><style type="text/css" id="operaUserStyle"></style>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
    <style>
    </style>
</head>
<body>
    <div class="page-content">
    <?php include("../header/header.php");?>
    <div class="container">
        <div class="swiper-container swiper-initialized swiper-horizontal swiper-backface-hidden">
            <div class="swiper-wrapper js-swiper-wrapper" id="swiper-wrapper-d8711c96c49e104b7" aria-live="polite">
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 5">
                        <div class="columns">
                            <div class="product-card">
                            <a href="http://localhost/infinityware/product/<?php echo htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="img-container">
                                    <img src="<?php echo htmlspecialchars($row['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                </div>
                                </a>
                                <div class="product-card-bottom">
                                    <h3 class="product-card-category"><?php echo htmlspecialchars($row['Category'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <h3><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="product-card-price">R<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                
                                <div class="addtocart">
                                    <a href="?add-to-cart=<?php echo htmlspecialchars($row['sku'], ENT_QUOTES, 'UTF-8'); ?>"> 
                                        <button class="addtocart js-add-to-cart" data-product-id="<?php echo htmlspecialchars($row['sku'], ENT_QUOTES, 'UTF-8'); ?>" data-quantity="1">
                                            ADD TO CART
                                        </button>
                                </div>
                                </a>
                            </div>
                            
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        
    </div>
    </div>
    <?php include("../header/footer.php");?>
    
    
    
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

</body>
</html>