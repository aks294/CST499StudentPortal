<?php
declare(strict_types=1);
require_once __DIR__ . '/admin_only.php';
require_once __DIR__ . '/Database.php';

$msg = $err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $code     = strtoupper(trim($_POST['code'] ?? ''));
    $title    = trim($_POST['title'] ?? '');
    $credits  = (int)($_POST['credits'] ?? 0);
    $capacity = (int)($_POST['capacity'] ?? 0);

    if ($code === '' || $title === '' || $credits <= 0 || $capacity <= 0) {
      throw new RuntimeException('All fields are required and must be positive.');
    }

    Database::run(
      "INSERT INTO courses (code, title, credits, capacity) VALUES (:c,:t,:cr,:cap)",
      [':c'=>$code, ':t'=>$title, ':cr'=>$credits, ':cap'=>$capacity]
    );
    $msg = 'Course added.';
  } catch (Throwable $e) {
    $err = $e->getMessage();
  }
}

$courses = Database::run("SELECT * FROM courses ORDER BY code")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">Manage Courses</h1>
  <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header">Add Course</div>
    <div class="card-body">
      <form method="post" class="row g-2">
        <div class="col-md-2">
          <label class="form-label">Code</label>
          <input class="form-control" name="code" placeholder="CST310">
        </div>
        <div class="col-md-5">
          <label class="form-label">Title</label>
          <input class="form-control" name="title" placeholder="Web App Dev (PHP)">
        </div>
        <div class="col-md-2">
          <label class="form-label">Credits</label>
          <input type="number" class="form-control" name="credits" min="1" max="12" value="3">
        </div>
        <div class="col-md-2">
          <label class="form-label">Capacity</label>
          <input type="number" class="form-control" name="capacity" min="1" max="500" value="30">
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button class="btn btn-primary w-100">Add</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Existing Courses</div>
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead><tr><th>Code</th><th>Title</th><th>Credits</th><th>Capacity</th></tr></thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['code']) ?></td>
            <td><?= htmlspecialchars($c['title']) ?></td>
            <td><?= (int)$c['credits'] ?></td>
            <td><?= (int)$c['capacity'] ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <a class="btn btn-outline-secondary mt-4" href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
