<?php
session_start();
include('connect.php');

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Your cart is empty!'); window.location.href = 'index.php';</script>";
    exit();
}

// Remove item
if (isset($_GET['remove_id'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $_GET['remove_id']) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex
    header("Location: cart.php");
    exit();
}

// Proceed to checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    header("Location: checkout.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', sans-serif;
            transition: background 0.3s ease, color 0.3s ease;
        }

        header {
            background: linear-gradient(90deg, #0f2027, #203a43, #2c5364);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
            margin: 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        main {
            flex: 1;
        }

        .remove-btn {
            color: #dc3545;
            text-decoration: none;
        }

        .remove-btn:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 12px;
            border-top: 1px solid #ddd;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        /* Table Styling */
        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        /* DARK MODE */
        body.dark-mode {
            background-color: #121212;
            color: #e0e0e0;
        }

        body.dark-mode header {
            background: linear-gradient(90deg, #1a1a1a, #2c2c2c);
        }

        body.dark-mode nav a {
            color: #fff;
        }

        body.dark-mode footer {
            background-color: #1e1e1e;
            color: #ccc;
        }

        /* Table dark styling */
        body.dark-mode .table-bordered {
            background-color: #1e1e1e;
            color: #f1f1f1;
            border-color: #444;
        }

        body.dark-mode .table-bordered td,
        body.dark-mode .table-bordered th {
            background-color: #1e1e1e !important;
            color: #f1f1f1 !important;
            border-color: #444;
        }

        body.dark-mode .table-bordered thead {
            background-color: #2c2c2c !important;
            color: #fff !important;
        }

        body.dark-mode .remove-btn {
            color: #ff6666;
        }

        body.dark-mode .table-responsive {
            border-color: #444;
        }
    </style>
</head>
<body>
<header>
    <h1>Clothing Store</h1>
    <nav>
        <a href="index.php">← Continue Shopping</a>
        <button id="darkToggle" class="btn btn-sm btn-outline-light ms-3">🌙</button>
    </nav>
</header>

<main class="container my-5">
    <h2 class="mb-4 text-center">🛒 Your Shopping Cart</h2>

    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Total (₹)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPrice = 0;
                foreach ($_SESSION['cart'] as $item):
                    $quantity = $item['quantity'] ?? 1;
                    $total = $item['price'] * $quantity;
                    $totalPrice += $total;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($total, 2) ?></td>
                    <td>
                        <a href="cart.php?remove_id=<?= $item['id'] ?>" class="remove-btn">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4 class="text-end">Total: ₹<?= number_format($totalPrice, 2) ?></h4>

    <form method="POST" class="text-end mt-3">
        <button type="submit" name="checkout" class="btn btn-success px-4">Proceed to Checkout</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Clothing Store. All rights reserved.</p>
</footer>

<script>
    const toggle = document.getElementById('darkToggle');
    toggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    });

    // Load theme on page load
    window.onload = () => {
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    };
</script>

</body>
</html>
