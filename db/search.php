<?php
// Check if the search query is provided
if (isset($_GET['query'])) {
    // Sanitize the search query to prevent HTML injection
    $search_query = htmlspecialchars($_GET['query']);
    
    // Preprocess the search query to handle multi-word search terms
    $search_terms = explode(" ", $search_query);
    $processed_search_terms = array_map('preprocessSearchQuery', $search_terms);

    // Perform the search query against your database or any other data source
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

    // Array to store matching category URLs
    $matching_category_urls = [];

    // Check if there is an exact match for any of the processed search terms in the category table
    $category_exact_query = "SELECT * FROM categories WHERE name = ?";
    $stmt_exact_category = $conn->prepare($category_exact_query);
    $stmt_exact_category->bind_param("s", $term);

    foreach ($processed_search_terms as $term) {
        $stmt_exact_category->execute();
        $result_exact_category = $stmt_exact_category->get_result();

        if ($result_exact_category->num_rows > 0) {
            // Collect the URLs of matching categories
            while ($row = $result_exact_category->fetch_assoc()) {
                $matching_category_urls[] = $row['url'];
            }
        }
    }

    // Check if "Intel CPUs" category exists in the matching categories
    $intel_cpus_url = array_search('intel-cpus', $matching_category_urls);
    if ($intel_cpus_url !== false) {
        // Redirect to "Intel CPUs" category page
        header("Location: http://localhost/infinityware/c/intel-cpus");
        exit();
    }

    // Perform a loose search for similar terms in the category table
    $category_loose_query = "SELECT * FROM categories WHERE name LIKE ?";
    $stmt_loose_category = $conn->prepare($category_loose_query);
    $stmt_loose_category->bind_param("s", $search_term);

    foreach ($processed_search_terms as $term) {
        $search_term = "%" . $term . "%";
        $stmt_loose_category->execute();
        $result_loose_category = $stmt_loose_category->get_result();

        if ($result_loose_category->num_rows > 0) {
            // Collect the URLs of matching categories
            while ($row = $result_loose_category->fetch_assoc()) {
                $matching_category_urls[] = $row['url'];
            }
        }
    }

    // Redirect to the first matching category page, if any
    if (!empty($matching_category_urls)) {
        header("Location: http://localhost/infinityware/c/" . $matching_category_urls[0]);
        exit();
    }

    // Array to store matching product URLs
    $matching_product_urls = [];

    // Check if there is an exact match for any of the processed search terms in the product table
    $product_exact_query = "SELECT * FROM products WHERE name = ?";
    $stmt_exact_product = $conn->prepare($product_exact_query);
    $stmt_exact_product->bind_param("s", $term);

    foreach ($processed_search_terms as $term) {
        $stmt_exact_product->execute();
        $result_exact_product = $stmt_exact_product->get_result();

        if ($result_exact_product->num_rows > 0) {
            // Collect the URLs of matching products
            while ($row = $result_exact_product->fetch_assoc()) {
                $matching_product_urls[] = $row['url'];
            }
        }
    }

    // Redirect to the first matching product page, if any
    if (!empty($matching_product_urls)) {
        header("Location: http://localhost/infinityware/product/" . $matching_product_urls[0]);
        exit();
    }

    // Perform a loose search for similar terms in the product table
    $product_loose_query = "SELECT * FROM products WHERE name LIKE ?";
    $stmt_loose_product = $conn->prepare($product_loose_query);
    $stmt_loose_product->bind_param("s", $search_term);

    foreach ($processed_search_terms as $term) {
        $search_term = "%" . $term . "%";
        $stmt_loose_product->execute();
        $result_loose_product = $stmt_loose_product->get_result();

        if ($result_loose_product->num_rows > 0) {
            // Collect the URLs of matching products
            while ($row = $result_loose_product->fetch_assoc()) {
                $matching_product_urls[] = $row['url'];
            }
        }
    }

    // Redirect to the first matching product page, if any
    if (!empty($matching_product_urls)) {
        header("Location: http://localhost/infinityware/product/" . $matching_product_urls[0]);
        exit();
    }

    // No exact or similar match found for product or category
    header("Location: http://localhost/infinityware/catnotfound");
        exit();

    // Close statements and connection
    $stmt_exact_category->close();
    $stmt_loose_category->close();
    $stmt_exact_product->close();
    $stmt_loose_product->close();
    $conn->close();
}

function preprocessSearchQuery($search_query) {
    // Example: Convert "CPUs" to "processors"
    return str_replace("CPUs", "processors", $search_query);
}
?>
