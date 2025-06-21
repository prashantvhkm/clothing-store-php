<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$cart = $_SESSION['cart'] ?? [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($cart)) {
    $full_name     = trim($_POST['full_name']);
    $address       = trim($_POST['address']);
    $city          = trim($_POST['city']);
    $state         = trim($_POST['state']);
    $postal_code   = trim($_POST['postal_code']);
    $country       = trim($_POST['country']);
    $phone         = trim($_POST['phone']);
    $payment_method = trim($_POST['payment_method']);
    
    $total_price = 0;
    foreach ($cart as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    try {
        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, payment_method) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $total_price, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // Insert delivery details
        $stmtDelivery = $pdo->prepare("INSERT INTO delivery_details (order_id, full_name, address, city, state, postal_code, country, phone)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtDelivery->execute([
            $order_id, $full_name, $address, $city, $state, $postal_code, $country, $phone
        ]);

        // Insert order items
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, product_price) VALUES (?, ?, ?, ?)");
        foreach ($cart as $item) {
            $stmtItem->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        // Clear cart and redirect
        unset($_SESSION['cart']);
        $_SESSION['last_order_id'] = $order_id;
        header("Location: order_confirmation.php");
        exit();

    } catch (PDOException $e) {
        echo "Checkout Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Clothing Store</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Checkout</h2>

        <?php if (!empty($cart)): ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Delivery Information</h5>
                        <div class="mb-2">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="COD">Cash on Delivery</option>
                                <option value="UPI">UPI</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Cart Summary</h5>
                        <ul class="list-group mb-3">
                            <?php $total = 0; ?>
                            <?php foreach ($cart as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?>
                                    <span>₹<?= number_format($subtotal, 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Total:</strong>
                                <strong>₹<?= number_format($total, 2) ?></strong>
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">Your cart is empty.</div>
        <?php endif; ?>
    </div>
</body>
</html>
