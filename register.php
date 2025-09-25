<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/Database.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($first === '') { $errors[] = 'First name is required.'; }
    if ($last === '')  { $errors[] = 'Last name is required.'; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'A valid email is required.'; }
    if (strlen($pass) < 8) { $errors[] = 'Password must be at least 8 characters.'; }
    if ($pass !== $confirm) { $errors[] = 'Passwords do not match.'; }

    if (!$errors) {
        $pdo = Database::conn();
        // Check for duplicate email
        $stmt = $pdo->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO students (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)');
            $ins->execute([$first, $last, $email, $hash]);
            $success = true;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 720px;">
  <h1 class="mb-4">Create your student account</h1>

  <?php if ($success): ?>
    <div class="alert alert-success">Registration successful. You may <a href="login.php" class="alert-link">log in</a> now.</div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">First name</label>
        <input class="form-control" name="first_name" required value="<?= htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Last name</label>
        <input class="form-control" name="last_name" required value="<?= htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" required>
        <div class="form-text">At least 8 characters.</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Confirm password</label>
        <input type="password" class="form-control" name="confirm_password" required>
      </div>
      <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="login.php">I already have an account</a>
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
