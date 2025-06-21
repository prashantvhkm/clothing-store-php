<?php
session_start();
require_once 'connect.php';

// Welcome alert (once per session)
$welcomeAlert = "";
if (isset($_SESSION['user']) && !isset($_SESSION['welcome_shown'])) {
    $welcomeAlert = "Welcome, " . htmlspecialchars($_SESSION['user']['username']) . "!";
    $_SESSION['welcome_shown'] = true;
}

// Fetch products
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clothing Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f7f8fc;
            font-family: 'Segoe UI', sans-serif;
        }

        header {
            background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 28px;
            margin: 0;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        .welcome-message {
            background-color: #e0ffe0;
            padding: 12px;
            font-weight: bold;
            text-align: center;
            color: #2c662d;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            padding: 40px;
        }

        .product-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeInUp 0.6s ease-in-out;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-body {
            padding: 20px;
        }

        .product-name {
            font-size: 20px;
            font-weight: 600;
        }

        .product-price {
            color: #28a745;
            font-size: 18px;
            font-weight: 500;
            margin-top: 8px;
        }

        .btn-shop {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 12px;
            transition: background 0.3s ease-in-out;
        }

        .btn-shop:hover {
            background: linear-gradient(135deg, #0072ff, #0052cc);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <?php if (!empty($welcomeAlert)): ?>
    <script>
        window.onload = () => alert("<?= $welcomeAlert ?>");
    </script>
    <?php endif; ?>
</head>
<body>
<?php if (isset($_SESSION['added_to_cart']) && $_SESSION['added_to_cart'] === true): ?>
    <script>
        alert("✅ Product added to cart successfully!");
    </script>
    <?php unset($_SESSION['added_to_cart']); ?>
<?php endif; ?>

<header>
    <h1>Clothing Store</h1>
    <nav>
        <ul>
            <li><a href="cart.php">Cart <i class="fas fa-shopping-cart"></i></a></li>
            <?php if(isset($_SESSION['user'])): ?>
                <li style="color: white;">Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if(isset($_SESSION['user'])): ?>
    <div class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?> 🎉</div>
<?php endif; ?>

<div class="container mt-4">
    <h2 class="text-center mb-5">Shop Our Latest Collection</h2>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-image">
                <div class="product-body">
                    <h5 class="product-name"><?= $product['name'] ?></h5>
                    <p class="product-price">₹<?= number_format($product['price'], 2) ?></p>
                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <button type="submit" class="btn-shop" name="add_to_cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
