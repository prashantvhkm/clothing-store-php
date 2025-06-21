<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders with delivery and payment method
$sql = "SELECT o.*, d.full_name, d.address, d.city, d.state, d.postal_code, d.country, d.phone 
        FROM orders o 
        JOIN delivery_details d ON o.id = d.order_id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);

// Define status badge classes
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'shipped': return 'primary';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Clothing Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Clothing Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link active" href="orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">My Orders</h1>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(mysqli_num_rows($orders_result) == 0): ?>
            <div class="alert alert-info">You haven't placed any orders yet.</div>
        <?php else: ?>
            <?php while($order = mysqli_fetch_assoc($orders_result)): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order #<?= $order['id']; ?></h5>
                            <span class="badge bg-<?= getStatusBadge($order['status']); ?>">
                                <?= ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Order Info -->
                            <div class="col-md-6">
                                <h6>Order Summary</h6>
                                <p class="mb-1">Date: <?= date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                                <p class="mb-1">Total Amount: ₹<?= number_format($order['total_amount'], 2); ?></p>
                                <p class="mb-1">Payment Method: <strong><?= htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></strong></p>
                            </div>
                            <!-- Delivery Info -->
                            <div class="col-md-6">
                                <h6>Delivery Address</h6>
                                <p class="mb-1"><?= htmlspecialchars($order['full_name']); ?></p>
                                <p class="mb-1"><?= htmlspecialchars($order['address']); ?></p>
                                <p class="mb-1"><?= htmlspecialchars($order['city']); ?>, <?= htmlspecialchars($order['state']); ?> <?= htmlspecialchars($order['postal_code']); ?></p>
                                <p class="mb-1"><?= htmlspecialchars($order['country']); ?></p>
                                <p class="mb-1">Phone: <?= htmlspecialchars($order['phone']); ?></p>
                            </div>
                        </div>

                        <h6 class="mt-4">Ordered Items</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $items_sql = "SELECT oi.*, p.name 
                                                FROM order_items oi 
                                                JOIN products p ON oi.product_id = p.id 
                                                WHERE oi.order_id = ?";
                                    $items_stmt = mysqli_prepare($conn, $items_sql);
                                    mysqli_stmt_bind_param($items_stmt, "i", $order['id']);
                                    mysqli_stmt_execute($items_stmt);
                                    $items_result = mysqli_stmt_get_result($items_stmt);

                                    while($item = mysqli_fetch_assoc($items_result)):
                                        $subtotal = $item['price'] * $item['quantity'];
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']); ?></td>
                                            <td><?= $item['quantity']; ?></td>
                                            <td>₹<?= number_format($item['price'], 2); ?></td>
                                            <td>₹<?= number_format($subtotal, 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
