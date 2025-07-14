<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    echo json_encode([
        'orders' => (int)$orders,
        'products' => (int)$products,
        'users' => (int)$users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
