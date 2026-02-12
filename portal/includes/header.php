<?php
// includes/header.php
require_once __DIR__ . '/config.php';

// Security Gatekeeper
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit();
}

function isActive($page) { return basename($_SERVER['PHP_SELF']) == $page ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARHAM ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/modern.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { min-height: 100vh; background: #212529; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; font-size: 0.95rem; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-left: 4px solid #0d6efd; }
        .glass-panel { background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #eee; }
        /* Mobile Toggle */
        @media (max-width: 768px) { .sidebar { min-height: auto; } }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top p-0 px-3 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0" href="dashboard.php">ARHAM ERP</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type=\"button\" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="text-white small d-none d-md-block">
        Logged in as: <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>
        <a href="logout.php" class="btn btn-sm btn-outline-danger ms-3">Logout</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse pt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('dashboard.php'); ?>" href="dashboard.php">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                
                <?php if(has_perm('bisp')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('bisp.php'); ?>" href="bisp.php">
                        <i class="fas fa-hand-holding-usd me-2"></i> BISP Portal
                    </a>
                </li>
                <?php endif; ?>

                <?php if(has_perm('hbl')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('bills.php'); ?>" href="bills.php">
                        <i class="fas fa-file-invoice me-2"></i> Add Bills
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('night_mode.php'); ?>" href="night_mode.php">
                        <i class="fas fa-moon me-2"></i> Night Mode
                    </a>
                </li>
                <?php endif; ?>

                <?php if(has_perm('shop')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('shop_pos.php'); ?>" href="shop_pos.php">
                        <i class="fas fa-cash-register me-2"></i> Shop POS
                    </a>
                </li>
                <?php endif; ?>

                <?php if(has_perm('loans')): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('loans.php'); ?>" href="loans.php">
                        <i class="fas fa-book me-2"></i> Ledger
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(has_perm('admin')): ?>
                <li class="nav-item mt-3"><span class="text-muted small ps-3 fw-bold">ADMINISTRATION</span></li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('accounts.php'); ?>" href="accounts.php">
                        <i class="fas fa-wallet me-2"></i> Accounts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('system_settings.php'); ?>" href="system_settings.php">
                        <i class="fas fa-cogs me-2"></i> Settings
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item d-md-none mt-3 border-top pt-2">
                     <a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">