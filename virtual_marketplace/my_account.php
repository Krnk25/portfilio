<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // User not found, force logout
    session_destroy();
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Account - Virtual Marketplace</title>
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


                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>My Account</h2>
        <div class="card p-4 mb-4">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Mobile No.:</strong> <?= htmlspecialchars($user['mobile']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <h3>My Orders</h3>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();
        ?>

        <?php if ($orders): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td>₹<?= htmlspecialchars($order['price']) ?></td>
                            <td><?= htmlspecialchars($order['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>