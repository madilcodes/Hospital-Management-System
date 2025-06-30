<?php

require_once 'dbconnect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user details for the form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) {
        echo "User not found.";
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $designation = trim($_POST['designation']);
    $address = trim($_POST['address']);
    $salary = trim($_POST['salary']);

    $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, phone=?, designation=?, address=?, salary=? WHERE id=?");
    $stmt->bind_param("sssssssi", $name, $username, $email, $phone, $designation, $address, $salary, $id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating user.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Edit Staff</h3>
    <form method="POST">
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="form-control mb-2">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="form-control mb-2">
        <input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control mb-2">
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control mb-2">
        <input type="text" name="designation" value="<?= htmlspecialchars($user['designation']) ?>" class="form-control mb-2">
        <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" class="form-control mb-2">
        <input type="number" name="salary" value="<?= htmlspecialchars($user['salary']) ?>" class="form-control mb-2">
        <button type="submit" class="btn btn-success">Update</button>
        <a href="admin_dashboard.php" class="btn btn-secondary ml-2">Cancel</a>
    </form>
</div>