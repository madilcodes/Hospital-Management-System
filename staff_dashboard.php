<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['username']) || $_SESSION['employee_type'] !== 'Staff') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Attendance logic
$punch_in = 'Not punched in yet';
$punch_out = 'Not punched out yet';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $timestamp = date('Y-m-d H:i:s');

    if ($action === 'punch_in') {
        // Prevent multiple punch-ins for the same day
        $check = $conn->prepare("SELECT * FROM attendance WHERE username = ? AND DATE(punch_in) = CURDATE()");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO attendance (username, punch_in) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $timestamp);
            $stmt->execute();
        }
    } elseif ($action === 'punch_out') {
        // Only punch out if already punched in and not punched out yet
        $check = $conn->prepare("SELECT * FROM attendance WHERE username = ? AND DATE(punch_in) = CURDATE() AND punch_out IS NULL");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows === 1) {
            $stmt = $conn->prepare("UPDATE attendance SET punch_out = ? WHERE username = ? AND DATE(punch_in) = CURDATE()");
            $stmt->bind_param("ss", $timestamp, $username);
            $stmt->execute();
        }
    }
    // Refresh to show updated status
    header("Location: staff_dashboard.php");
    exit();
}

// Fetch today's punch info after any action
$att = $conn->prepare("SELECT * FROM attendance WHERE username = ? AND DATE(punch_in) = CURDATE()");
$att->bind_param("s", $username);
$att->execute();
$already_punched = $att->get_result()->fetch_assoc();
if ($already_punched) {
    $punch_in = $already_punched['punch_in'];
    $punch_out = $already_punched['punch_out'] ?? 'Not punched out yet';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body { background: #f8f9fa; }
    .dashboard-card { margin-top: 40px; }
  </style>
</head>
<body>
<div class="container dashboard-card">
  <div class="card p-4">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    <p><strong>Designation:</strong> <?= htmlspecialchars($user['designation']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
    <p><strong>Salary:</strong> ₹<?= htmlspecialchars($user['salary']) ?></p>
    <hr>
    <h5>Today's Attendance</h5>
    <p><strong>Punch In:</strong> <?= htmlspecialchars($punch_in) ?></p>
    <p><strong>Punch Out:</strong> <?= htmlspecialchars($punch_out) ?></p>
    <form method="POST" class="mt-3">
      <button name="action" value="punch_in" class="btn btn-success" <?= ($punch_in !== 'Not punched in yet') ? 'disabled' : '' ?>>Punch In</button>
      <button name="action" value="punch_out" class="btn btn-danger" <?= ($punch_in === 'Not punched in yet' || $punch_out !== 'Not punched out yet') ? 'disabled' : '' ?>>Punch Out</button>
    </form>
    <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
  </div>
</div>