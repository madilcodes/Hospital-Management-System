<?php
require_once 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appoint_id = ($_POST['appoint_id']);
    $patient_name = trim($_POST['patient_name']);
    $patient_ph = ($_POST['patient_ph']);
    $doctor = ($_POST['doctor']);
    $diagnosis = ($_POST['diagnosis']);
    if (isset($_FILES['report']) && $_FILES['report']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['report']['tmp_name'];
        $fileName = basename($_FILES['report']['name']);
        $fileType = mime_content_type($fileTmp);

        if ($fileType !== 'application/pdf') {
            echo "Only PDF files are allowed.";
            exit();
        }

        $uploadsDir = "uploads";
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        $targetPath = $uploadsDir . "/" . uniqid() . "_" . $fileName;
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO patient_history (appoint_id,patient_name,phone,doctor,diagnosis,file_path) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $appoint_id,$patient_name,$patient_ph,$doctor,$diagnosis,$targetPath);
            if ($stmt->execute()) {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Database error.";
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "No file uploaded.";
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
