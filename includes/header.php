<?php
// Get the current page name (e.g., 'index.php', 'products.php')
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Arham Printers'; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Printing Services, Business Cards, Wedding Cards" name="keywords">
    <meta content="Professional printing services in Pakistan." name="description">

    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <div class="container-fluid bg-dark text-white-50 py-2 px-0 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="fa fa-phone-alt me-2"></small>
                    <small>+92 300 6238233</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="far fa-envelope-open me-2"></small>
                    <small>info@arhamprinters.pk</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center">
                    <a class="text-white-50 ms-4" href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a class="text-white-50 ms-4" href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5">
        <a href="index.php" class="navbar-brand d-flex align-items-center me-4">
            <img src="img/logo2.webp" alt="Arham Printers" style="height: 40px; margin-right: 10px;">
        </a>

        <div class="collapse navbar-collapse flex-grow-1" id="navbarCollapse">
            <div class="navbar-nav me-auto bg-light pe-4 py-3 py-lg-0">
                <a href="index.php" class="nav-item nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="products.php" class="nav-item nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">Products</a>
                <a href="wedding.php" class="nav-item nav-link <?php echo ($current_page == 'wedding.php') ? 'active' : ''; ?>">Wedding Cards</a>
                <a href="prints.php" class="nav-item nav-link <?php echo ($current_page == 'prints.php') ? 'active' : ''; ?>">Printing</a>
            </div>
            
            <div class="h-100 d-lg-inline-flex align-items-center">
                <a class="btn-sm-square bg-white text-dark rounded-circle me-4 position-relative" href="products.php#cart-section" onclick="showSection('cart')">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <span id="desktop-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.7em;">0</span>
                </a>
            </div>
        </div>
    </nav>
    <nav id="mobile-bottom-nav" class="d-lg-none">
        <a href="index.php" class="mobile-nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> <span>Home</span>
        </a>
        <a href="products.php" class="mobile-nav-link <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
            <i class="fas fa-th-list"></i> <span>Shop</span>
        </a>
        <a href="wedding.php" class="mobile-nav-link <?php echo ($current_page == 'wedding.php') ? 'active' : ''; ?>">
            <i class="fas fa-gift"></i> <span>Wedding</span>
        </a>
        <a href="products.php#cart-section" class="mobile-nav-link" onclick="showSection('cart')">
            <i class="fas fa-shopping-cart"></i> <span>Cart</span>
            <span id="cart-count" class="badge bg-danger rounded-circle" style="position: absolute; top: 5px; right: 25px; font-size: 10px;">0</span>
        </a>
    </nav>