<?php
require_once '../auth_check.php';
requireRole(['admin', 'reception']);

// LOAD DATA FOR STATS
$patients = getJSON(FILE_PATIENTS);
$visits = getJSON(FILE_VISITS);
$today_visits = array_filter($visits, function($v) {
    return substr($v['visit_date'], 0, 10) == date('Y-m-d');
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reception Portal | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column flex-shrink-0" style="width: 280px;">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="fas fa-hospital-user fa-2x me-2 text-primary"></i>
                <span class="fs-4 fw-bold">Reception</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a></li>
                <li><a href="registration.php" class="nav-link text-white"><i class="fas fa-user-plus me-2"></i> New Patient</a></li>
                <li><a href="appointments.php" class="nav-link text-white"><i class="fas fa-calendar-check me-2"></i> Appointments</a></li>
                <li><a href="billing.php" class="nav-link text-white"><i class="fas fa-file-invoice-dollar me-2"></i> Billing</a></li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="../logout.php" class="d-flex align-items-center text-white text-decoration-none">
                    <i class="fas fa-sign-out-alt me-2"></i> <strong>Sign Out</strong>
                </a>
            </div>
        </div>

        <div class="flex-grow-1 p-5 overflow-auto" style="height: 100vh;">
            <h2 class="fw-bold mb-4">Dashboard Overview</h2>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="glass-panel p-4 d-flex justify-content-between align-items-center bg-white text-primary">
                        <div>
                            <h6 class="text-muted text-uppercase">Total Patients</h6>
                            <h2 class="fw-bold mb-0"><?php echo count($patients); ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 d-flex justify-content-between align-items-center bg-white text-success">
                        <div>
                            <h6 class="text-muted text-uppercase">Today's Visits</h6>
                            <h2 class="fw-bold mb-0"><?php echo count($today_visits); ?></h2>
                        </div>
                        <i class="fas fa-notes-medical fa-3x opacity-25"></i>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 d-flex justify-content-between align-items-center bg-white text-warning">
                        <div>
                            <h6 class="text-muted text-uppercase">Doctors Active</h6>
                            <h2 class="fw-bold mb-0">8</h2> </div>
                        <i class="fas fa-user-md fa-3x opacity-25"></i>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4 bg-white">
                <div class="d-flex justify-content-between mb-3">
                    <h4 class="fw-bold">Recent Patients</h4>
                    <a href="registration.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New</a>
                </div>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recent = array_slice(array_reverse($patients), 0, 5);
                        foreach($recent as $p): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo $p['id']; ?></span></td>
                            <td class="fw-bold"><?php echo $p['name']; ?></td>
                            <td><?php echo $p['phone']; ?></td>
                            <td><?php echo date('M d, H:i', strtotime($p['created_at'])); ?></td>
                            <td>
                                <a href="print_card.php?id=<?php echo $p['id']; ?>" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-id-card"></i> Card</a>
                                <a href="appointments.php?pid=<?php echo $p['id']; ?>" class="btn btn-outline-primary btn-sm">Book</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>