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
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background: #f8f9fa; }
    .dashboard-card { margin-top: 40px; }
  </style>
</head>
<body>
<div class="container dashboard-card">
  <div class="card p-3">
<div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
      </div>
<hr>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?> |
    <strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
    <p><strong>Designation:</strong> <?= htmlspecialchars($user['designation']) ?> |
    <?php $user_specialty = $user['specialty']; ?>
    <strong>Specialty:</strong> <?= htmlspecialchars($user_specialty) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?> |
    <strong>Salary:</strong> ₹<?= htmlspecialchars($user['salary']) ?></p>
    <hr>
    <h5>Today's Attendance</h5>
    <p><strong>Clock In:</strong> <?= htmlspecialchars($punch_in) ?> |
    <strong>Clock Out:</strong> <?= htmlspecialchars($punch_out) ?></p>
    <form method="POST" class="mt-3">
      <button name="action" value="punch_in" class="btn btn-success" <?= ($punch_in !== 'Not punched in yet') ? 'disabled' : '' ?>>Punch In</button>
      <button name="action" value="punch_out" class="btn btn-danger" <?= ($punch_in === 'Not punched in yet' || $punch_out !== 'Not punched out yet') ? 'disabled' : '' ?>>Punch Out</button>
    </form>
  </div>
 <h4 class="mt-5 d-inline block">Patient Appointments</h4>
    <button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#showpatientForm"
      aria-expanded="false" aria-controls="showpatientForm">+</button>
    <div class="collapse mt-3" id="showpatientForm">
      <table class="table table-bordered table-striped">
        <tr class="table-active">
          <th>AppointmentID</th>
          <th>PatientName</th>
          <th>Phone</th>
          <th>AppointmentDate</th>
          <th>Specialist</th>
	  </tr>
	  <?php
	  $appointments = $conn->query("SELECT * FROM appointments WHERE doctor='$user_specialty' ORDER BY appoint_id ASC");
	  ?>
	  <?php if ($appointments->num_rows > 0): ?>
	  <?php while ($row = $appointments->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['appoint_id']) ?></td>
              <td><?= htmlspecialchars($row['patient_name']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td><?= htmlspecialchars($row['doctor']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted">No records</td>
          </tr>
        <?php endif; ?>
      </table>
    </div>
</div>
