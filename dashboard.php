<?php
declare(strict_types=1);
session_start();

// require login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Student';
$role     = $_SESSION['role']      ?? 'student';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Portal — Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="dashboard.php">Student Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
            aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="offerings.php">Find / Add Classes</a></li>
        <li class="nav-item"><a class="nav-link" href="my_schedule.php">My Schedule</a></li>
        <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="admin_semesters.php">Admin: Semesters</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_courses.php">Admin: Courses</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_offerings.php">Admin: Offerings</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <a class="btn btn-outline-light" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="p-4 p-md-5 bg-white rounded-3 shadow-sm">
        <h1 class="h3 mb-3">Welcome, <?= htmlspecialchars($userName, ENT_QUOTES) ?>!</h1>
        <p class="text-muted mb-4">Use the quick actions below to manage your classes.</p>

        <div class="row g-3">
          <div class="col-12 col-md-6 col-xl-4">
            <a class="btn btn-primary w-100 py-3" href="offerings.php">Browse Active Offerings →</a>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <a class="btn btn-outline-primary w-100 py-3" href="my_schedule.php">View My Schedule →</a>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <a class="btn btn-outline-danger w-100 py-3" href="logout.php">Logout</a>
          </div>
        </div>

        <?php if ($role === 'admin'): ?>
        <hr class="my-4">
        <h2 class="h5">Admin</h2>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-outline-secondary" href="admin_semesters.php">Manage Semesters</a>
          <a class="btn btn-outline-secondary" href="admin_courses.php">Manage Courses</a>
          <a class="btn btn-outline-secondary" href="admin_offerings.php">Manage Offerings</a>
        </div>
        <?php endif; ?>

        <hr class="my-4">
        <h2 class="h5">Checklist for Week 4 screenshots</h2>
        <ul class="mb-0">
          <li>phpMyAdmin: tables created + before/after rows for add & drop</li>
          <li>offerings → add class; schedule → drop class</li>
          <li>code snippets: Database.php (prepared queries), register/drop handlers</li>
        </ul>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
