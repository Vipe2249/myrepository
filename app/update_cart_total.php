<?php

session_start();

if (isset($_POST['newTotal'])) {
    // Update the $cartTotal session variable
    $_SESSION['cartTotal'] = $_POST['newTotal'];
    // Return the updated total for confirmation (optional)
    echo $_SESSION['cartTotal'];
}