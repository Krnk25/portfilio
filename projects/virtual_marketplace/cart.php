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
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(90deg, #4b6cb7, #182848);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .btn {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table tr {
                display: block;
                margin-bottom: 1rem;
            }

            .table td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border: none;
                border-bottom: 1px solid #dee2e6;
            }

            .table td::before {
                content: attr(data-label);
                font-weight: 600;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg shadow">
        <div class="container">
            <a class="navbar-brand" href="#">Virtual Marketplace</a>
            <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_account.php">My Account</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Your Cart</h2>
        <?php if (count($cart) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
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
                            <?php
                            $price = (float) $item['price'];
                            $quantity = (int) $item['quantity'];
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td data-label="Product"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td data-label="Price">₹<?= number_format($price, 2) ?></td>
                                <td data-label="Quantity">
                                    <form class="d-flex" action="update_cart.php" method="post">
                                        <input type="hidden" name="product_name"
                                            value="<?= htmlspecialchars($item['product_name']) ?>">
                                        <input class="form-control me-2" style="width:80px;" type="number" name="quantity"
                                            value="<?= $quantity ?>" min="1" required>
                                        <button class="btn btn-primary btn-sm" type="submit">Update</button>
                                    </form>
                                </td>
                                <td data-label="Subtotal">₹<?= number_format($subtotal, 2) ?></td>
                                <td data-label="Action">
                                    <form action="remove_from_cart.php" method="post">
                                        <input type="hidden" name="product_name"
                                            value="<?= htmlspecialchars($item['product_name']) ?>">
                                        <button class="btn btn-danger btn-sm" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h4>Total: ₹<?= number_format($total, 2) ?></h4>
            <a href="place_order.php" class="btn btn-success">Place Order</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>