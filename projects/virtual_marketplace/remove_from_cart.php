<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    $productName = $_POST['product_name'];

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_name'] === $productName) {
                unset($_SESSION['cart'][$index]);
                break;
            }
        }
        // Re-index array to maintain order
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    header("Location: cart.php");
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>