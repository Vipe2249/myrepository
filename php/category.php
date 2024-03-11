<?php
session_start();
require_once('../db/dbcon.php');

// Function to remove an item from the cart by SKU
function removeItemFromCart($con, $sku) {
    // Sanitize input
    $product_sku = mysqli_real_escape_string($con, $sku);
    
    if(isset($_SESSION['cart'][$product_sku])) {
        unset($_SESSION['cart'][$product_sku]);
    }
}

if(isset($_GET['add-to-cart'])) {
    // Get the SKU of the product to add to cart
    $product_sku = $_GET['add-to-cart'];
    
    // Fetch the product details from the database
    $query = "SELECT * FROM products WHERE sku = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $product_sku);
    $stmt->execute();
    $stmt->bind_result($sku, $name, $price, $image_url); // Bind result variables
    $stmt->fetch();
    $stmt->close();
    
    if($sku) {
        // Check if the product is already in the cart
        if(isset($_SESSION['cart'][$sku])) {
            // If the product is already in the cart, increase its quantity
            $_SESSION['cart'][$sku]['quantity']++;
        } else {
            // If the product is not in the cart, add it to the cart
            $_SESSION['cart'][$sku] = array(
                'name' => $name,
                'price' => $price,
                'quantity' => 1,
                'image_url' => $image_url // Add image_url to the cart
            );
        }
    }
}

$total_quantity = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_quantity += $item['quantity'];
    }
}

// Check if category URL is present in the URL
if(isset($_GET['url'])) {
    $category_url = $_GET['url'];
    
    // Fetch the category details from the database based on URL
    $query_category = "SELECT * FROM categories WHERE url = ?";
    $stmt_category = $con->prepare($query_category);
    $stmt_category->bind_param("s", $category_url);
    $stmt_category->execute();
    $stmt_category->bind_result($category_id, $category_name, $category_url, $parent_id, $image_url); // Bind result variables
    $stmt_category->fetch();
    $stmt_category->close();
    
    if($category_id) {
        // Fetch products belonging to the selected category including child categories
        $query_products = "SELECT p.* FROM products p
                           INNER JOIN categories c ON p.category_id = c.id
                           WHERE c.id = ? OR c.parent_id = ?";
        $stmt_products = $con->prepare($query_products);
        $stmt_products->bind_param("ii", $category_id, $category_id);
        $stmt_products->execute();
        $result_products = $stmt_products->get_result();
        
        // Check if the selected category has child categories
        $query_child_categories = "SELECT * FROM categories WHERE parent_id = ?";
        $stmt_child_categories = $con->prepare($query_child_categories);
        $stmt_child_categories->bind_param("i", $category_id);
        $stmt_child_categories->execute();
        $result_child_categories = $stmt_child_categories->get_result();
    } else {
        // Handle invalid category URL
        echo "Category not found.";
        exit;
    }
} else {
    // Handle missing category URL
    echo "Category URL is missing.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category_name; ?></title>
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
</head>
<body>

    <?php include("../header/header.php"); ?>
    <div class="page-content">
    <div class="container">
    <h2><?php echo $category_name; ?></h2>
    <?php if($result_child_categories->num_rows > 0) { ?>
            <div class="category-columns">
                <?php while ($child_category = $result_child_categories->fetch_assoc()) { ?>
                    <div class="category-card">
                        <a href="http://localhost/infinityware/c/<?php echo $child_category['url']; ?>">
                            <div class="img-container">
                                <img src="<?php echo $child_category['image_url']; ?>" alt="">
                            </div>
                        </a>
                        <div class="category-card-bottom">
                            <h3><?php echo $child_category['name']; ?></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <h2>Check out our range of <?php echo $category_name; ?></h2>
        <?php } ?>
        
        <?php if($result_products->num_rows > 0) { ?>
           
            <div class="category-columns" style="gap: 10px">
                <?php while ($product = $result_products->fetch_assoc()) { ?>
                    
                    <div class="product-card">
                        <a href="http://localhost/infinityware/product/<?php echo $product['url']; ?>">
                            <div class="img-container">
                                <img src="<?php echo $product['image_url']; ?>" alt="">
                            </div>
                        </a>
                        <div class="product-card-bottom">
                            <h3 class="product-card-category"><?php echo $product['Category']; ?></h3>
                            <h3><?php echo $product['name']; ?></h3>
                            <p class="product-card-price">R<?php echo $product['price']; ?></p>
                        </div>
                        <div class="addtocart">
                            <a href="?url=<?php echo $category_url; ?>&add-to-cart=<?php echo $product['sku']; ?>">
                                <button class="addtocart js-add-to-cart" data-product-id="<?php echo $product['sku']; ?>" data-quantity="1">
                                    ADD TO CART
                                </button>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }?>
        

    </div>
    </div>

<?php include("../header/footer.php");?>

</body>
</html>
