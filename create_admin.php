<?php
require_once 'connect.php'; // Your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $check = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "❌ Email already exists.";
        } else {
            // Hash the password securely
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into DB
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
            $success = $stmt->execute([$username, $email, $hashedPassword]);

            if ($success) {
                header("Location: admin_login.php");
                exit();
            } else {
                $error = "❌ Failed to create admin.";
            }
        }
    }
}
?>

<!-- Bootstrap Admin Creation Form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow p-4">
        <h4 class="mb-4 text-center">Create Admin Account</h4>

        <?php if (isset($error)): ?>
          <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Admin name">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
          </div>
          <button type="submit" class="btn btn-primary w-100">Create Admin</button>
        </form>

        <div class="mt-3 text-center">
          <a href="admin_login.php" class="text-decoration-none">🔒 Already an admin? Login here</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
