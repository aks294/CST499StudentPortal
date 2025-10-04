<?php
declare(strict_types=1);
require_once __DIR__ . '/admin_only.php';
require_once __DIR__ . '/Database.php';

$msg = $err = null;

$active  = Database::run("SELECT * FROM semesters WHERE is_active=1 LIMIT 1")->fetch();
$courses = Database::run("SELECT * FROM courses ORDER BY code")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (!$active) throw new RuntimeException('No active semester. Activate one first on Semesters page.');
    $courseId = (int)($_POST['course_id'] ?? 0);
    if ($courseId <= 0) throw new RuntimeException('Select a course.');

    $cap = Database::run("SELECT capacity FROM courses WHERE id=:id", [':id'=>$courseId])->fetchColumn();
    if ($cap === false) throw new RuntimeException('Course not found.');

    Database::run(
      "INSERT INTO course_offerings (course_id, semester_id, seats_available) VALUES (:c,:s,:seats)",
      [':c'=>$courseId, ':s'=>$active['id'], ':seats'=>(int)$cap]
    );
    $msg = 'Offering created for '.$active['name'].'.';
  } catch (Throwable $e) {
    $err = $e->getMessage();
  }
}

$offerings = Database::run("
  SELECT o.id, c.code, c.title, s.name AS semester, o.seats_available
  FROM course_offerings o
  JOIN courses c ON o.course_id=c.id
  JOIN semesters s ON o.semester_id=s.id
  ORDER BY s.start_date DESC, c.code
")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Offerings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">Manage Offerings</h1>
  <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header">Create Offering (Active Semester)</div>
    <div class="card-body">
      <?php if (!$active): ?>
        <div class="alert alert-warning">No active semester. Go to <a href="admin_semesters.php">Semesters</a> and set one active.</div>
      <?php endif; ?>

      <form method="post" class="row g-2">
        <div class="col-md-6">
          <label class="form-label">Course</label>
          <select class="form-select" name="course_id" <?= !$active ? 'disabled' : '' ?>>
            <option value="">Select course…</option>
            <?php foreach ($courses as $c): ?>
              <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['code'].' — '.$c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Semester</label>
          <input class="form-control" value="<?= $active ? htmlspecialchars($active['name']) : '—' ?>" disabled>
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button class="btn btn-primary w-100" <?= !$active ? 'disabled' : '' ?>>Create Offering</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Existing Offerings</div>
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead><tr><th>Code</th><th>Title</th><th>Semester</th><th>Seats Available</th></tr></thead>
        <tbody>
        <?php foreach ($offerings as $o): ?>
          <tr>
            <td><?= htmlspecialchars($o['code']) ?></td>
            <td><?= htmlspecialchars($o['title']) ?></td>
            <td><?= htmlspecialchars($o['semester']) ?></td>
            <td><?= (int)$o['seats_available'] ?></td>
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
