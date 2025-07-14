<?php
session_start();
require_once 'connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Fetch the image filename from database
$stmt = $pdo->prepare("SELECT image FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Delete the product from the database
    $deleteStmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $deleted = $deleteStmt->execute([$product_id]);

    if ($deleted) {
        // Delete image file if exists
        $imagePath = "uploads/" . $product['image'];
        if (!empty($product['image']) && file_exists($imagePath)) {
            unlink($imagePath);
        }

        $_SESSION['message'] = "Product deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete product.";
    }
} else {
    $_SESSION['error'] = "Product not found.";
}

header("Location: manage_products.php");
exit();
?>
