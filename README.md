Hospital Management System - PHP & MySQL

========================================
Features:
========================================
- Admin and Staff login system
- Staff attendance (Punch In/Out)
- Staff management (Create, Edit, Delete)
- Appointment booking and management
- Patient registration and medical history tracking
- Lab report upload and download (PDF)
- Responsive Bootstrap UI
- Multiple hospital branches contact info

========================================
Admin Login Credentials:
========================================
Username: admin
Password: admin

(Default password is 'admin'. Change it after first login for security.)

========================================
Setup Instructions:
========================================
1. Import the SQL tables into your MySQL database (phpMyAdmin or CLI).
   - Tables: users, attendance, appointments, patients, patient_history, lab_reports
2. Update `dbconnect.php` with your database credentials if needed.
3. Place the project folder in your XAMPP `htdocs` directory.
4. Start Apache and MySQL from XAMPP control panel.
5. Access the system at: http://localhost/Hospital-Management-System/

========================================
Default Admin User:
========================================
- Username: admin
- Password: admin

========================================
Notes:
========================================
- Only PDF files are allowed for lab report uploads.
- Staff can only access their own dashboard and attendance.
- Admin can manage staff, view attendance, appointments, patients, and lab reports.
- For security, change the admin password after first login.
- Patient registration and history features help doctors and staff quickly access patient details and medical records.

====================================