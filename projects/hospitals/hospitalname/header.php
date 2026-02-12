<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $info['name']; ?> | <?php echo $info['tagline']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<?php include __DIR__ . '/../../seo.php'; ?>

    <style>
        /* DYNAMIC THEME FROM JSON */
        :root {
            --primary: <?php echo $theme['primary_color']; ?>;
            --secondary: <?php echo $theme['secondary_color']; ?>;
            --light: #F9F9F9;
            --dark: #1D2A4D;
        }
        
        body { font-family: <?php echo $theme['font_family']; ?>; color: #555; }
        
        /* Top Bar */
        .top-bar { background: var(--secondary); color: #fff; padding: 10px 0; font-size: 14px; }
        .top-bar a { color: #fff; text-decoration: none; }
        
        /* Navbar */
        .navbar { box-shadow: 0 2px 15px rgba(0,0,0,0.1); background: #fff; padding: 15px 0; }
        .navbar-brand h1 { color: var(--secondary); font-weight: 700; margin: 0; font-size: 24px; }
        .nav-link { color: var(--secondary); font-weight: 500; padding: 10px 15px !important; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--primary); }
        .btn-primary { background: var(--primary); border: none; padding: 10px 30px; border-radius: 5px; color: #fff; }
        .btn-primary:hover { background: var(--secondary); color: #fff; }

        /* Footer & Service Styles */
        .hero-header {
            background: linear-gradient(rgba(29, 42, 77, 0.8), rgba(29, 42, 77, 0.8)), url('<?php echo $hospital['hero_section']['image']; ?>');
            background-size: cover; background-position: center; height: 80vh; display: flex; align-items: center;
        }
        .service-item { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 45px rgba(0,0,0,0.08); transition: .5s; height: 100%; }
        .service-item:hover { background: var(--primary); color: #fff; transform: translateY(-10px); }
        .service-item:hover .service-icon { background: #fff; color: var(--primary); }
        .service-item:hover p { color: #f0f0f0; }
        .service-icon { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: var(--primary); color: #fff; border-radius: 50px; font-size: 25px; margin-bottom: 20px; transition: .5s; }
        .footer { background: var(--secondary); color: #fff; padding-top: 60px; }
        .footer a { color: #fff; text-decoration: none; transition: 0.3s; }
        .footer a:hover { color: var(--primary); padding-left: 5px; }
    </style>
</head>

<body>
    <div class="top-bar d-none d-md-block">
        <div class="container d-flex justify-content-between">
            <div>
                <a href="mailto:<?php echo $info['email']; ?>"><i class="fa fa-envelope me-2"></i><?php echo $info['email']; ?></a>
            </div>
            <div>
                <span class="me-3"><i class="fa fa-clock me-2"></i><?php echo $info['emergency_text']; ?></span>
                <a href="tel:<?php echo $info['phone']; ?>"><i class="fa fa-phone-alt me-2"></i><?php echo $info['phone']; ?></a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <h1><i class="fa fa-heartbeat text-primary me-3"></i><?php echo $info['name']; ?></h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="doctors.php" class="nav-item nav-link">Doctors</a>
                    <a href="https://wa.me/<?php echo $info['whatsapp_clean']; ?>" target="_blank" class="nav-item nav-link">Contact</a>
                </div>
                <a href="appointment.php" class="btn btn-primary ms-3">Book Appointment <i class="fab fa-whatsapp ms-2"></i></a>
            </div>
        </div>
    </nav>