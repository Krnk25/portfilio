<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<script>alert('Your cart is empty!'); window.location.href='cart.php';</script>";
    exit();
}
foreach ($cart as $item) {
    $product_name = isset($item['product_name']) ? $item['product_name'] : '';
    $price = isset($item['price']) ? $item['price'] : 0;
    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $product_name, $price, $quantity]);
}

unset($_SESSION['cart']);

echo "<script>alert('Order placed successfully!'); window.location.href='index.html';</script>";
?>