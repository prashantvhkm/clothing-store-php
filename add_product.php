<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];

    // Basic validation
    if (empty($name) || empty($description) || empty($price) || empty($stock)) {
        echo "<script>alert('Please fill all required fields.');</script>";
    } else {
        $imageName = null;

        // Handle image upload if available
        if (!empty($_FILES["image"]["name"])) {
            $imageName = basename(htmlspecialchars($_FILES["image"]["name"]));
            $imageTmp = $_FILES["image"]["tmp_name"];
            $uploadPath = "images/" . $imageName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $imageType = $_FILES["image"]["type"];

            if (!in_array($imageType, $allowedTypes)) {
                echo "<script>alert('Invalid image type. Only JPG, PNG, or WEBP allowed.');</script>";
                return;
            }

            if (!move_uploaded_file($imageTmp, $uploadPath)) {
                echo "<script>alert('Failed to upload image.');</script>";
                return;
            }
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
        $inserted = $stmt->execute([$name, $description, $price, $stock, $imageName]);

        if ($inserted) {
            $_SESSION['message'] = 'Product added successfully.';
        } else {
            $_SESSION['error'] = 'Failed to add product.';
        }

        header("Location: manage_products.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }
    .form-container {
      max-width: 600px;
      margin: auto;
      margin-top: 50px;
      padding: 20px;
      border-radius: 10px;
      background-color: #f8f9fa;
    }
    .form-container.dark-mode {
      background-color: #2c2c2c;
      color: white;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="form-container" id="formContainer">
    <h3 class="mb-4">Add New Product</h3>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="4" required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" name="price" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control" name="stock" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" class="form-control" name="image">
      </div>

      <button type="submit" class="btn btn-primary">Add Product</button>
      <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>

<script>
  const body = document.body;
  const formContainer = document.getElementById('formContainer');
  if (localStorage.getItem('darkMode') === 'true') {
    body.classList.add('dark-mode');
    formContainer.classList.add('dark-mode');
  }
</script>
</body>
</html>
