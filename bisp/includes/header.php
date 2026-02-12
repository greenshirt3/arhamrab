<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arham Printers - Portal</title>
    
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2910/2910768.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <link rel="stylesheet" href="css/modern.css">
    <link rel="manifest" href="manifest.json">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-size: .875rem; background-color: #f8f9fa; }
        
        /* Sidebar Styling */
        .sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; padding: 48px 0 0; box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1); background-color: #fff; }
        
        /* Mobile Sidebar */
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 50px; bottom: 0; left: 0; z-index: 1000; padding: 20px 0; width: 100%; height: auto; background-color: white; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
            .sidebar.collapse:not(.show) { display: none !important; }
        }

        .nav-link { font-weight: 500; color: #333; padding: 10px 15px; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover { color: #007bff; background-color: #f0f8ff; border-right: 3px solid #007bff; }
        .navbar-brand { padding-top: .75rem; padding-bottom: .75rem; font-size: 1rem; background-color: rgba(0, 0, 0, .25); box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25); }
        
        /* Print Hide */
        @media print { .sidebar, .navbar, .btn, .no-print { display: none !important; } main { margin: 0 !important; width: 100% !important; } }
    </style>
</head>
<body>
    
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fw-bold" href="dashboard.php">
            ARHAM <span class="text-warning">Printers</span>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav w-100 d-none d-md-block">
            <div class="nav-item text-nowrap d-flex align-items-center justify-content-end px-3">
                <span class="badge bg-secondary me-3"><?php echo isset($_SESSION['role']) ? strtoupper($_SESSION['role']) : 'USER'; ?></span>
                <a class="nav-link px-3 text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="navbar-nav d-md-none px-3">
             <a class="nav-link text-white small" href="logout.php">Logout</a>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    
                    <h6 class="sidebar-heading px-3 mt-2 mb-1 text-muted">Operations</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="queue.php"><i class="fas fa-ticket-alt"></i> Token Queue</a></li>
                        <li class="nav-item"><a class="nav-link" href="beneficiaries.php"><i class="fas fa-users"></i> Beneficiaries</a></li>
                        
                        <li class="nav-item"><a class="nav-link" href="bills.php"><i class="fas fa-qrcode"></i> Bill Scanner</a></li>
                        <li class="nav-item"><a class="nav-link" href="night_mode.php"><i class="fas fa-moon"></i> Night Shift</a></li>
                    </ul>

                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase text-danger">Financials</h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item"><a class="nav-link" href="payout.php"><i class="fas fa-hand-holding-usd"></i> Payout Entry</a></li>
                        <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                        <li class="nav-item"><a class="nav-link text-danger" href="reconcile.php"><i class="fas fa-balance-scale"></i> Cash Closing</a></li>
                    </ul>

                    <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted text-uppercase text-primary">System</h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item"><a class="nav-link" href="admin_settings.php"><i class="fas fa-broadcast-tower"></i> Public Portal</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-user-cog"></i> User Manager</a></li>
                    </ul>
                    <?php endif; ?>
                    
                    <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">Display</h6>
                    <ul class="nav flex-column mb-2">
                         <li class="nav-item"><a class="nav-link" href="display.php" target="_blank"><i class="fas fa-tv"></i> TV Mode</a></li>
                    </ul>

                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">