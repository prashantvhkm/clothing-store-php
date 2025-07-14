<?php
session_start();
require_once 'connect.php';

// Redirect if not admin
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin info
$admin = $_SESSION['admin'];

// Get search term
$searchTerm = $_GET['search'] ?? '';
$searchLike = "%{$searchTerm}%";

// Fetch users
if (!empty($searchTerm)) {
    $userStmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $userStmt->execute([$searchLike, $searchLike]);
} else {
    $userStmt = $pdo->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC");
}
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch orders
if (!empty($searchTerm)) {
    $orderStmt = $pdo->prepare("
        SELECT o.order_id, o.total_price, o.status, o.payment_method, o.shipping_address, o.created_at,
               u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE u.username LIKE ? OR u.email LIKE ?
        ORDER BY o.created_at DESC
    ");
    $orderStmt->execute([$searchLike, $searchLike]);
} else {
    $orderStmt = $pdo->query("
        SELECT o.order_id, o.total_price, o.status, o.payment_method, o.shipping_address, o.created_at,
               u.username, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
    ");
}
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users & Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }

    .table-dark th, .table-dark td {
      color: #f1f1f1;
    }

    /* Sidebar styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 220px;
      height: 100vh;
      background-color: #343a40;
      color: white;
      overflow-y: auto;
      padding-top: 1rem;
      z-index: 1000;
    }

    .sidebar a {
      color: white;
      display: block;
      padding: 8px 16px;
      text-decoration: none;
      font-weight: 500;
    }

    .sidebar a:hover {
      background-color: #495057;
      color: #fff;
    }

    .sidebar a.active {
      background-color: #0d6efd;
      font-weight: 700;
    }

    /* Main content styles */
    main {
      margin-left: 220px;
      height: 100vh;
      overflow-y: auto;
      padding: 20px 30px;
    }

    /* Responsive adjustment */
    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
      }
      main {
        margin-left: 0;
        height: auto;
        overflow-y: visible;
        padding: 15px;
      }
    }
  </style>
</head>
<body>
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

  <main>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4>Welcome, <?php echo htmlspecialchars($admin['email']); ?></h4>
      <button id="darkModeToggle" class="btn btn-outline-secondary btn-sm" aria-label="Toggle dark mode">Toggle Dark Mode</button>
    </div>

    <!-- Search Form -->
    <form method="GET" class="mb-4 d-flex" role="search" aria-label="Search users and orders">
      <input
        type="search"
        name="search"
        value="<?php echo htmlspecialchars($searchTerm); ?>"
        class="form-control me-2"
        placeholder="Search by username or email"
        aria-label="Search by username or email"
      />
      <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Users Table -->
    <section aria-labelledby="usersHeading">
      <h5 id="usersHeading" class="mb-3">Registered Users</h5>
      <?php if (count($users) > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="usersTable">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Registered On</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td><?php echo $user['id']; ?></td>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo $user['created_at']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>No users found<?php echo $searchTerm ? " for '<strong>" . htmlspecialchars($searchTerm) . "</strong>'" : ''; ?>.</p>
      <?php endif; ?>
    </section>

    <!-- Orders Table -->
    <section aria-labelledby="ordersHeading" class="mt-5">
      <h5 id="ordersHeading" class="mb-3">User Orders</h5>
      <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="ordersTable">
            <thead>
              <tr>
                <th scope="col">Order ID</th>
                <th scope="col">User</th>
                <th scope="col">Email</th>
                <th scope="col">Total Price</th>
                <th scope="col">Status</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Shipping Address</th>
                <th scope="col">Ordered On</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td><?php echo $order['order_id']; ?></td>
                  <td><?php echo htmlspecialchars($order['username']); ?></td>
                  <td><?php echo htmlspecialchars($order['email']); ?></td>
                  <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                  <td><?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?></td>
                  <td><?php echo htmlspecialchars($order['payment_method'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                  <td><?php echo $order['created_at']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>No orders found<?php echo $searchTerm ? " for '<strong>" . htmlspecialchars($searchTerm) . "</strong>'" : ''; ?>.</p>
      <?php endif; ?>
    </section>
  </main>

  <script>
    const darkBtn = document.getElementById('darkModeToggle');
    const body = document.body;

    if (localStorage.getItem('darkMode') === 'true') {
      body.classList.add('dark-mode');
      document.querySelectorAll('table').forEach(t => t.classList.add('table-dark'));
    }

    darkBtn.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      document.querySelectorAll('table').forEach(t => t.classList.toggle('table-dark'));
      localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
    });
  </script>
</body>
</html>
