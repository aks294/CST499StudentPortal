<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/Database.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        if ($email === '' || $password === '') {
            throw new RuntimeException('Email and password are required.');
        }

        // Pull exactly your columns; alias a display_name and alias password_hash as password
        $stmt = Database::run(
            "SELECT 
                id,
                email,
                COALESCE(full_name, CONCAT(first_name,' ',last_name)) AS display_name,
                password_hash AS password,
                role
             FROM students
             WHERE email = :email
             LIMIT 1",
            [':email' => $email]
        );

        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            throw new RuntimeException('Invalid email or password.');
        }

        // Success — set session
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['user_name'] = $user['display_name'] ?: $user['email'];
        $_SESSION['role']      = $user['role'] ?? 'student';

        header('Location: dashboard.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — Student Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-4 text-center">Login</h1>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email"
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
          </form>

          <hr class="my-4">
          <p class="text-center mb-0">
            Don’t have an account? <a href="register.php">Register here</a>.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
