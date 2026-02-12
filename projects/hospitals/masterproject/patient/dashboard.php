<?php
require_once '../config.php';
require_once '../functions.php';

// Custom Check for Patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$pid = $_SESSION['user_id'];
$all_rx = getJSON(DIR_DATA . 'prescriptions.json');
$my_rx = array_filter($all_rx, function($r) use ($pid) { return $r['patient_id'] == $pid; });
$last_rx = end($my_rx); // Get most recent record for vitals

$all_visits = getJSON(FILE_VISITS);
$upcoming = array_filter($all_visits, function($v) use ($pid) {
    return $v['patient_id'] == $pid && $v['status'] == 'waiting';
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light pb-5">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top px-3 shadow">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-heartbeat me-2"></i> My Health
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li class="nav-item"><a href="my_records.php" class="nav-link">Medical Records</a></li>
                <li class="nav-item"><a href="book.php" class="nav-link">Book Appointment</a></li>
                <li class="nav-item"><a href="../logout.php" class="nav-link text-warning">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container py-4">
        <div class="glass-panel p-4 bg-white mb-4">
            <h2 class="fw-bold text-primary">Welcome, <?php echo $_SESSION['name']; ?></h2>
            <p class="text-muted mb-0">ID: <span class="badge bg-secondary"><?php echo $pid; ?></span></p>
        </div>

        <div class="row g-4">
            
            <div class="col-md-8">
                <div class="glass-panel p-4 bg-white h-100">
                    <h5 class="fw-bold mb-4"><i class="fas fa-chart-line me-2 text-danger"></i> Latest Vitals</h5>
                    <?php if($last_rx): ?>
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <h3 class="fw-bold text-dark"><?php echo $last_rx['vitals']['bp'] ?? '--'; ?></h3>
                                <small class="text-muted">BP (mmHg)</small>
                            </div>
                            <div class="col-4 border-end">
                                <h3 class="fw-bold text-dark"><?php echo $last_rx['vitals']['temp'] ?? '--'; ?></h3>
                                <small class="text-muted">Temp (°F)</small>
                            </div>
                            <div class="col-4">
                                <h3 class="fw-bold text-dark"><?php echo $last_rx['vitals']['weight'] ?? '--'; ?></h3>
                                <small class="text-muted">Weight (kg)</small>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top text-center text-muted small">
                            Recorded on: <?php echo date('d M Y', strtotime($last_rx['date'])); ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary">No vitals recorded yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="glass-panel p-4 bg-primary text-white h-100 text-center d-flex flex-column justify-content-center">
                    <h5 class="mb-3">Need a Checkup?</h5>
                    <a href="book.php" class="btn btn-light text-primary fw-bold w-100 mb-2 py-3 shadow-sm">
                        <i class="fas fa-calendar-plus me-2"></i> Book Now
                    </a>
                </div>
            </div>

            <div class="col-12">
                <h5 class="fw-bold text-secondary mb-3">Upcoming Appointments</h5>
                <?php if(empty($upcoming)): ?>
                    <div class="glass-panel p-4 bg-white text-center text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3 opacity-25"></i>
                        <p>No upcoming appointments.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach($upcoming as $app): 
                            $users = getJSON(FILE_USERS);
                            $doc = findEntry($users, 'id', $app['doctor_id']);
                        ?>
                        <div class="col-md-6">
                            <div class="glass-panel p-3 bg-white border-start border-5 border-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold text-dark">Dr. <?php echo $doc['name']; ?></h5>
                                        <small class="text-muted"><?php echo $doc['dept']; ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning text-dark mb-1">Waiting</span>
                                        <br><small><?php echo date('d M, h:i A', strtotime($app['visit_date'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>
</html>