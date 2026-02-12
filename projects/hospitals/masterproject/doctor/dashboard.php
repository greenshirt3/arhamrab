<?php
require_once '../auth_check.php';
requireRole(['admin', 'doctor']);

$doctor_id = $_SESSION['user_id'];
$visits = getJSON(FILE_VISITS);
$patients = getJSON(FILE_PATIENTS);

// Filter: Visits for this doctor that are 'waiting'
$my_queue = array_filter($visits, function($v) use ($doctor_id) {
    return $v['doctor_id'] == $doctor_id && $v['status'] == 'waiting';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold"><i class="fas fa-stethoscope me-2 text-info"></i> Dr. <?php echo $_SESSION['name']; ?></a>
        <div class="d-flex gap-3">
            <a href="patient_history.php" class="btn btn-outline-light btn-sm">Patient History</a>
            <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold">My Waiting Room</h2>
                <p class="text-muted">You have <strong><?php echo count($my_queue); ?></strong> patients waiting.</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="glass-panel p-3 d-inline-block bg-white text-success">
                    <small>OPD STATUS</small><br>
                    <strong><i class="fas fa-circle fa-xs me-1"></i> ONLINE</strong>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php if(empty($my_queue)): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-coffee fa-4x text-muted mb-3"></i>
                    <h4>No patients in queue.</h4>
                    <p>Enjoy your break, Doctor!</p>
                </div>
            <?php else: ?>
                <?php foreach($my_queue as $visit): 
                    $p = findEntry($patients, 'id', $visit['patient_id']);
                ?>
                <div class="col-md-4">
                    <div class="glass-panel p-4 bg-white hover-3d position-relative overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold mb-0"><?php echo $p['name']; ?></h5>
                            <span class="badge bg-warning text-dark">Waiting</span>
                        </div>
                        <p class="text-muted small mb-1">ID: <?php echo $p['id']; ?></p>
                        <p class="text-muted small mb-4">Gender: <?php echo $p['gender']; ?> | Age: <?php echo date_diff(date_create($p['dob']), date_create('today'))->y; ?> Yrs</p>
                        
                        <div class="d-grid">
                            <a href="examine.php?visit_id=<?php echo $visit['id']; ?>" class="btn btn-primary fw-bold">
                                <i class="fas fa-user-md me-2"></i> Start Checkup
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>