<?php
session_start();
include('connect.php');

// Check if the cart is empty
if (empty($_SESSION['cart'])) {
    echo "Your cart is empty!";
    exit();
}

// Remove item from the cart if the remove button is clicked
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];

    // Loop through the cart to find the item with the matching ID and remove it
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    // Reindex the session array to prevent gaps in the array
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    // Redirect to prevent resubmission on refresh
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    header("Location: checkout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Clothing Store</h1>
        </div>
        <nav>
            <a href="index.php">Continue Shopping</a>
        </nav>
    </header>

    <h2>Your Cart</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $item):
                // Check if 'quantity' exists before accessing it
                $quantity = isset($item['quantity']) ? $item['quantity'] : 1; // Default to 1 if missing
                $totalPrice += $item['price'] * $quantity;
            ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>₹<?= number_format($item['price'], 2) ?></td>
                    <td><?= $quantity ?></td>
                    <td>₹<?= number_format($item['price'] * $quantity, 2) ?></td>
                    <td>
                        <a href="cart.php?remove_id=<?= $item['id'] ?>" class="remove-btn">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total: ₹<?= number_format($totalPrice, 2) ?></h3>

    <form method="POST">
        <button type="submit" name="checkout">Proceed to Checkout</button>
    </form>

    <footer>
        <p>&copy; 2025 Clothing Store</p>
    </footer>
</body>
</html>
