<?php

if (isset($_POST['products'])) {
    
    $products = json_decode($_POST['products'], true);

    if ($products === null) {
        
        die("Error: Invalid JSON data.");
    }


    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "infinityware";

  
    $conn = new mysqli($servername, $username, $password, $dbname);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt_insert = $conn->prepare("INSERT INTO products (sku, name, price, description, image_url, stock, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_update = $conn->prepare("UPDATE products SET price = ?, description = ?, image_url = ?, stock = ? WHERE sku = ?");


    if (!$stmt_insert || !$stmt_update) {
        die("Error: Failed to prepare SQL statements.");
    }


    foreach ($products as $product) {


        $sku = $product['sku'];
        $name = $product['name'];
        $price = $product['price'];
        $description = $product['description'];
        $imageUrl = $product['image_url'];
        $stock = $product['stock'];
        $url = $product['url'];

     

        $stmt_check = $conn->prepare("SELECT sku FROM products WHERE sku = ?");
        $stmt_check->bind_param("s", $sku);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $stmt_check->close();

        if ($result->num_rows > 0) {

            $stmt_update->bind_param("dssds", $price, $description, $imageUrl, $stock, $sku);
            $stmt_update->execute();
            echo "Product with SKU: $sku updated successfully.<br>";
        } else {
  
            $stmt_insert->bind_param("ssdssds", $sku, $name, $price, $description, $imageUrl, $stock, $url);
            $stmt_insert->execute();
            echo "New product with SKU: $sku inserted successfully.<br>";
        }
    }

    $stmt_insert->close();
    $stmt_update->close();
    $conn->close();


    echo "Data inserted or updated successfully.";
} else {

    echo "Error: No data received.";
}
?>
