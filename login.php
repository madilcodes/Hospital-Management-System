
<?php
session_start();
include 'dbconnect.php'; // DB connection with users table

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $employee_type = $_POST['employee_type'];

    // Validate input
    if (empty($username) || empty($password) || empty($employee_type)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND employee_type = ?");
        $stmt->bind_param("ss", $username, $employee_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Use password_verify for hashed passwords
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['employee_type'] = $user['employee_type'];

                if ($employee_type === 'Staff') {
                    header('Location: staff_dashboard.php');
                    exit();
                } else {
                    header('Location: admin_dashboard.php');
                    exit();
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid login details.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Hospital Management System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body { background: #f8f9fa; }
    .login-container { margin-top: 60px; }
    .card { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
  </style>
</head>
<body>
  <div class="container login-container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4">
          <h3 class="text-center mb-4">Login</h3>
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Employee Type</label>
              <select name="employee_type" class="form-control" required>
                <option value="">Select</option>
                <option value="Staff">Staff</option>
                <option value="Admin">Admin</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
          </form>
        </div>
      </div>
    </div>