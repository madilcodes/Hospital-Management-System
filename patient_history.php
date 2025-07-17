<?php
require_once 'dbconnect.php';

$patient_name = $_GET['patient_name'];
$phone = isset($_GET['phone']) ? intval($_GET['phone']) : 0;
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

// Fetch patient details
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ? OR phone = ?");
$stmt->bind_param("ii", $patient_id,$phone);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    echo "<h3 class='text-center text-danger'>Patient not found.</h3>";
    exit();
}

// Fetch appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE  phone= $phone  ORDER BY appoint_id  DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$sql = "SELECT MAX(CAST(appoint_id AS UNSIGNED)) AS last_id FROM appointments";
$result = $conn->query($sql);

$last_id = 40100; // Default start
if ($row = $result->fetch_assoc()) {
    if (!empty($row['last_id'])) {
        $last_id = $row['last_id'];
    }
}
$response ='';
// Generate next appointments
$new_appoint_id = $last_id + 1;

  $stmt = $conn->prepare("INSERT INTO appointments (appoint_id,patient_name, phone, date, doctor) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss",$new_appoint_id, $_POST['name'], $phone, $_POST['date'], $_POST['doctor']);
  $stmt->execute();
 $response = "Appointment booked successfully.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient History</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">   
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div>
 <?php if ($response): ?>
            <div class="alert alert-success"><?= htmlspecialchars($response) ?></div>
          <?php endif; ?>
</div>
<div class="container mt-4">
    <h2 class="mb-4">Patient History: <?= htmlspecialchars($patient['name']) ?></h2>
    <p><strong>PatientId:</strong> <?= htmlspecialchars($patient['patient_registration_id']) ?> | <strong>Age:</strong> <?= htmlspecialchars($patient['age']) ?> | <strong>Gender:</strong> <?= htmlspecialchars($patient['gender']) ?> | <strong>Phone:</strong> <?= htmlspecialchars($patient['phone']) ?></p>
    <hr>
<h4 class="mt-5 d-inline-block">New Appointment</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#createStaffForm" aria-expanded="false" aria-controls="createStaffForm">+</button>
<div class="collapse mt-2" id="createStaffForm">
  <form method="POST" action="">
    <input type="text" class="form-control form-control-sm mb-2" id="name" name="name" value="<?=$patient['name']?>" readonly>
    <input type="date" class="form-control form-control-sm mb-2" id="appointmentDate" name="date" placeholder="Select Appointment Date'" title='Select Appointment Date' required>

    <select class="form-control form-control-sm mb-2" id="specialty" name="doctor" placeholder="Select Specialty" required>
      <option value="">Select</option>
      <option>Cardiology</option>
      <option>Neurology</option>
      <option>Orthopedics</option>
      <option>Pediatrics</option>
      <option>Gastroenterology</option>
      <option>Oncology</option>
      <option>Nephrology</option>
      <option>Urology</option>
      <option>Ophthalmology</option>
      <option>Dentistry</option>
      <option>Psychiatry</option>
      <option>Radiology</option>
      <option>Anesthesiology</option>
      <option>Endocrinology</option>
      <option>Dermatology</option>
      <option>Pulmonology</option>
    </select>

    <button type="submit" class="btn btn-sm btn-primary">Add Appointment</button>
  </form>
</div>
<br>
<hr>
    <h4>Appointments History</h4>
    <table class="table table-bordered table-striped">
        <tr class="table-active"><th>Appointment ID</th><th>Date</th><th>Doctor</th></tr>
        <?php if ($appointments->num_rows > 0): ?>
	<?php while ($a = $appointments->fetch_assoc()): ?>
	<tr>
	<?php
	$phone_number = $a['phone'];
	?>
                    <td><?= htmlspecialchars($a['appoint_id']) ?></td>
                    <td><?= htmlspecialchars($a['date']) ?></td>
                    <td><?= htmlspecialchars($a['doctor']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" class="text-center text-muted">No appointments</td></tr>
        <?php endif; ?>
    </table>
      <h4 class="mt-4">Medical History & Lab Reports</h4>
<table class="table   table-bordered table-striped">
    <tr class="table-active"><th>AppointmentId</th><th>PatientName</th><th>Doctor</th><th>Diagnosis</th><th>Report</th></tr>
<?php 
// Fetch lab reports
$reports = $conn->query("SELECT * FROM patient_history WHERE phone = $phone_number");
?>
 <?php if ($reports->num_rows > 0): ?>
    <?php while ($r = $reports->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['appoint_id']) ?></td>
        <td><?= htmlspecialchars($r['patient_name']) ?></td>
                       <td><?= htmlspecialchars($r['doctor']) ?></td>
                    <td><?= nl2br(htmlspecialchars($r['diagnosis'])) ?></td>

        <td><a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank">Download</a></td>
      </tr>
    <?php endwhile; ?>
<?php else: ?>
      <tr>
        <td colspan="5" class="text-center text-muted">No records</td>
      </tr>
    <?php endif; ?>
  </table>
    <a href="javascript:history.back()" class="btn btn-warning mt-3">Back to Dashboard</a>
</div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var darkOn = document.getElementById('darkOn');
  var darkOff = document.getElementById('darkOff');
  var body = document.body;

  function setDarkMode(on) {
    if (on) {
      body.style.background = '#222';
      body.style.color = '#f8f9fa';
      document.querySelectorAll('.card, .table, .form-control, .modal-content').forEach(function(el){
        el.style.background = '#333';
        el.style.color = '#f8f9fa';
      });
      localStorage.setItem('darkMode', 'on');
      darkOn.checked = true;
    } else {
      body.style.background = '#f8f9fa';
      body.style.color = '#212529';
      document.querySelectorAll('.card, .table, .form-control, .modal-content').forEach(function(el){
        el.style.background = '';
        el.style.color = '';
      });
      localStorage.setItem('darkMode', 'off');
      darkOff.checked = true;
    }
  }

  // Restore dark mode on page load
  var saved = localStorage.getItem('darkMode');
  if (saved === 'on') {
    setDarkMode(true);
  } else {
    setDarkMode(false);
  }

  darkOn.addEventListener('change', function() { setDarkMode(true); });
  darkOff.addEventListener('change', function() { setDarkMode(false); });
});


</script>
</html>
