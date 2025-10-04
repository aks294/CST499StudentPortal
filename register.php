<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/Database.php';

// If already logged in, skip registration
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $email     = trim($_POST['email']      ?? '');
    $password  = $_POST['password']        ?? '';
    $confirm   = $_POST['confirm']         ?? '';

    try {
        // Basic validation
        if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirm === '') {
            throw new RuntimeException('All fields are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid email address.');
        }
        if (strlen($password) < 8) {
            throw new RuntimeException('Password must be at least 8 characters long.');
        }
        if ($password !== $confirm) {
            throw new RuntimeException('Passwords do not match.');
        }

        // Duplicate email check
        $exists = Database::run(
            "SELECT id FROM students WHERE email = :email LIMIT 1",
            [':email' => $email]
        )->fetchColumn();
        if ($exists) {
            throw new RuntimeException('An account with this email already exists.');
        }

        // Hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Compute full_name for convenience
        $fullName = trim($firstName . ' ' . $lastName);

        // Insert new student (role defaults to 'student')
        Database::run(
            "INSERT INTO students (first_name, last_name, email, full_name, password_hash, role)
             VALUES (:first_name, :last_name, :email, :full_name, :password_hash, 'student')",
            [
                ':first_name'   => $firstName,
                ':last_name'    => $lastName,
                ':email'        => $email,
                ':full_name'    => $fullName,
                ':password_hash'=> $hash,
            ]
        );

        // Auto-login
        $newId = Database::lastId();
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$newId;
        $_SESSION['user_name'] = $fullName ?: $email;
        $_SESSION['role']      = 'student';

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
  <title>Register — Student Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-4 text-center">Create Account</h1>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <div class="mb-3">
              <label for="first_name" class="form-label">First name</label>
              <input type="text" class="form-control" id="first_name" name="first_name"
                     value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label for="last_name" class="form-label">Last name</label>
              <input type="text" class="form-control" id="last_name" name="last_name"
                     value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email"
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password"
                     placeholder="At least 8 characters" required>
            </div>

            <div class="mb-3">
              <label for="confirm" class="form-label">Confirm password</label>
              <input type="password" class="form-control" id="confirm" name="confirm" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create Account</button>
          </form>

          <hr class="my-4">
          <p class="text-center mb-0">
            Already have an account? <a href="login.php">Login here</a>.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
