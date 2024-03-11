<?php
session_start();

require_once('../db/dbcon.php');


if(isset($_GET['add-to-cart'])) {

    $product_sku = mysqli_real_escape_string($con, $_GET['add-to-cart']);
    

    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = mysqli_prepare($con, $query);
    

    mysqli_stmt_bind_param($stmt, "s", $product_sku);
    

    mysqli_stmt_execute($stmt);
    

    $result = mysqli_stmt_get_result($stmt);
    
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
        }
    }
}

$query = "SELECT * FROM categories WHERE parent_id IS NULL";
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
    <title>Products</title>
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
    <style></style>
</head>
<body>
    <?php include("../header/header.php");?>
    <div class="page-content">
        <div class="container">
            <div class="category-columns">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="category-card">
                        <a href="http://localhost/infinityware/c/<?php echo $row['url']; ?>">
                            <div class="img-container">
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="">
                            </div>
                        </a>
                        <div class="category-card-bottom">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php include("../header/footer.php");?>
</body>
</html>
