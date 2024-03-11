<?php

session_start();

if (isset($_POST['newTotal'])) {

    $_SESSION['cartTotal'] = $_POST['newTotal'];

    echo $_SESSION['cartTotal'];
}
