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
    $specialty = trim($_POST['specialty']);
    $address = trim($_POST['address']);
    $salary = trim($_POST['salary']);

    $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, phone=?, designation=?, specialty=?,address=?, salary=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $name, $username, $email, $phone, $designation,$specialty, $address, $salary, $id);
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
<select name="designation" id="designationDropdown" class="form-control mb-2" required>	
<option value="">Select Designation</option>
	<option value="Doctor" <?= $user['designation'] == 'Doctor' ? 'selected' : '' ?>>Doctor</option>
	<option value="Nurse" <?= $user['designation'] == 'Nurse' ? 'selected' : '' ?>>Nurse</option>
	<option value="Receptionist" <?= $user['designation'] == 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
	<option value="Lab Technician" <?= $user['designation'] == 'Lab Technician' ? 'selected' : '' ?>>Lab Technician</option>
	<option value="Pharmacist" <?= $user['designation'] == 'Pharmacist' ? 'selected' : '' ?>>Pharmacist</option>
	<option value="Accountant" <?= $user['designation'] == 'Accountant' ? 'selected' : '' ?>>Accountant</option>
	<option value="Cleaner" <?= $user['designation'] == 'Cleaner' ? 'selected' : '' ?>>Cleaner</option>
	<option value="Security" <?= $user['designation'] == 'Security' ? 'selected' : '' ?>>Security</option>
	<option value="Other" <?= $user['designation'] == 'Other' ? 'selected' : '' ?>>Other</option>
	</select>       
<select name="specialty" id="specialtyDropdown" class="form-control mb-2" style="display:none;">
  <option value="">Select Specialty</option>
  <option value="Cardiology" <?= isset($user['specialty']) && $user['specialty'] == 'Cardiology' ? 'selected' : '' ?>>Cardiology</option>
  <option value="Neurology" <?= isset($user['specialty']) && $user['specialty'] == 'Neurology' ? 'selected' : '' ?>>Neurology</option>
  <option value="Orthopedics" <?= isset($user['specialty']) && $user['specialty'] == 'Orthopedics' ? 'selected' : '' ?>>Orthopedics</option>
  <option value="Pediatrics" <?= isset($user['specialty']) && $user['specialty'] == 'Pediatrics' ? 'selected' : '' ?>>Pediatrics</option>
  <option value="Gastroenterology" <?= isset($user['specialty']) && $user['specialty'] == 'Gastroenterology' ? 'selected' : '' ?>>Gastroenterology</option>
  <option value="Oncology" <?= isset($user['specialty']) && $user['specialty'] == 'Oncology' ? 'selected' : '' ?>>Oncology</option>
  <option value="Nephrology" <?= isset($user['specialty']) && $user['specialty'] == 'Nephrology' ? 'selected' : '' ?>>Nephrology</option>
  <option value="Urology" <?= isset($user['specialty']) && $user['specialty'] == 'Urology' ? 'selected' : '' ?>>Urology</option>
  <option value="Ophthalmology" <?= isset($user['specialty']) && $user['specialty'] == 'Ophthalmology' ? 'selected' : '' ?>>Ophthalmology</option>
  <option value="Dentistry" <?= isset($user['specialty']) && $user['specialty'] == 'Dentistry' ? 'selected' : '' ?>>Dentistry</option>
  <option value="Psychiatry" <?= isset($user['specialty']) && $user['specialty'] == 'Psychiatry' ? 'selected' : '' ?>>Psychiatry</option>
  <option value="Radiology" <?= isset($user['specialty']) && $user['specialty'] == 'Radiology' ? 'selected' : '' ?>>Radiology</option>
  <option value="Anesthesiology" <?= isset($user['specialty']) && $user['specialty'] == 'Anesthesiology' ? 'selected' : '' ?>>Anesthesiology</option>
  <option value="Endocrinology" <?= isset($user['specialty']) && $user['specialty'] == 'Endocrinology' ? 'selected' : '' ?>>Endocrinology</option>
  <option value="Dermatology" <?= isset($user['specialty']) && $user['specialty'] == 'Dermatology' ? 'selected' : '' ?>>Dermatology</option>
  <option value="Pulmonology" <?= isset($user['specialty']) && $user['specialty'] == 'Pulmonology' ? 'selected' : '' ?>>Pulmonology</option>
</select> 
	<input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>" class="form-control mb-2">
	<input type="number" name="salary" value="<?= htmlspecialchars($user['salary']) ?>" class="form-control mb-2">
	<button type="submit" class="btn btn-success">Update</button>
	<a href="admin_dashboard.php" class="btn btn-secondary ml-2">Cancel</a>
	</form>

</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var designation = document.getElementById('designationDropdown');
    var specialty = document.getElementById('specialtyDropdown');
    if (designation) {
      designation.addEventListener('change', function () {
        if (this.value === 'Doctor') {
          specialty.style.display = '';
          specialty.required = true;
        } else {
          specialty.style.display = 'none';
          specialty.required = false;
          specialty.value = '';
        }
      });
      // On page load, set specialty visibility if editing
      if (designation.value === 'Doctor') {
        specialty.style.display = '';
        specialty.required = true;
      } else {
        specialty.style.display = 'none';
        specialty.required = false;
      }
    }
  });
</script>
</body>
</html>
