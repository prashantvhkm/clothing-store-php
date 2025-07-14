<?php

session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin'];

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Manage Products</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      body.dark-mode {
        background-color: #121212;
        color: #f1f1f1;
      }
      .sidebar {
        height: 100vh;
        background-color: #343a40;
        color: white;
      }
      .sidebar a {
        color: white;
        text-decoration: none;
        padding: 8px 12px;
        display: block;
      }
      .sidebar a:hover {
        background-color: #495057;
      }
      .sidebar.dark-mode {
        background-color: #1f1f1f;
      }
      .card.dark-mode,
      .table-dark-mode {
        background-color: #2c2c2c;
        color: white;
      }
      .content-scrollable {
        height: calc(100vh - 70px);
        overflow-y: auto;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row">
  <!-- Sidebar -->
    <nav class="col-md-2 d-none d-md-block sidebar py-4" id="sidebar">
  <div class="position-sticky">
    <h5 class="text-center mb-4">Admin Panel</h5>
    <ul class="nav flex-column px-3">
      <li class="nav-item">
        <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="view_orders.php">View Orders</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="manage_products.php">Manage Products</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="manage_user.php">Manage Users</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link text-danger fw-bold"
          href="logout.php"
          onclick="return confirm('Are you sure you want to logout?');"
        >
          Logout
        </a>
      </li>
    </ul>
  </div>
</nav>


        <main class="col-md-10 ms-sm-auto px-md-4 py-4 content-scrollable">
          <div
            class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom"
          >
            <h2 class="h4">
              Welcome,
              <?php echo htmlspecialchars($admin['email']); ?>
            </h2>
            <button
              class="btn btn-outline-secondary btn-sm"
              id="darkModeToggle"
            >
              Toggle Dark Mode
            </button>
          </div>

          <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h3>All Products</h3>
              <a href="add_product.php" class="btn btn-primary">Add Product</a>
            </div>

            <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY product_id ASC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC); if (count($products)
            > 0): ?>
            <table
              class="table table-bordered table-striped <?php echo isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true' ? 'table-dark' : ''; ?>"
            >
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Image</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                  <td><?php echo $product['product_id']; ?></td>
                  <td><?php echo htmlspecialchars($product['name']); ?></td>
                  <td>
                    <?php echo htmlspecialchars($product['description']); ?>
                  </td>
                  <td>₹<?php echo number_format($product['price'], 2); ?></td>
                  <td><?php echo $product['stock']; ?></td>
                  <td>
                    <?php if (!empty($product['image'])): ?>
                    <img
                      src="images/<?php echo $product['image']; ?>"
                      alt="Product Image"
                      width="60"
                    />
                    <?php else: ?>
                    No image
                    <?php endif; ?>
                  </td>
                  <td>
  <div class="d-flex gap-2">
    <a
      href="edit_product.php?id=<?php echo $product['product_id']; ?>"
      class="btn btn-sm btn-warning"
      style="min-width: 70px;"
    >
      ✏️ Edit
    </a>

    <a
      href="delete_product.php?id=<?php echo $product['product_id']; ?>"
      onclick="return confirm('Are you sure you want to delete this product?');"
      class="btn btn-sm btn-danger"
      style="min-width: 70px;"
    >
      🗑️ Delete
    </a>
  </div>
</td>

                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php else: ?>
            <p>No products found.</p>
            <?php endif; ?>
          </div>
        </main>
      </div>
    </div>

    <script>
      const toggleBtn = document.getElementById("darkModeToggle");
      const body = document.body;
      const sidebar = document.getElementById("sidebar");

      if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
        sidebar.classList.add("dark-mode");
        document
          .querySelectorAll("table")
          .forEach((t) => t.classList.add("table-dark"));
      }

      toggleBtn.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        sidebar.classList.toggle("dark-mode");
        document
          .querySelectorAll("table")
          .forEach((t) => t.classList.toggle("table-dark"));
        localStorage.setItem("darkMode", body.classList.contains("dark-mode"));
      });
    </script>
  </body>
</html>
