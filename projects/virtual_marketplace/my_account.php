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
    <style>
    body {
        background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
    }

    .navbar {
        background: linear-gradient(90deg, #4b6cb7, #182848);
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #ffeb3b !important;
    }

    h2,
    h3 {
        font-weight: 700;
        color: #182848;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .btn {
        border-radius: 30px;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }

    .btn-danger {
        background: linear-gradient(90deg, #ff6a00, #ee0979);
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(90deg, #ee0979, #ff6a00);
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(255, 87, 34, 0.6);
    }

    .table {
        background-color: #ffffff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .table thead {
        background: #4b6cb7;
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: #e8f0fe;
        transition: background-color 0.3s ease;
    }

    footer {
        text-align: center;
        padding: 20px 0;
        color: #777;
        font-size: 14px;
        margin-top: 50px;
    }

    @media (max-width: 768px) {
        h2 {
            font-size: 1.5rem;
        }

        .card {
            padding: 20px;
        }

        .table {
            font-size: 14px;
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">My Account</h2>
        <div class="card p-4 mb-4">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Mobile No.:</strong> <?= htmlspecialchars($user['mobile']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <div class="text-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <h3 class="mt-5 mb-3 text-center">My Orders</h3>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();
        ?>

        <?php if ($orders): ?>
        <div class="table-responsive">
            <table class="table table-hover text-center">
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
        </div>
        <?php else: ?>
        <p class="text-center text-muted mt-3">No orders found.</p>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?= date('Y') ?> Virtual Marketplace. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>