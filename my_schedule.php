<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/Database.php';

if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$rows = Database::run("
  SELECT e.id AS enrollment_id, c.code, c.title, c.credits, s.name AS semester, e.enrolled_at
  FROM enrollments e
  JOIN course_offerings o ON e.offering_id = o.id
  JOIN courses c         ON o.course_id = c.id
  JOIN semesters s       ON o.semester_id = s.id
  WHERE e.student_id = :s AND e.status='enrolled'
  ORDER BY c.code
", [':s' => $_SESSION['user_id']])->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Schedule</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">My Schedule</h1>
  <?php if (isset($_GET['msg']) && $_GET['msg']==='added'): ?>
    <div class="alert alert-success">Class added.</div>
  <?php elseif (isset($_GET['msg']) && $_GET['msg']==='dropped'): ?>
    <div class="alert alert-warning">Class dropped.</div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
  <?php endif; ?>

  <table class="table table-striped align-middle">
    <thead><tr><th>Code</th><th>Title</th><th>Credits</th><th>Semester</th><th>Enrolled</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['code']) ?></td>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= (int)$r['credits'] ?></td>
        <td><?= htmlspecialchars($r['semester']) ?></td>
        <td><?= htmlspecialchars($r['enrolled_at']) ?></td>
        <td>
          <form method="post" action="drop_class.php" class="m-0">
            <input type="hidden" name="enrollment_id" value="<?= (int)$r['enrollment_id'] ?>">
            <button class="btn btn-outline-danger btn-sm" type="submit">Drop</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <a class="btn btn-outline-secondary" href="offerings.php">Add More Classes</a>
  <a class="btn btn-outline-secondary" href="dashboard.php">Back to Dashboard</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
