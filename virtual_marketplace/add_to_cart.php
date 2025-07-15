<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_name])) {
        $_SESSION['cart'][$product_name]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_name] = [
            'product_name' => $product_name,
            'price' => $price,
            'quantity' => 1
        ];
    }

    echo "<script>alert('Product added successfully!'); window.location.href='product.html';</script>";
}
?>