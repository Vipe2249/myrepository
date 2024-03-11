<?php
session_start();

// Function to remove an item from the cart by SKU
function removeItemFromCart($con, $sku) {
    // Sanitize input
    $product_sku = mysqli_real_escape_string($con, $sku);
    
    if(isset($_SESSION['cart'][$product_sku])) {
        unset($_SESSION['cart'][$product_sku]);
    }
}

require_once('../db/dbcon.php');

// Check if 'remove-from-cart' parameter is present in the URL
if(isset($_GET['remove-from-cart'])) {
    // Get the SKU of the product to remove from cart
    $product_sku = $_GET['remove-from-cart'];
    // Call the function to remove the item from the cart
    removeItemFromCart($con, $product_sku);
}

$total_quantity = 0;
$total_price = 0; 
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $sku => $item) {
        $total_quantity += $item['quantity'];
        $total_price += ($item['quantity'] * $item['price']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
</head>
<body>
<div class="page-content">
    <?php include("../header/header.php"); ?>
    <div class="container">
        <div class="cart-body">
            <h1>Cart</h1>
            <?php if(empty($_SESSION['cart'])) { ?>
                <p>Your cart is empty.</p>
            <?php } else { ?>
                <div class="cart-table">
                    <table>
                        <thead>
                            <tr>
                                <td>Product</td>
                                <td class="actions">Qty</td>
                                <td class="actions">Subtotal</td>
                                <td class="actions" >Remove</td>
                            </tr>
                        </thead>
                        <tbody class="cart-product-table">
                            <?php foreach($_SESSION['cart'] as $sku => $item) { ?>
                                <tr class="js-cart-item-<?php echo $sku; ?>">
                                    <td>
                                        <div class="cart-details" style="display: flex;">
                                            <div class="cart-thumbnail" style="display: flex;">
                                                <?php if(isset($item['image_url'])) { ?>
                                                    <img style="height: 100px; width: 100px;" src="<?php echo $item['image_url']; ?>" alt="">
                                                <?php } else { ?>
                                                    <img style="height: 100px; width: 100px;" src="default_image.jpg" alt="Default Image">
                                                <?php } ?>
                                            </div>
                                            <div class="cart-name">
                                                <p><?php echo $item['name']; ?></p>
                                                <p>R<?php echo $item['price']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="actions"><?php echo $item['quantity']; ?></td>
                                    <td class="actions">R<?php echo ($item['quantity'] * $item['price']); ?></td>
                                    <td class="actions"><a href="?remove-from-cart=<?php echo $sku; ?>" class="js-remove-from-cart" data-product-id="<?php echo $sku; ?>"><i class="fa-solid fa-trash" aria-hidden="true"></i></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="cart-summary" style="display: flex; justify-content: right; flex-direction: column; text-align: right; padding-top: 40px;">
                    <p style="text-align: right;"  class="cart-total">
                        <span style="margin-right: 10px;">Total:</span>
                        <span class="js-cart-total">R<?php echo $total_price; ?></span></span>
                    </p>
                    <div class="cart-buttons" style="padding-top: 20px;">
                        <a href="checkout" class="checkout">Proceed to Checkout</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php include("../header/footer.php");?>
</body>
</html>