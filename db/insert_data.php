<?php
// Check if the 'products' data is received
if (isset($_POST['products'])) {
    // Decode the JSON string into an array of products
    $products = json_decode($_POST['products'], true);

    if ($products === null) {
        // Handle JSON decoding error
        die("Error: Invalid JSON data.");
    }

    // Database connection parameters
    // (Replace these with your actual database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "infinityware";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the SQL statements
    $stmt_insert = $conn->prepare("INSERT INTO products (sku, name, price, description, image_url, stock, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_update = $conn->prepare("UPDATE products SET price = ?, description = ?, image_url = ?, stock = ? WHERE sku = ?");

    // Check if prepared statements were successfully prepared
    if (!$stmt_insert || !$stmt_update) {
        die("Error: Failed to prepare SQL statements.");
    }

    // Insert or update each product in the database
    foreach ($products as $product) {
        // Validate product data (e.g., ensure required fields are present)

        $sku = $product['sku'];
        $name = $product['name'];
        $price = $product['price'];
        $description = $product['description'];
        $imageUrl = $product['image_url'];
        $stock = $product['stock'];
        $url = $product['url'];

        // Perform additional data validation/sanitization if necessary

        // Check if product with the same SKU already exists
        $stmt_check = $conn->prepare("SELECT sku FROM products WHERE sku = ?");
        $stmt_check->bind_param("s", $sku);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $stmt_check->close();

        if ($result->num_rows > 0) {
            // Update existing product
            $stmt_update->bind_param("dssds", $price, $description, $imageUrl, $stock, $sku);
            $stmt_update->execute();
            echo "Product with SKU: $sku updated successfully.<br>";
        } else {
            // Insert new product
            $stmt_insert->bind_param("ssdssds", $sku, $name, $price, $description, $imageUrl, $stock, $url);
            $stmt_insert->execute();
            echo "New product with SKU: $sku inserted successfully.<br>";
        }
    }

    // Close statements and connection
    $stmt_insert->close();
    $stmt_update->close();
    $conn->close();

    // Return success message
    echo "Data inserted or updated successfully.";
} else {
    // Return error message if 'products' data is not received
    echo "Error: No data received.";
}
?>