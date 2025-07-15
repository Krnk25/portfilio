<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $quantity = (int) $_POST['quantity'];

    if (isset($_SESSION['cart'][$product_name])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$product_name]['quantity'] = $quantity;
        }
    }
}

header('Location: cart.php');
exit();
?>