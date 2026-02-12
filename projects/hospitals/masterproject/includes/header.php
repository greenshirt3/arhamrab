<?php require_once __DIR__ . '/../config.php'; ?>
<?php require_once __DIR__ . '/../functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        .navbar-glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        .hero-gradient { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); }
    </style>
</head>
<body>

<div class="bg-dark text-white py-2 text-small text-xs">
    <div class="container d-flex justify-content-between align-items-center">
        <small><i class="fas fa-phone-alt me-2 text-info"></i> <?php echo HOSPITAL_PHONE; ?></small>
        <small class="d-none d-md-block"><i class="fas fa-map-marker-alt me-2 text-info"></i> <?php echo HOSPITAL_ADDRESS; ?></small>
        <div>
            <a href="doctor/login.php" class="text-white text-decoration-none ms-3"><i class="fas fa-user-md me-1"></i> Staff Login</a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bolder text-primary fs-3" href="index.php">
            <i class="fas fa-heartbeat me-2"></i>CITY<span class="text-dark">HOSPITAL</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="fas fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto fw-semibold align-items-center">
                <li class="nav-item"><a class="nav-link px-3" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="departments.php">Departments</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 text-primary" href="#" id="portalDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-lock me-1"></i> Login Portals
                    </a>
                    <ul class="dropdown-menu border-0 shadow-lg p-3 fade-in">
                        <li><h6 class="dropdown-header text-uppercase small text-muted fw-bold">For Patients</h6></li>
                        <li><a class="dropdown-item rounded py-2" href="patient/login.php"><i class="fas fa-user-injured me-2 text-primary"></i> Patient Portal</a></li>
                        <li><a class="dropdown-item rounded py-2" href="check_reports.php"><i class="fas fa-file-medical-alt me-2 text-info"></i> View Lab Reports</a></li>
                        
                        <li><hr class="dropdown-divider"></li>
                        
                        <li><h6 class="dropdown-header text-uppercase small text-muted fw-bold">For Hospital Staff</h6></li>
                        <li><a class="dropdown-item rounded py-2" href="doctor/login.php"><i class="fas fa-user-md me-2 text-danger"></i> Staff Login</a></li>
                    </ul>
                </li>
                
                <li class="nav-item"><a class="nav-link px-3" href="contact.php">Contact</a></li>
            </ul>
            
            <a href="patient/book.php" class="btn btn-primary rounded-pill px-4 ms-3 shadow-sm">
                Book Appointment
            </a>
        </div>
    </div>
</nav>