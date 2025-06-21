<?php
session_start();
if (!isset($_SESSION['last_order_id'])) {
    header("Location: index.php");
    exit();
}
$order_id = $_SESSION['last_order_id'];
unset($_SESSION['last_order_id']); // Prevent refresh issues
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <h2 class="text-success">🎉 Order Placed Successfully!</h2>
        <p>Your order number is <strong>#<?= $order_id ?></strong>.</p>
        <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
    </div>
</body>
</html>
