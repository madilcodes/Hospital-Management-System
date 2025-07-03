<?php

session_start();
// Session timeout: 30 minutes
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

require_once 'dbconnect.php';

if (!isset($_SESSION['username']) || $_SESSION['employee_type'] !== 'Admin') {
    header('Location: login.php');
    exit();
}
// Pagination 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search staff users
$search = $_GET['search'] ?? '';
$search_sql = '';
if ($search !== '') {
    $search_sql = "AND name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$users = $conn->query("SELECT * FROM users WHERE employee_type = 'Staff' $search_sql");

$logs = $conn->query("SELECT id,username,punch_in,punch_out,SEC_TO_TIME(TIMESTAMPDIFF(SECOND, punch_in, punch_out)) AS working_duration  FROM attendance ORDER BY punch_in DESC LIMIT $limit OFFSET $offset");

// Fetch lab reports
$reports = $conn->query("SELECT * FROM patient_history");

// Fetch appointments
$appointments = $conn->query("SELECT * FROM appointments ORDER BY appoint_id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body { background: #f8f9fa; }
    .container { margin-bottom: 40px; }
    .table th, .table td { vertical-align: middle !important; }
table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }
    th {
        background-color: #f2f2f2;
    }
    .table-container {
        max-height: 550px;
        overflow-y: auto;
    }
    .fixed-header {
        position: sticky;
        top: 0;
        background-color: #f2f2f2;
    }
  </style>
<script>
$(document).ready(function(){
       $("tbody").before(headerClone);
   });
</script>
</head>
<body>
<div class="container mt-4">
  <h2 class='text-center'>Admin Dashboard</h2>
<div class="mb-3" >
<a href="#" class=" float-left mb-2 mr-2" data-toggle="modal" data-target="#changePasswordModal" title="Settings">
    <i class="fas fa-cog" title='update password'></i>
</a>
  <a href="logout.php" class="float-right mb-2"><i class="fas fa-sign-out-alt" title='logout'></i></a>
  <a href="admin_dashboard.php" class=" mb-2"><i class="fas fa-sync-alt" title='refresh'></i></a>
</div>
<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="POST" action="update_admin_password.php">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" class="form-control" name="current_password" required>
          </div>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
          </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Password</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
  <!-- Search staff -->
  <form method="GET" class="form-inline mb-3">
      <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-info ml-2"> <i class="fas fa-search" title='Search Staff'></i></button>
  </form>
<!-- Create Staff -->
<h4 class="mt-5 d-inline-block">Create Staff</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#createStaffForm" aria-expanded="false" aria-controls="createStaffForm">+</button>
<div class="collapse mt-3" id="createStaffForm">
  <form method="POST" action="create_user.php">
    <input type="text" name="name" placeholder="Name" required class="form-control mb-2">
    <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
    <input type="password" name="password" placeholder="Password" required class="form-control mb-2">
    <input type="text" name="email" placeholder="Email" class="form-control mb-2">
    <input type="text" name="phone" placeholder="Phone" class="form-control mb-2">
    <input type="text" name="designation" placeholder="Designation" class="form-control mb-2">
    <input type="text" name="address" placeholder="Address" class="form-control mb-2">
    <input type="number" name="salary" placeholder="Salary" class="form-control mb-2">
    <button type="submit" class="btn btn-primary">Create User</button>
  </form>
</div>
<br>
<!-- Staff List -->
  <h4 class="mt-5">Staff List</h4>
  <table class="table table-bordered table-striped">
     <thead class="fixed-header">
      <tr class="table-success">
        <th>FullName</th>
        <th>UserId</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Designation</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>

 <?php if ($users->num_rows > 0): ?>
      <?php while($row = $users->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['designation']) ?></td>
        <td>
          <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"> <i class="fas fa-edit" title='Edit User'></i></a>
          <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')"> <i class="fas fa-trash" title='Delete User'></i></a>
        </td>
      </tr>
      <?php endwhile; ?>
<?php else: ?>
      <tr>
        <td colspan="7" class="text-center text-muted">No records</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
<br>
<br>
  <!-- Attendance Logs -->
  <h4 class="mt-5 d-inline block">Attendance Logs</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#showAttanForm" aria-expanded="false" aria-controls="showAttanForm">+</button>
<div class="collapse mt-3" id="showAttanForm">

  <table class="table table-bordered  table-striped">
    <thead>
      <tr class="table-warning">
        <th>UserId</th>
        <th>Punch In</th>
        <th>Punch Out</th>
        <th>Working Duration</th>
      </tr>
    </thead>
    <tbody>
 <?php if ($logs->num_rows > 0): ?>
      <?php while($log = $logs->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($log['username']) ?></td>
        <td><?= htmlspecialchars($log['punch_in']) ?></td>
        <td><?= htmlspecialchars($log['punch_out']) ?></td>
        <td><?= htmlspecialchars($log['working_duration']) ?></td>
      </tr>
      <?php endwhile; ?>
<?php else: ?>
      <tr>
        <td colspan="4" class="text-center text-muted">No records</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
<br>
<!-- Patient Registration  -->
<h4 class="mt-5 d-inline-block">Patient Registration</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#registerPatientForm" aria-expanded="false" aria-controls="registerPatientForm">+</button>
<div class="collapse mt-3" id="registerPatientForm">
  <form method="POST" action="register_patient.php">
    <input type="text" name="name" placeholder="Name" required class="form-control mb-2">
    <input type="number" name="age" placeholder="Age" class="form-control mb-2">
    <select name="gender" class="form-control mb-2">
      <option value="">Gender</option>
      <option>Male</option>
      <option>Female</option>
      <option>Other</option>
    </select>
    <input type="text" name="phone" placeholder="Phone" class="form-control mb-2">
    <input type="text" name="address" placeholder="Address" class="form-control mb-2">
    <button type="submit" class="btn btn-primary">Register Patient</button>
  </form>
</div>
<br>
<!-- Patients list-->
<h4 class="mt-5 d-inline-block">Patients</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#showPatientList" aria-expanded="false" aria-controls="showPatientList">+</button>
<div class="collapse mt-3" id="showPatientList">
  <table class="table table-bordered table-striped">
    <tr class="table-info"><th>RegistrationID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>History</th></tr>
    <?php
    $patients = $conn->query("SELECT * FROM patients");
    if ($patients->num_rows > 0):
      while ($p = $patients->fetch_assoc()):
    ?>
      <tr>
        <td><?= htmlspecialchars($p['patient_registration_id']) ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['age']) ?></td>
        <td><?= htmlspecialchars($p['gender']) ?></td>
        <td><?= htmlspecialchars($p['phone']) ?></td>
        <td>

<a href="patient_history.php?patient_id=<?= $p['id'] ?>&phone=<?= urlencode($p['phone']) ?>patient_name=<?= $p['name'] ?>" class="btn btn-info btn-sm">View History</a>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="6" class="text-center text-muted">No records</td></tr>
    <?php endif; ?>
  </table>
</div>
<br>
    <!-- Appointments -->
<br>
  <h4 class="mt-5 d-inline block">Patient Appointments</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#showpatientForm" aria-expanded="false" aria-controls="showpatientForm">+</button>
<div class="collapse mt-3" id="showpatientForm">
  <table class="table table-bordered table-striped">
    <tr class="table-active"><th>AppointmentID</th><th>PatientName</th><th>Phone</th><th>AppointmentDate</th><th>Specialist</th></tr>
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
<br>
  <!-- Upload lab report -->
<br>
<h4>Upload Reports</h4>
  <form method="POST" enctype="multipart/form-data" action="save_report.php" class="mb-5">
    <div class="form-row">
 <div class="col">
        <input type="text" name="appoint_id" placeholder="Appointment-ID" class="form-control mb-2" required>
      </div>

      <div class="col">
        <input type="text" name="patient_name" placeholder="Patient Name" class="form-control mb-2" required>
      </div>
<div class="col">
        <input type="text" name="patient_ph" placeholder="Patient PhNo" class="form-control mb-2" required>
      </div>

 <div class="col">
        <input type="text" name="doctor" placeholder="Dr Name" class="form-control mb-2" required>
      </div>
 <div class="col">
        <input type="text" name="diagnosis" placeholder="Diagnosis" class="form-control mb-2" required>
      </div>

      <div class="col">
        <input type="file" name="report" accept=".pdf" class="form-control mb-2" title='Only PDF files are allowed.' required>
      </div>
      <div class="col">
        <button class="btn btn-primary mb-2" title='Upload report'>Upload</button>
      </div>
    </div>
  </form>
  <!-- Lab Reports -->
  <h4 class="mt-5 d-inline block">Lab Reports</h4>
<button class="btn btn-success btn-sm ml-2" type="button" data-toggle="collapse" data-target="#showReportForm" aria-expanded="false" aria-controls="showReportForm">+</button>
<div class="collapse mt-3" id="showReportForm">
  <table class="table   table-bordered table-striped">
    <tr class="table-success"><th>AppointmentId</th><th>PatientName</th><th>Report</th></tr>
 <?php if ($reports->num_rows > 0): ?>
    <?php while ($r = $reports->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['appoint_id']) ?></td>
        <td><?= htmlspecialchars($r['patient_name']) ?></td>
        <td><a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank">Download</a></td>
      </tr>
    <?php endwhile; ?>
<?php else: ?>
      <tr>
        <td colspan="3" class="text-center text-muted">No records</td>
      </tr>
    <?php endif; ?>
  </table>
 </div>
</div>
</body>
</html>
