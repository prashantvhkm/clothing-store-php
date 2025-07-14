<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin'];

// Fetch product details
$product = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id  = $_POST['product_id'];
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];

    // Retain current image
    $imageName = $product['image'];

    // Handle new image upload
    if (!empty($_FILES["image"]["name"])) {
        $imageTmp = $_FILES["image"]["tmp_name"];
        $imageNameNew = basename(htmlspecialchars($_FILES["image"]["name"]));
        $imageType = $_FILES["image"]["type"];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($imageType, $allowedTypes)) {
            $_SESSION['error'] = "Invalid image type. Only JPG, PNG, WEBP allowed.";
            header("Location: edit_product.php?id=" . $product_id);
            exit();
        }

        if (move_uploaded_file($imageTmp, "uploads/" . $imageNameNew)) {
            $imageName = $imageNameNew;
        } else {
            $_SESSION['error'] = "Failed to upload image.";
            header("Location: edit_product.php?id=" . $product_id);
            exit();
        }
    }

    // Update product in database
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $updated = $stmt->execute([$name, $description, $price, $stock, $imageName, $product_id]);

    if ($updated) {
        $_SESSION['message'] = "Product updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update product.";
    }

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Product</title>
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
    <h3 class="mb-4">Edit Product</h3>

    <?php if ($product): ?>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $product['price']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" class="form-control" name="stock" value="<?php echo $product['stock']; ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <?php if (!empty($product['image'])): ?>
          <img src="uploads/<?php echo $product['image']; ?>" width="100" alt="Product Image"><br>
        <?php else: ?>
          <span>No image uploaded</span><br>
        <?php endif; ?>
        <label class="form-label mt-2">Change Image</label>
        <input type="file" class="form-control" name="image">
      </div>

      <button type="submit" class="btn btn-success">Update Product</button>
      <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?php else: ?>
      <p class="text-danger">Product not found.</p>
    <?php endif; ?>
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
