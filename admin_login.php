<?php
session_start();
require_once 'connect.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!-- Bootstrap Admin Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">Admin Login</h3>
    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Admin Email</label>
        <input type="email" name="email" id="email" class="form-control" required placeholder="admin@example.com">
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required placeholder="Password">
      </div>
      <?php if (isset($error)): ?>
        <div class="alert alert-danger p-2 text-center"><?php echo $error; ?></div>
      <?php endif; ?>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</div>

</body>
</html>
