<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<h2>Your Orders</h2>
<?php foreach ($orders as $order): ?>
  <div>
    <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
    <p><strong>Total:</strong> ₹<?= $order['total_price'] ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?></p>
    <p><strong>Placed on:</strong> <?= $order['created_at'] ?></p>
    <hr>
  </div>
<?php endforeach; ?>
