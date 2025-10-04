<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/Database.php';

if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$studentId = (int)$_SESSION['user_id'];
$offeringId = (int)($_POST['offering_id'] ?? 0);

try {
  $pdo = Database::conn();
  $pdo->beginTransaction();

  // Lock offering row for capacity check
  $seats = Database::run(
    "SELECT seats_available FROM course_offerings WHERE id = :id FOR UPDATE",
    [':id' => $offeringId]
  )->fetchColumn();

  if ($seats === false) throw new RuntimeException('Offering not found.');
  if ((int)$seats <= 0) throw new RuntimeException('Section is full.');

  // Prevent duplicate enrollment
  $exists = Database::run(
    "SELECT id FROM enrollments WHERE student_id=:s AND offering_id=:o AND status='enrolled'",
    [':s'=>$studentId, ':o'=>$offeringId]
  )->fetchColumn();
  if ($exists) throw new RuntimeException('You are already enrolled in this class.');

  // Create enrollment + decrement seat
  Database::run(
    "INSERT INTO enrollments (student_id, offering_id) VALUES (:s,:o)",
    [':s'=>$studentId, ':o'=>$offeringId]
  );
  Database::run(
    "UPDATE course_offerings SET seats_available = seats_available - 1 WHERE id = :o",
    [':o'=>$offeringId]
  );

  $pdo->commit();
  header('Location: my_schedule.php?msg=added'); exit;
} catch (Throwable $e) {
  if ($pdo?->inTransaction()) $pdo->rollBack();
  header('Location: offerings.php?error=' . urlencode($e->getMessage())); exit;
}
