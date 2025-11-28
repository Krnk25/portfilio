<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;

    // Check if product already exists in the cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_name'] === $product_name) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // If not found, add new product
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_name' => $product_name,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    header("Location: cart.php");
    exit();
}
?>