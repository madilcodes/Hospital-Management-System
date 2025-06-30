<?php

session_start();
require_once 'dbconnect.php';

// Ensure only admin can delete
if (!isset($_SESSION['username']) || $_SESSION['employee_type'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}