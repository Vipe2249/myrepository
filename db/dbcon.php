<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'infinityware');

// Attempt to establish a connection to the database
$con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check the connection
if (!$con) {
    // Handle connection error gracefully
    die("Connection failed: " . mysqli_connect_error());
}
?>
