<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name,mobile,address,email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $_POST['mobile'], $_POST['address'], $email, $password]);

    echo "<script>alert('Registered successfully! Please login.'); window.location.href='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register - Virtual Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Register</h2>
        <form method="post">
            <input class="form-control mb-3" type="text" name="name" placeholder="Full Name" required>
            <input class="form-control mb-3" type="text" name="mobile" placeholder="Mobile No" required>
            <input class="form-control mb-3" type="text" name="address" placeholder="Address" required>
            <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-success" type="submit">Register</button>
            <a href="login.php" class="btn btn-link">Already have an account? Login</a>
        </form>
    </div>
</body>

</html>