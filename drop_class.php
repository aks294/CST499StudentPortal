<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/Database.php';

if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
$studentId    = (int)$_SESSION['user_id'];

try {
  $pdo = Database::conn();
  $pdo->beginTransaction();

  // Only drop if it's yours and currently enrolled
  $updated = Database::run("
    UPDATE enrollments
       SET status='dropped'
     WHERE id=:id AND student_id=:s AND status='enrolled'
  ", [':id' => $enrollmentId, ':s' => $studentId])->rowCount();

  if ($updated === 0) throw new RuntimeException('Nothing to drop.');

  // Increment seat back
  Database::run("
    UPDATE course_offerings o
    JOIN enrollments e ON e.offering_id = o.id
       SET o.seats_available = o.seats_available + 1
     WHERE e.id = :id
  ", [':id' => $enrollmentId]);

  $pdo->commit();
  header('Location: my_schedule.php?msg=dropped'); exit;
} catch (Throwable $e) {
  if ($pdo?->inTransaction()) $pdo->rollBack();
  header('Location: my_schedule.php?error=' . urlencode($e->getMessage())); exit;
}
