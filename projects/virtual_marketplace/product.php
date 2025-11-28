<?php
// Start session to manage cart
session_start();

// 50 sample products
$products = [
    "Coffee Mug",
    "Tea Cup",
    "Water Bottle",
    "Notebook",
    "Laptop Stand",
    "Bluetooth Speaker",
    "Earphones",
    "Wireless Mouse",
    "Keyboard",
    "Desk Lamp",
    "Smart Watch",
    "Sunglasses",
    "Backpack",
    "Wallet",
    "Shoes",
    "T-shirt",
    "Jeans",
    "Jacket",
    "Perfume",
    "Hair Dryer",
    "Wrist Band",
    "Power Bank",
    "Phone Case",
    "Charger",
    "Tripod",
    "Camera Lens",
    "Microphone",
    "Pen Drive",
    "Hard Disk",
    "Monitor",
    "Table Clock",
    "Photo Frame",
    "Cushion",
    "Towel Set",
    "Wall Art",
    "Cooking Pan",
    "Pressure Cooker",
    "Mixer Grinder",
    "Rice Cooker",
    "Electric Kettle",
    "Toaster",
    "Juicer",
    "Sneakers",
    "Sports Bottle",
    "Yoga Mat",
    "Gym Gloves",
    "Fitness Band",
    "Wireless Charger",
    "LED Bulb",
    "Extension Board"
];

// Corresponding prices
$prices = [
    299,
    199,
    499,
    99,
    899,
    999,
    499,
    399,
    599,
    699,
    1099,
    799,
    1299,
    499,
    1799,
    699,
    999,
    1499,
    899,
    599,
    299,
    999,
    299,
    199,
    499,
    799,
    999,
    299,
    1699,
    799,
    299,
    399,
    299,
    499,
    799,
    999,
    1199,
    899,
    699,
    399,
    699,
    799,
    1999,
    499,
    399,
    899,
    999,
    599,
    199,
    299
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Virtual Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafb;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            color: #007bff !important;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 10px;
            transition: 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #007bff, #00c6ff);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #0056d1, #0099cc);
            transform: scale(1.05);
        }

        h2 {
            font-weight: 700;
            text-transform: uppercase;
            color: #333;
            letter-spacing: 1px;
        }

        footer {
            background: #222;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">Virtual Marketplace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="product.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_account.php">My Account</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Product Grid -->
    <div class="container my-5">
        <h2 class="text-center mb-5">Our Products</h2>
        <div class="row g-4">
            <?php for ($i = 0; $i < 50; $i++): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card shadow-sm">
                        <img src="images/product<?= ($i % 10) + 1 ?>.jpg" class="card-img-top"
                            alt="<?= htmlspecialchars($products[$i]) ?>">
                        <div class="card-body text-center">
                            <h6 class="card-title"><?= htmlspecialchars($products[$i]) ?></h6>
                            <p class="card-text mb-2">Price: ₹<?= htmlspecialchars($prices[$i]) ?></p>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($products[$i]) ?>">
                                <input type="hidden" name="price" value="<?= htmlspecialchars($prices[$i]) ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?= date("Y"); ?> Virtual Marketplace. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>