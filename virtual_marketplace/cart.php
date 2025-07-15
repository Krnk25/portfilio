<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Cart - Virtual Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="container">
            <a class="navbar-brand" href="#">Virtual Marketplace</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="product.html">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_account.php">My Account</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Your Cart</h2>
        <?php if (count($cart) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                        <th>Subtotal (₹)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>₹<?= htmlspecialchars($item['price']) ?></td>
                            <td>
                                <form class="d-flex" action="update_cart.php" method="post">
                                    <input type="hidden" name="product_name"
                                        value="<?= htmlspecialchars($item['product_name']) ?>">
                                    <input class="form-control me-2" style="width:80px;" type="number" name="quantity"
                                        value="<?= $item['quantity'] ?>" min="1" required>
                                    <button class="btn btn-primary btn-sm" type="submit">Update</button>
                                </form>
                            </td>
                            <td>₹<?= $item['price'] * $item['quantity']; ?></td>
                            <td>
                                <form action="remove_from_cart.php" method="post">
                                    <input type="hidden" name="product_name"
                                        value="<?= htmlspecialchars($item['product_name']) ?>">
                                    <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4>Total: ₹<?= $total ?></h4>
            <a href="place_order.php" class="btn btn-success">Place Order</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>