<?php
session_start();
include('connect.php');

// Fetch all products from the database
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll();

// Add product to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity for added product

    // Fetch the product from the database
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // If the product is already in the cart, increment the quantity
        $exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += $quantity; // Increment quantity
                $exists = true;
                break;
            }
        }

        // If the product is not in the cart, add it with quantity 1
        if (!$exists) {
            $_SESSION['cart'][] = [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity // Set quantity to 1 when the product is first added
            ];
        }

        // Redirect to prevent form resubmission on refresh
        header("Location: shop.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Clothing Store</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Header Styles */
        header {
            background-color: #232f3e;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        header .logo h1 {
            margin: 0;
            font-size: 2rem;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            margin: 0 15px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* Shop Banner */
        .shop-banner {
            background-color: #ff9900;
            color: white;
            text-align: center;
            padding: 40px 0;
        }

        .shop-banner h2 {
            font-size: 2.5rem;
            margin: 0;
        }

        .shop-banner p {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        /* Filter Section */
        .shop-filter {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .filter-form {
            display: flex;
            gap: 10px;
        }

        .filter-select {
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .filter-btn {
            padding: 10px 20px;
            background-color: #232f3e;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .filter-btn:hover {
            background-color: #ff9900;
        }

        /* Product Card Grid */
        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 400px;
        }

        /* Proper Image Size */
        .product-image {
            width: 100%;
            height: 200px; /* Set a fixed height for consistency */
            object-fit: cover; /* Ensures the image doesn't stretch */
            border-radius: 10px;
            margin-bottom: 1px; /* Space between image and text */
        }

        .product-title {
            font-size: 1.3rem;
            margin: 10px 0;
        }

        .product-description {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 10px;
        }

        .add-to-cart-btn {
            padding: 10px 20px;
            background-color: #ff9900;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: auto;
        }

        .add-to-cart-btn:hover {
            background-color: #232f3e;
        }

        /* Footer Styles */
        footer {
            background-color: #232f3e;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
<header>
        <div class="logo">
            <h1>Clothing Store</h1>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="cart.php">View Cart</a>
            <?php if(isset($_SESSION['user'])): ?>
                <span style="color: white; margin: 0 15px;">Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="shop-banner">
        <h2>Shop Our Collection</h2>
        <p>Browse through our latest fashion trends and exclusive deals!</p>
    </section>

    <section class="shop-filter">
        <form method="GET" class="filter-form">
            <select name="category" class="filter-select">
                <option value="">All Categories</option>
                <option value="T-Shirts">T-Shirts</option>
                <option value="Shirts">Shirts</option>
                <option value="Pants">Pants</option>
                <option value="Jackets">Jackets</option>
            </select>
            <select name="price" class="filter-select">
                <option value="">Price Range</option>
                <option value="low_to_high">Low to High</option>
                <option value="high_to_low">High to Low</option>
            </select>
            <button type="submit" class="filter-btn">Filter</button>
        </form>
    </section>

    <section class="products-container">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-image">
                <h3 class="product-title"><?= $product['name'] ?></h3>
                <p class="product-description"><?= substr($product['description'], 0, 100) ?>...</p>
                <p class="product-price">₹<?= number_format($product['price'], 2) ?></p>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </section>

    <footer>
        <p>&copy; 2025 Clothing Store</p>
    </footer>
</body>
</html>
