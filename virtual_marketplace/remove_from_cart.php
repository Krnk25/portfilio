<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];

    if (isset($_SESSION['cart'][$product_name])) {
        unset($_SESSION['cart'][$product_name]);
    }
}

header('Location: cart.php');
exit();
?>