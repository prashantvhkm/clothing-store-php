<?php

session_start();
require_once 'connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin'];

$stmt = $pdo->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at ASC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Orders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .card.dark-mode, .table-dark-mode {
      background-color: #2c2c2c;
      color: white;
    }
    .content-scrollable {
      height: calc(100vh - 70px);
      overflow-y: auto;
    }

     html, body {
    height: 100%;
    margin: 0;
  }

  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 220px; /* sidebar width */
    height: 100vh;
    background-color: #343a40;
    color: white;
    overflow-y: auto; /* scrollbar inside sidebar if needed */
    padding-top: 1rem;
  }

  .sidebar a {
    color: white;
    display: block;
    padding: 8px 16px;
    text-decoration: none;
  }

  .sidebar a:hover {
    background-color: #495057;
  }

  main {
    margin-left: 220px; /* same as sidebar width */
    height: 100vh;
    overflow-y: auto; /* scroll only main content */
    padding: 1rem 2rem;
  }

  body.dark-mode {
    background-color: #121212;
    color: #f1f1f1;
  }

  .table-dark th, .table-dark td {
    color: #f1f1f1;
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
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h2 class="h4">Welcome, <?php echo htmlspecialchars($admin['email']); ?></h2>
      </div>

      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3>All Orders</h3>
          <a href="admin_dashboard.php" class="btn btn-secondary btn-sm">&larr; Back to Dashboard</a>
        </div>

        <table class="table table-bordered table-hover" id="ordersTable">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Total Price</th>
              <th>Address</th>
              <th>Status</th>
              <th>Payment Method</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($orders) > 0): ?>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td><?= $order['order_id'] ?></td>
                  <td><?= htmlspecialchars($order['username'] ?? 'Guest') ?></td>
                  <td>₹<?= number_format($order['total_price'], 2) ?></td>
                  <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                  <td><?= htmlspecialchars($order['status']) ?></td>
                  <td><?= htmlspecialchars($order['payment_method']) ?></td>
                  <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('darkModeToggle');
  const body = document.body;
  const sidebar = document.getElementById('sidebar');

  if (localStorage.getItem('darkMode') === 'true') {
    enableDarkMode();
  }

  toggleBtn.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    sidebar.classList.toggle('dark-mode');
    document.querySelectorAll('table').forEach(t => t.classList.toggle('table-dark'));
    localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
  });
</script>

</body>
</html>
