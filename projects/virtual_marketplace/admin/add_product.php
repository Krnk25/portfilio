<?php
include '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../images/$image");

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $image]);
    echo "Product added!";
}
?>
<form method="post" enctype="multipart/form-data">
    Name: <input name="name"><br>
    Description: <textarea name="description"></textarea><br>
    Price: <input name="price" type="number"><br>
    Image: <input type="file" name="image"><br>
    <button type="submit">Add Product</button>
</form>