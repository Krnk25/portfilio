<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'], $_POST['quantity'])) {
    $productName = $_POST['product_name'];
    $newQty = (int) $_POST['quantity'];

    // Ensure cart exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Update product quantity if found
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_name'] === $productName) {
            $item['quantity'] = $newQty;
            break;
        }
    }
    unset($item); // break reference

    // Redirect back to cart
    header("Location: cart.php");
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>