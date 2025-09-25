<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">
    <a class="navbar-brand" href="index.php">Student Portal</a>
    <div class="ms-auto">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a class="btn btn-outline-secondary me-2" href="dashboard.php">Dashboard</a>
        <a class="btn btn-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-primary me-2" href="login.php">Login</a>
        <a class="btn btn-primary" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<header class="py-5 bg-light">
  <div class="container text-center">
    <h1 class="display-5 fw-bold">Welcome to the Student Portal</h1>
    <p class="lead">Register for access and manage your account.</p>
    <a class="btn btn-primary btn-lg" href="register.php">Create an Account</a>
    <a class="btn btn-outline-secondary btn-lg ms-2" href="login.php">I already have an account</a>
  </div>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
