<?php
session_start();
require_once 'connect.php'; // Your PDO connection

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin'];

function getCounts($pdo) {
    $counts = [];
    $counts['orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $counts['products'] = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $counts['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    return $counts;
}

$counts = getCounts($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      transition: background-color 0.3s, color 0.3s;
    }
    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      color: white;
      transition: background-color 0.3s;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      padding: 8px 12px;
      display: block;
      transition: background-color 0.2s;
    }
    .sidebar a:hover,
    .sidebar a:focus {
      background-color: #495057;
      outline: none;
    }
    .sidebar.dark-mode {
      background-color: #1f1f1f;
    }
    .card {
      transition: background-color 0.3s, color 0.3s;
      cursor: default;
    }
    .card.dark-mode {
      background-color: #2c2c2c;
      color: white;
    }
    .card .bi {
      vertical-align: -0.125em;
      margin-right: 6px;
    }
    .btn-refresh {
      min-width: 120px;
    }
    /* Sidebar toggle button for small screens */
    #sidebarToggle {
      display: none;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1050;
      background: #343a40;
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 4px;
    }
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: -250px;
        top: 0;
        width: 250px;
        transition: left 0.3s ease;
        z-index: 1040;
      }
      .sidebar.show {
        left: 0;
      }
      #sidebarToggle {
        display: block;
      }
      main {
        padding-left: 0 !important;
      }
    }
  </style>
</head>
<body>

<button id="sidebarToggle" aria-label="Toggle sidebar menu" title="Toggle sidebar menu">
  <i class="bi bi-list" style="font-size: 1.25rem;"></i>
</button>

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

    <!-- Main Content -->
    <main class="col-md-10 ms-sm-auto px-md-4 py-4" role="main" tabindex="-1">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h2 class="h4" id="welcomeMessage">Welcome, <?php echo htmlspecialchars($admin['email']); ?></h2>
        <button class="btn btn-outline-secondary btn-sm" id="darkModeToggle" aria-pressed="false" aria-label="Toggle dark mode">
          <i class="bi bi-moon-fill"></i> Dark Mode
        </button>
      </div>

      <div class="d-flex align-items-center mb-3">
        <input type="text" id="searchInput" class="form-control me-2" placeholder="Filter cards (search)..." aria-label="Filter dashboard cards" />
        <button class="btn btn-primary btn-refresh" id="refreshCounts" aria-label="Refresh counts">
          <i class="bi bi-arrow-clockwise"></i> Refresh Counts
        </button>
      </div>

      <div class="row" id="cardsContainer">
        <!-- Total Orders Card -->
        <div class="col-md-4 mb-4 card-wrapper" data-type="orders" data-content="orders">
          <div class="card shadow-sm" id="card1" tabindex="0" role="region" aria-labelledby="ordersLabel" aria-describedby="ordersDesc" title="Total number of orders placed">
            <div class="card-body">
              <h5 class="card-title" id="ordersLabel"><i class="bi bi-basket3"></i> Total Orders</h5>
              <p class="card-text" id="ordersDesc"><?php echo $counts['orders']; ?> orders placed.</p>
            </div>
          </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-md-4 mb-4 card-wrapper" data-type="products" data-content="products">
          <div class="card shadow-sm" id="card2" tabindex="0" role="region" aria-labelledby="productsLabel" aria-describedby="productsDesc" title="Total number of products available">
            <div class="card-body">
              <h5 class="card-title" id="productsLabel"><i class="bi bi-box-seam"></i> Total Products</h5>
              <p class="card-text" id="productsDesc"><?php echo $counts['products']; ?> products available.</p>
            </div>
          </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-md-4 mb-4 card-wrapper" data-type="users" data-content="users">
          <div class="card shadow-sm" id="card3" tabindex="0" role="region" aria-labelledby="usersLabel" aria-describedby="usersDesc" title="Total number of registered users">
            <div class="card-body">
              <h5 class="card-title" id="usersLabel"><i class="bi bi-people"></i> Total Users</h5>
              <p class="card-text" id="usersDesc"><?php echo $counts['users']; ?> registered users.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Sidebar toggle for small screens
  const sidebarToggleBtn = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');

  sidebarToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
  });

  // Dark mode toggle
  const toggleBtn = document.getElementById('darkModeToggle');
  const body = document.body;
  const cards = document.querySelectorAll('.card');
  const sidebarElement = document.getElementById('sidebar');

  // Apply saved preference on load
  if (localStorage.getItem('darkMode') === 'true') {
    enableDarkMode();
  }

  toggleBtn.addEventListener('click', () => {
    if (body.classList.contains('dark-mode')) {
      disableDarkMode();
    } else {
      enableDarkMode();
    }
  });

  function enableDarkMode() {
    body.classList.add('dark-mode');
    sidebarElement.classList.add('dark-mode');
    cards.forEach(card => card.classList.add('dark-mode'));
    toggleBtn.setAttribute('aria-pressed', 'true');
    toggleBtn.innerHTML = '<i class="bi bi-sun-fill"></i> Light Mode';
    localStorage.setItem('darkMode', 'true');
  }

  function disableDarkMode() {
    body.classList.remove('dark-mode');
    sidebarElement.classList.remove('dark-mode');
    cards.forEach(card => card.classList.remove('dark-mode'));
    toggleBtn.setAttribute('aria-pressed', 'false');
    toggleBtn.innerHTML = '<i class="bi bi-moon-fill"></i> Dark Mode';
    localStorage.setItem('darkMode', 'false');
  }

  // Refresh counts via AJAX/fetch
  document.getElementById('refreshCounts').addEventListener('click', async () => {
    const btn = document.getElementById('refreshCounts');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Refreshing...';

    try {
      const response = await fetch('ajax_get_counts.php'); // You need to create this file
      if (!response.ok) throw new Error('Network error');

      const data = await response.json();
      if(data.orders !== undefined) {
        document.getElementById('ordersDesc').textContent = `${data.orders} orders placed.`;
      }
      if(data.products !== undefined) {
        document.getElementById('productsDesc').textContent = `${data.products} products available.`;
      }
      if(data.users !== undefined) {
        document.getElementById('usersDesc').textContent = `${data.users} registered users.`;
      }

    } catch (error) {
      alert('Failed to refresh counts: ' + error.message);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refresh Counts';
    }
  });

  // Simple live filter for cards based on search input
  document.getElementById('searchInput').addEventListener('input', (e) => {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.card-wrapper').forEach(wrapper => {
      const text = wrapper.getAttribute('data-content');
      if(text.includes(term)) {
        wrapper.style.display = '';
      } else {
        wrapper.style.display = 'none';
      }
    });
  });
</script>

</body>
</html>
