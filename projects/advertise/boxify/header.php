<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $info['name']; ?> | Custom Box Manufacturers</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;600;800&family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<?php include '../../../seo/seo.php'; ?>
<?php include '../../../secure.php'; ?>

    <style>
        :root {
            --brand-orange: #FF6B35; /* Call to Action */
            --brand-navy: #004E89;    /* Trust/Corporate */
            --brand-light: #F7F7F7;
            --brand-kraft: #E0C097;   /* Cardboard Color */
        }
        
        body { font-family: 'Rubik', sans-serif; background-color: var(--brand-light); color: #333; overflow-x: hidden; }
        
        h1, h2, h3, .brand-font { font-family: 'Kanit', sans-serif; text-transform: uppercase; }
        
        /* Navbar */
        .top-bar { background: var(--brand-navy); color: white; padding: 10px 0; font-size: 0.9rem; }
        .navbar { background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 15px 0; }
        .navbar-brand { font-size: 2rem; font-weight: 800; color: var(--brand-navy) !important; letter-spacing: -1px; }
        .navbar-brand span { color: var(--brand-orange); }
        
        .nav-link { font-weight: 600; color: #555 !important; margin: 0 10px; transition: 0.3s; }
        .nav-link:hover { color: var(--brand-orange) !important; }
        .btn-quote { background: var(--brand-orange); color: white; font-weight: 700; padding: 10px 30px; border-radius: 5px; border: none; clip-path: polygon(10% 0, 100% 0, 100% 100%, 0% 100%); }
        .btn-quote:hover { background: var(--brand-navy); color: white; }

        /* Arham Badge */
        .arham-badge { background: #f0f0f0; border: 1px solid #ddd; padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: #666; display: inline-block; }

        /* Card 3D Effect */
        .box-card { background: white; border: none; border-radius: 15px; overflow: hidden; transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.05); height: 100%; }
        .box-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .box-img-wrap { overflow: hidden; height: 250px; }
        .box-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .box-card:hover img { transform: scale(1.1); }
        
        /* Footer */
        .footer { background: var(--brand-navy); color: white; padding: 60px 0; border-top: 5px solid var(--brand-orange); }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div><span class="arham-badge"><i class="fa fa-check-circle text-success me-1"></i> A Joint Venture by Boxify FS & Arham Printers</span></div>
            <div>
                <a href="mailto:<?php echo $info['email']; ?>" class="text-white text-decoration-none me-3"><i class="fa fa-envelope me-1"></i> <?php echo $info['email']; ?></a>
                <span class="fw-bold"><i class="fab fa-whatsapp me-1"></i> <?php echo $info['phone']; ?></span>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa fa-cube me-2 text-warning"></i><?php echo $info['logo_text']; ?><span><?php echo $info['logo_accent']; ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="fa fa-bars text-dark"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#products">Boxes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#process">Process</a></li>
                    <li class="nav-item"><a class="nav-link" href="#calculator">Price Calculator</a></li>
                    <li class="nav-item ms-3">
                        <a href="#calculator" class="btn-quote">Get Custom Quote</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>