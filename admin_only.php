<?php
declare(strict_types=1);
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (($_SESSION['role'] ?? 'student') !== 'admin') { header('Location: dashboard.php'); exit; }
