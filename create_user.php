<?php

require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $designation = trim($_POST['designation']);
    $specialty = trim($_POST['specialty']);
    $address = trim($_POST['address']);
    $salary = trim($_POST['salary']);

    // Check if username already exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "Username already exists.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, email, phone, designation,specialty, address, salary, employee_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Staff')");
    $stmt->bind_param("sssssssss", $name, $username, $password, $email, $phone, $designation,$specialty, $address, $salary);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error creating user.";
    }
}
