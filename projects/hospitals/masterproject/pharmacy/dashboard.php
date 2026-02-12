<?php
require_once '../auth_check.php';
requireRole(['admin', 'pharmacy']);

$inventory = getJSON(FILE_INVENTORY);
$prescriptions = getJSON(DIR_DATA . 'prescriptions.json');

// Stats Logic
$low_stock = array_filter($inventory, function($i) { return $i['stock_qty'] < 10; });
$today_rx = array_filter($prescriptions, function($p) { 
    return substr($p['date'], 0, 10) == date('Y-m-d'); 
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pharmacy Dashboard | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column flex-shrink-0" style="width: 280px;">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="fas fa-pills fa-2x me-2 text-success"></i>
                <span class="fs-4 fw-bold">Pharmacy</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active bg-success text-white"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                <li><a href="pos.php" class="nav-link text-white"><i class="fas fa-cash-register me-2"></i> POS / Billing</a></li>
                <li><a href="inventory.php" class="nav-link text-white"><i class="fas fa-boxes me-2"></i> Inventory</a></li>
            </ul>
            <hr>
            <a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
        </div>

        <div class="flex-grow-1 p-5 overflow-auto" style="height: 100vh;">
            <h2 class="fw-bold mb-4">Pharmacy Overview</h2>
            
            <?php if(!empty($low_stock)): ?>
            <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>Action Required:</strong> <?php echo count($low_stock); ?> items are running low on stock. 
                    <a href="inventory.php" class="alert-link">Check Inventory</a>.
                </div>
            </div>
            <?php endif; ?>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="glass-panel p-4 bg-white text-success">
                        <h6 class="text-muted text-uppercase">Total Medicines</h6>
                        <h2 class="fw-bold"><?php echo count($inventory); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 bg-white text-primary">
                        <h6 class="text-muted text-uppercase">Rx Received Today</h6>
                        <h2 class="fw-bold"><?php echo count($today_rx); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-panel p-4 bg-white text-danger">
                        <h6 class="text-muted text-uppercase">Critical Stock</h6>
                        <h2 class="fw-bold"><?php echo count($low_stock); ?></h2>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4 bg-white">
                <h4 class="fw-bold mb-3">Live Prescription Feed</h4>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Rx ID</th>
                            <th>Doctor</th>
                            <th>Patient ID</th>
                            <th>Prescribed Meds</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice(array_reverse($prescriptions), 0, 10) as $p): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?php echo $p['id']; ?></span></td>
                            <td>Dr. <?php echo $p['doctor_name']; ?></td>
                            <td><?php echo $p['patient_id']; ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php 
                                        $meds = array_map(function($m){ return $m['name']; }, $p['medicines']);
                                        echo implode(', ', array_slice($meds, 0, 2)) . (count($meds)>2 ? '...' : ''); 
                                    ?>
                                </small>
                            </td>
                            <td>
                                <a href="pos.php?rx_id=<?php echo $p['id']; ?>" class="btn btn-success btn-sm fw-bold">
                                    <i class="fas fa-shopping-cart me-1"></i> Load in POS
                                </a>
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