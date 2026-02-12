<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $info['name']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<?php include __DIR__ . '/../../seo.php'; ?>

    <style>
        :root {
            --primary: #007bff; /* Trust Blue */
            --secondary: #002b5c; /* Deep Navy */
            --accent: #20c997; /* Medical Green */
            --light: #f8f9fa;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6f9; }
        
        /* Top Bar */
        .top-bar { background: var(--secondary); color: white; padding: 8px 0; font-size: 0.9rem; }
        
        /* Navbar */
        .navbar { background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 15px 0; }
        .nav-link { font-weight: 600; color: var(--secondary) !important; margin: 0 10px; }
        .nav-link:hover { color: var(--primary) !important; }
        .btn-portal { background: var(--accent); color: white; border-radius: 50px; padding: 10px 25px; font-weight: 700; border: none; }
        .btn-portal:hover { background: #19a17a; color: white; }

        /* Card Styles */
        .doc-card, .dept-card { background: white; border: none; border-radius: 15px; overflow: hidden; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .doc-card:hover, .dept-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        /* Footer */
        .footer { background: var(--secondary); color: white; padding-top: 60px; margin-top: 80px; }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div><i class="fa fa-phone me-2"></i>Emergency: <?php echo $info['emergency']; ?></div>
            <div class="d-none d-md-block"><i class="fa fa-clock me-2"></i>OPD Timings: Mon-Sat, 9:00 AM - 9:00 PM</div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary fs-3" href="index.php">
                <i class="fa fa-hospital-alt me-2"></i>CITY<span class="text-dark">HOSPITAL</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="doctors.php">Find Doctor</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointment.php">Book Appointment</a></li>
                    <li class="nav-item ms-3">
                        <a href="lab_portal.php" class="btn-portal"><i class="fa fa-file-medical-alt me-2"></i>Lab Reports</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>