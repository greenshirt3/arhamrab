<?php
require_once '../auth_check.php';
requireRole(['admin', 'lab']);

$lab_queue = getJSON(FILE_LAB);
$pending = array_filter($lab_queue, function($t) { return $t['status'] == 'pending'; });
$completed = array_filter($lab_queue, function($t) { return $t['status'] == 'completed'; });
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lab Dashboard | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3 d-flex flex-column flex-shrink-0" style="width: 280px;">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <i class="fas fa-microscope fa-2x me-2 text-warning"></i>
                <span class="fs-4 fw-bold">Pathology</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active bg-warning text-dark"><i class="fas fa-list me-2"></i> Work Queue</a></li>
                <li><a href="radiology.php" class="nav-link text-white"><i class="fas fa-x-ray me-2"></i> Radiology Upload</a></li>
                <li><a href="#" class="nav-link text-white"><i class="fas fa-boxes me-2"></i> Inventory</a></li>
            </ul>
            <hr>
            <a href="../logout.php" class="text-white text-decoration-none"><i class="fas fa-sign-out-alt me-2"></i> Sign Out</a>
        </div>

        <div class="flex-grow-1 p-5 overflow-auto" style="height: 100vh;">
            <h2 class="fw-bold mb-4">Laboratory Work Queue</h2>
            
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="glass-panel p-4 bg-white border-start border-5 border-warning">
                        <h6 class="text-muted">PENDING TESTS</h6>
                        <h2 class="fw-bold"><?php echo count($pending); ?></h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="glass-panel p-4 bg-white border-start border-5 border-success">
                        <h6 class="text-muted">COMPLETED TODAY</h6>
                        <h2 class="fw-bold"><?php echo count($completed); ?></h2>
                    </div>
                </div>
            </div>

            <div class="glass-panel p-4 bg-white">
                <h4 class="fw-bold mb-3">Incoming Requests</h4>
                <?php if(empty($pending)): ?>
                    <div class="alert alert-success">No pending tests. All caught up!</div>
                <?php else: ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Lab ID</th>
                                <th>Patient Name</th>
                                <th>Test Requested</th>
                                <th>Request Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pending as $test): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo $test['id']; ?></span></td>
                                <td class="fw-bold"><?php echo $test['patient_name']; ?></td>
                                <td>
                                    <?php if(str_contains($test['test_name'], 'X-RAY')): ?>
                                        <i class="fas fa-radiation text-danger me-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-vial text-primary me-1"></i>
                                    <?php endif; ?>
                                    <?php echo $test['test_name']; ?>
                                </td>
                                <td><?php echo date('H:i', strtotime($test['timestamp'])); ?></td>
                                <td>
                                    <a href="perform_test.php?id=<?php echo $test['id']; ?>" class="btn btn-warning btn-sm fw-bold">
                                        <i class="fas fa-flask me-1"></i> Perform Test
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="mt-5">
                <h5 class="text-muted">Recently Completed</h5>
                <table class="table table-sm text-muted">
                    <?php foreach(array_slice($completed, 0, 5) as $c): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td><?php echo $c['patient_name']; ?></td>
                            <td><?php echo $c['test_name']; ?></td>
                            <td><a href="print_report.php?id=<?php echo $c['id']; ?>" target="_blank" class="text-primary"><i class="fas fa-print"></i> Print</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>