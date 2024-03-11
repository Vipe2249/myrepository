<?php

if (isset($_GET['query'])) {

    $search_query = htmlspecialchars($_GET['query']);
    

    $search_terms = explode(" ", $search_query);
    $processed_search_terms = array_map('preprocessSearchQuery', $search_terms);


    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "infinityware";


    $conn = new mysqli($servername, $username, $password, $dbname);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $matching_category_urls = [];


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

    $intel_cpus_url = array_search('intel-cpus', $matching_category_urls);
    if ($intel_cpus_url !== false) {

        header("Location: http://localhost/infinityware/c/intel-cpus");
        exit();
    }


    $category_loose_query = "SELECT * FROM categories WHERE name LIKE ?";
    $stmt_loose_category = $conn->prepare($category_loose_query);
    $stmt_loose_category->bind_param("s", $search_term);

    foreach ($processed_search_terms as $term) {
        $search_term = "%" . $term . "%";
        $stmt_loose_category->execute();
        $result_loose_category = $stmt_loose_category->get_result();

        if ($result_loose_category->num_rows > 0) {

            while ($row = $result_loose_category->fetch_assoc()) {
                $matching_category_urls[] = $row['url'];
            }
        }
    }


    if (!empty($matching_category_urls)) {
        header("Location: http://localhost/infinityware/c/" . $matching_category_urls[0]);
        exit();
    }


    $matching_product_urls = [];


    $product_exact_query = "SELECT * FROM products WHERE name = ?";
    $stmt_exact_product = $conn->prepare($product_exact_query);
    $stmt_exact_product->bind_param("s", $term);

    foreach ($processed_search_terms as $term) {
        $stmt_exact_product->execute();
        $result_exact_product = $stmt_exact_product->get_result();

        if ($result_exact_product->num_rows > 0) {

            while ($row = $result_exact_product->fetch_assoc()) {
                $matching_product_urls[] = $row['url'];
            }
        }
    }


    if (!empty($matching_product_urls)) {
        header("Location: http://localhost/infinityware/product/" . $matching_product_urls[0]);
        exit();
    }


    $product_loose_query = "SELECT * FROM products WHERE name LIKE ?";
    $stmt_loose_product = $conn->prepare($product_loose_query);
    $stmt_loose_product->bind_param("s", $search_term);

    foreach ($processed_search_terms as $term) {
        $search_term = "%" . $term . "%";
        $stmt_loose_product->execute();
        $result_loose_product = $stmt_loose_product->get_result();

        if ($result_loose_product->num_rows > 0) {

            while ($row = $result_loose_product->fetch_assoc()) {
                $matching_product_urls[] = $row['url'];
            }
        }
    }


    if (!empty($matching_product_urls)) {
        header("Location: http://localhost/infinityware/product/" . $matching_product_urls[0]);
        exit();
    }


    header("Location: http://localhost/infinityware/catnotfound");
        exit();


    $stmt_exact_category->close();
    $stmt_loose_category->close();
    $stmt_exact_product->close();
    $stmt_loose_product->close();
    $conn->close();
}

function preprocessSearchQuery($search_query) {

    return str_replace("CPUs", "processors", $search_query);
}
?>
