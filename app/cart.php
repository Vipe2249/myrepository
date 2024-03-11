<?php
session_start();

require_once('../db/dbcon.php');

function removeItemFromCart($sku, $con) {
    if(isset($_SESSION['cart'][$sku])) {
        unset($_SESSION['cart'][$sku]);
    }
}

if(isset($_GET['remove-from-cart'])) {
    $product_sku = $_GET['remove-from-cart'];
    removeItemFromCart($product_sku, $con);
}

$total_quantity = 0;
$total_price = 0; 
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $sku => $item) {
        $query = "SELECT L, W, H, KG FROM Products WHERE SKU = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $total_quantity += $item['quantity'];
        $total_price += ($item['quantity'] * $item['price']);
    }
}

$stmt->close(); 
$con->close(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("./header.php"); ?>
    <div class="container">
        <div class="cart-body">
            <h1>Cart</h1>
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
                        <?php
                        if(isset($_SESSION['cart'])) {
                            foreach($_SESSION['cart'] as $sku => $item) {
                                echo '<tr class="js-cart-item-' . $sku . '">';
                                echo '<td>';
                                echo '<div class="cart-details" style="display: flex;">';
                                echo '<div class="cart-thumbnail" style="display: flex;">';
                                if(isset($item['image_url'])) {
                                    echo '<img style="height: 100px; width: 100px;" src="' . $item['image_url'] . '" alt="">';
                                } else {
                                    echo '<img style="height: 100px; width: 100px;" src="default_image.jpg" alt="Default Image">';
                                }
                                echo '</div>';
                                echo '<div class="cart-name">';
                                echo '<p>' . $item['name'] . '</p>';
                                echo '<p>L: ' . $product['L'] . ' | W: ' . $product['W'] . ' | H: ' . $product['H'] . ' | KG: ' . $product['KG'] . '</p>'; // Echo fetched attributes
                                echo '<p>R' . $item['price'] . '</p>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td class="actions">' . $item['quantity'] . '</td>';
                                echo '<td class="actions">R' . ($item['quantity'] * $item['price']) . '</td>';
                                echo '<td class="actions"><a href="?remove-from-cart=' . $sku . '" class="js-remove-from-cart" data-product-id="' . $sku . '"><i class="fa-solid fa-trash" aria-hidden="true"></i></a></td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="cart-summary" style="display: flex; justify-content: right; flex-direction: column; text-align: right; padding-top: 40px;">
                <p style="text-align: right;"  class="cart-total">
                    <span style="margin-right: 10px;">Total:</span>
                    <span class="js-cart-total">R<?php echo $total_price; ?></span></span>
                </p>
                <div class="cart-buttons" style="padding-top: 20px;">
                    <a href="checkout.php" class="checkout">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
