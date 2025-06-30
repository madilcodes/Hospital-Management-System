<?php
$host = 'localhost';
$user = 'convox32';
$pass = 'convox32'; // default XAMPP password is empty
$db   = 'hospital_management';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
