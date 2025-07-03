<?php
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['username']) || $_SESSION['employee_type'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        echo "<script>alert('New passwords do not match!');window.location='admin_dashboard.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE username=? AND employee_type='Admin'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hash);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current, $hash)) {
        echo "<script>alert('Current password is incorrect!');window.location='admin_dashboard.php';</script>";
        exit();
    }

    $new_hash = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password=? WHERE username=? AND employee_type='Admin'");
    $update->bind_param("ss", $new_hash, $username);
    if ($update->execute()) {
        echo "<script>alert('Password updated successfully!');window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating password!');window.location='admin_dashboard.php';</script>";
    }
    exit();
}
?>
