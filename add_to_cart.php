<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    try {
        // Fetch the product from the database
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            $item = [
                'id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1
            ];

            // Check if cart exists
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $exists = false;

            // Check if product already in cart
            foreach ($_SESSION['cart'] as &$cartItem) {
                if ($cartItem['id'] == $product_id) {
                    $cartItem['quantity'] += 1;
                    $exists = true;
                    break;
                }
            }

            // If product not already in cart, add it
            if (!$exists) {
                $_SESSION['cart'][] = $item;
            }

            // Set alert session variable
            $_SESSION['added_to_cart'] = true;

            // Redirect back to index.php
            header("Location: index.php");
            exit();
        } else {
            echo "❌ Product not found.";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
