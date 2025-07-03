<?php
require_once 'dbconnect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$sql = "SELECT MAX(CAST(patient_registration_id AS UNSIGNED)) AS last_id FROM patients";
$result = $conn->query($sql);

$last_id = date(ymH); // Default start
if ($row = $result->fetch_assoc()) {
    if (!empty($row['last_id'])) {
        $last_id = $row['last_id'];
    }
}
$patient_registration_id = $last_id + 1;
// Generate next appoint_id
$appoint_id = $last_id + 1;
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $stmt = $conn->prepare("INSERT INTO patients (patient_registration_id,name, age, gender, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss",$patient_registration_id, $name, $age, $gender, $phone, $address);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}
?>
