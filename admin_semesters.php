<?php
declare(strict_types=1);
require_once __DIR__ . '/admin_only.php';
require_once __DIR__ . '/Database.php';

$msg = $err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    if (isset($_POST['create'])) {
      $name  = trim($_POST['name'] ?? '');
      $start = $_POST['start_date'] ?? '';
      $end   = $_POST['end_date'] ?? '';
      if ($name === '' || $start === '' || $end === '') {
        throw new RuntimeException('All fields are required.');
      }
      Database::run(
        "INSERT INTO semesters (name, start_date, end_date, is_active) VALUES (:n,:s,:e,0)",
        [':n'=>$name, ':s'=>$start, ':e'=>$end]
      );
      $msg = 'Semester created.';
    }

    if (isset($_POST['activate'])) {
      $id = (int)($_POST['id'] ?? 0);
      Database::begin();
      Database::run("UPDATE semesters SET is_active=0 WHERE is_active=1");
      Database::run("UPDATE semesters SET is_active=1 WHERE id=:id", [':id'=>$id]);
      Database::commit();
      $msg = 'Semester activated.';
    }
  } catch (Throwable $e) {
    if (Database::inTx()) Database::rollBack();
    $err = $e->getMessage();
  }
}

$semesters = Database::run("SELECT * FROM semesters ORDER BY start_date DESC")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Semesters</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h1 class="mb-4">Manage Semesters</h1>
  <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <div class="card mb-4">
    <div class="card-header">Create Semester</div>
    <div class="card-body">
      <form method="post" class="row g-2">
        <div class="col-md-4">
          <label class="form-label">Name</label>
          <input class="form-control" name="name" placeholder="Fall 2025">
        </div>
        <div class="col-md-3">
          <label class="form-label">Start date</label>
          <input type="date" class="form-control" name="start_date">
        </div>
        <div class="col-md-3">
          <label class="form-label">End date</label>
          <input type="date" class="form-control" name="end_date">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-primary w-100" name="create" value="1">Create</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Existing Semesters</div>
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead><tr><th>Name</th><th>Start</th><th>End</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($semesters as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['start_date']) ?></td>
            <td><?= htmlspecialchars($s['end_date']) ?></td>
            <td><?= $s['is_active'] ? 'Active' : '—' ?></td>
            <td>
              <?php if (!$s['is_active']): ?>
                <form method="post" class="m-0">
                  <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                  <button class="btn btn-outline-primary btn-sm" name="activate" value="1">Set Active</button>
                </form>
              <?php else: ?>
                <span class="text-success">Current</span>
              <?php endif; ?>
            </td>
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
