<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: place_order.php');
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login - Virtual Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <?php if (isset($error))
            echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-primary" type="submit">Login</button>
            <a href="register.php" class="btn btn-link">Register</a>
        </form>
    </div>
</body>

</html>