<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/Database.php';

if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$rows = Database::run("
  SELECT o.id AS offering_id, c.code, c.title, c.credits, s.name AS semester, o.seats_available
  FROM course_offerings o
  JOIN courses c   ON o.course_id = c.id
  JOIN semesters s ON o.semester_id = s.id
  WHERE s.is_active = 1
  ORDER BY c.code
")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Available Classes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">Available Classes (Active Semester)</h1>
  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>
  <table class="table table-striped align-middle">
    <thead><tr><th>Code</th><th>Title</th><th>Credits</th><th>Seats</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['code']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= (int)$r['credits'] ?></td>
        <td><?= (int)$r['seats_available'] ?></td>
        <td>
          <form method="post" action="register_class.php" class="m-0">
            <input type="hidden" name="offering_id" value="<?= (int)$r['offering_id'] ?>">
            <button class="btn btn-primary btn-sm" type="submit" <?= $r['seats_available'] <= 0 ? 'disabled' : '' ?>>
              Add
            </button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <a class="btn btn-outline-secondary" href="dashboard.php">Back to Dashboard</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
