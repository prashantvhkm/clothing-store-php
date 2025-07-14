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
            transition: all 0.3s ease-in-out;
        }
        header {
            background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
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
        .carousel-inner { border-radius: 12px; overflow: hidden; }
        .carousel-indicators [data-bs-target] { background-color: #007bff; }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 30px;
            padding: 20px;
        }
        .product-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .product-body {
            padding: 16px;
            text-align: center;
        }
        .product-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .product-price {
            font-size: 17px;
            color: #28a745;
            margin-bottom: 12px;
        }
        .btn-shop {
            background: linear-gradient(to right, #007bff, #00c6ff);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            transition: background 0.3s ease-in-out;
        }
        .btn-shop:hover {
            background: linear-gradient(to right, #0056b3, #0099cc);
        }
        footer {
            background-color: #f1f1f1;
            color: #333;
            font-size: 15px;
            border-top: 1px solid #ddd;
        }
        footer a.footer-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
        }
        footer a.footer-link:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0a58ca;
        }
        /* Dark mode styles */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }
        body.dark-mode header {
            background: linear-gradient(90deg, #1a1a1a, #2c2c2c);
        }
        body.dark-mode .product-card {
            background-color: #1e1e1e;
            color: #f5f5f5;
        }
        body.dark-mode .btn-shop {
            background: linear-gradient(to right, #444, #777);
        }
        body.dark-mode .btn-shop:hover {
            background: linear-gradient(to right, #666, #999);
        }
        body.dark-mode footer {
            background-color: #1e1e1e;
            color: #ccc;
        }
        body.dark-mode .footer-link {
            color: #90caf9;
        }
        body.dark-mode .footer-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: #bbdefb;
        }
        .welcome-message {
            background-color: #e0ffe0;
            padding: 12px;
            font-weight: bold;
            text-align: center;
            color: #2c662d;
            animation: fadeIn 1s ease-out;
        }

        body.dark-mode .welcome-message {
            background-color: #2a2e2e; /* Dark background */
            color: #a8ff78;            /* Light green text */
        }

    </style>
    <?php if (!empty($welcomeAlert)): ?>
    <script>window.onload = () => alert("<?= $welcomeAlert ?>");</script>
    <?php endif; ?>
</head>
<body>
<?php if (isset($_SESSION['added_to_cart']) && $_SESSION['added_to_cart'] === true): ?>
    <script>alert("✅ Product added to cart successfully!");</script>
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
                <li><a href="admin_dashboard.php">Admin</a></li>
               
            <?php endif; ?>
             
            <li><button id="darkModeToggle" class="btn btn-outline-light btn-sm">🌙</button></li>
        </ul>
    </nav>
</header>

<?php if(isset($_SESSION['user'])): ?>
    <div class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?> 🎉</div>
<?php endif; ?>

<div class="container">
    <div id="heroCarousel" class="carousel slide mt-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner rounded shadow">
            <div class="carousel-item active">
                <img src="images/banner1.jpg" class="d-block w-100" alt="Fashion 1" style="max-height: 400px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="images/banner2.jpg" class="d-block w-100" alt="Fashion 2" style="max-height: 400px; object-fit: cover;">
            </div>
            <div class="carousel-item">
                <img src="images/banner3.jpg" class="d-block w-100" alt="Fashion 3" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<div class="container mt-4">
    <h2 class="text-center mb-5">Shop Our Latest Collection</h2>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
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

<footer class="footer mt-5 py-4 text-center">
    <div class="container">
        <p class="mb-1">© <?= date('Y') ?> Clothing Store. All rights reserved.</p>
        <p class="mb-0">
            <a href="#" class="footer-link">About</a> |
            <a href="#" class="footer-link">Contact</a> |
            <a href="#" class="footer-link">Privacy Policy</a>
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById("darkModeToggle");
    toggleBtn.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        const isDark = document.body.classList.contains("dark-mode");
        localStorage.setItem("theme", isDark ? "dark" : "light");
    });
    window.onload = () => {
        const savedTheme = localStorage.getItem("theme");
        if (savedTheme === "dark") {
            document.body.classList.add("dark-mode");
        }
    };
</script>
</body>
</html>
