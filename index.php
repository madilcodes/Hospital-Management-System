<?php
require 'dbconnect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $conn->prepare("INSERT INTO appointments (patient_name, phone, date, doctor) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $_POST['patient_name'], $_POST['phone'], $_POST['date'], $_POST['doctor']);
  $stmt->execute();
  echo "Appointment booked successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .carousel-inner img {
            width: 100%;
            height: 400px;
        }

        .section {
            padding: 40px 0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">MyHospital</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#specialties">Specialties</a></li>
                <li class="nav-item"><a class="nav-link" href="#doctors">Doctors</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                <li class="nav-item"><a class="btn btn-primary" href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>

    <!-- Book Appointment Button -->
    <div class="text-center my-4">
        <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#appointmentModal">
            Book an Appointment
        </button>
    </div>

    <!-- Appointment Modal -->
    <div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Book an Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="patientName">Name</label>
                        <input type="text" class="form-control" id="patientName" name="patient_name" required>
                    </div>
                    <div class="form-group">
                        <label for="patientPhone">Phone</label>
                        <input type="tel" class="form-control" id="patientPhone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="appointmentDate">Date</label>
                        <input type="date" class="form-control" id="appointmentDate" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty">Specialty</label>
                        <select class="form-control" id="specialty" name="doctor" required>
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Book Now</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Carousel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://st4.depositphotos.com/9999814/28806/i/450/depositphotos_288060354-stock-photo-doctor-working-in-hospital-with.jpg"
                    class="d-block w-100" alt="Modern Hospital">
            </div>
            <div class="carousel-item">
                <img src="https://soti.net/media/6454/blog-banner.png" class="d-block w-100" alt="Hospital Staff">
            </div>
            <div class="carousel-item">
                <img src="https://miro.medium.com/v2/resize:fit:950/1*-tjgLeReOD9WnDbSErCEqQ.jpeg" class="d-block w-100"
                    alt="Medical Equipment">
            </div>
            <div class="carousel-item">
                <img src="https://keralakaumudi.com/web-news/en/2024/08/NMAN0523332/image/thumb/hospital.1.2869042.webp"
                    class="d-block w-100" alt="Medical Equipment">
            </div>
            <div class="carousel-item">
                <img src="https://www.portea.com/static/49ef005697a36edccca4f4986f9c0059/ca537/ICU-respiratory-service-big.png"
                    class="d-block w-100" alt="Medical Equipment">
            </div>
            <div class="carousel-item">
                <img src="https://www.etkho.com/wp-content/uploads/2021/09/hospital-safety-equipment-maintenance-protocol-pic03-20210930-etkho-hospital-engineering.jpg"
                    class="d-block w-100" alt="Medical Equipment">
            </div>
            <div class="carousel-item">
                <img src="https://media.istockphoto.com/id/187248033/photo/keeping-track-of-the-patients-vitals.jpg?s=612x612&w=0&k=20&c=APSrT4dq0R0WPHNZEW5rPGzhHg2kmrPWCrROGVJDmvE="
                    class="d-block w-100" alt="Medical Equipment">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Features -->
    <div class="section bg-light" id="features">
        <div class="container">
            <h2 class="text-center">Our Features</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <h5>24/7 Emergency</h5>
                </div>
                <div class="col-md-4">
                    <h5>Specialist Doctors</h5>
                </div>
                <div class="col-md-4">
                    <h5>Modern Labs</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievements -->
    <div class="section" id="achievements">
        <div class="container text-center">
            <h2>Achievements</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush text-left mb-3">
                        <li class="list-group-item bg-transparent border-0">🏆 Recognized as the best hospital for 5 consecutive years.</li>
                        <li class="list-group-item bg-transparent border-0">👨‍⚕️ Over 10,000 successful surgeries performed.</li>
                        <li class="list-group-item bg-transparent border-0">🌍 Accredited by National and International Health Organizations.</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush text-left mb-3">
                        <li class="list-group-item bg-transparent border-0">💉 Advanced ICU and Emergency Care Facilities.</li>
                        <li class="list-group-item bg-transparent border-0">🤝 Community Health Outreach Programs.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialties -->
    <div class="section bg-light" id="specialties">
        <div class="container text-center">
            <h2>Specialties</h2>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group mb-3">
                        <li class="list-group-item">Cardiology</li>
                        <li class="list-group-item">Neurology</li>
                        <li class="list-group-item">Orthopedics</li>
                        <li class="list-group-item">Pediatrics</li>
                        <li class="list-group-item">Gastroenterology</li>
                        <li class="list-group-item">Oncology</li>
                        <li class="list-group-item">Nephrology</li>
                        <li class="list-group-item">Pulmonology</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group mb-3">
                        <li class="list-group-item">Urology</li>
                        <li class="list-group-item">Ophthalmology</li>
                        <li class="list-group-item">Dentistry</li>
                        <li class="list-group-item">Psychiatry</li>
                        <li class="list-group-item">Radiology</li>
                        <li class="list-group-item">Anesthesiology</li>
                        <li class="list-group-item">Endocrinology</li>
                        <li class="list-group-item">Dermatology</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctors -->
    <div class="section" id="doctors">
        <div class="container text-center">
            <h2>Our Doctors</h2>
            <div class="row">
                <div class="col-md-4">
                    <p>Dr. John (Cardiologist, MD, DM Cardiology)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Smith (Neurologist, MD, DM Neurology)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Ayesha (Pediatrician, MBBS, MD Pediatrics)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Priya Sharma (Orthopedic Surgeon, MBBS, MS Orthopedics)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Rahul Mehra (General Surgeon, MBBS, MS General Surgery)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Fatima Khan (Gynecologist, MBBS, MD Gynecology)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Vivek Patel (Dermatologist, MBBS, MD Dermatology)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Anjali Verma (ENT Specialist, MBBS, MS ENT)</p>
                </div>
                <div class="col-md-4">
                    <p>Dr. Sameer Gupta (Pulmonologist, MBBS, MD Pulmonology)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="section bg-light" id="contact">
        <div class="container text-center">
            <h2>Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <p><strong>Main Branch:</strong><br>
                                123 Hospital Street, City<br>
                                Email: info@myhospital.com<br>
                                Phone: 9876543210
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p><strong>East Branch:</strong><br>
                                45 Sunrise Avenue, East City<br>
                                Email: east@myhospital.com<br>
                                Phone: 9123456780
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p><strong>West Branch:</strong><br>
                                78 Sunset Road, West City<br>
                                Email: west@myhospital.com<br>
                                Phone: 9988776655
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p><strong>North Branch:</strong><br>
                                22 North Park, North City<br>
                                Email: north@myhospital.com<br>
                                Phone: 9001122334
                            </p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <p><strong>South Branch:</strong><br>
                                56 South Lane, South City<br>
                                Email: south@myhospital.com<br>
                                Phone: 9112233445
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        &copy; 2025 MyHospital. All rights reserved.
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Auto-popup script -->
    <script>
        // Show the appointment modal automatically after 30 seconds
        setTimeout(function () {
            $('#appointmentModal').modal('show');
        }, 30000);
    </script>
</body>

</html>