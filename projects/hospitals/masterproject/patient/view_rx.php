<?php
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['role'])) header("Location: login.php");

$id = $_GET['id'] ?? '';
$all_rx = getJSON(DIR_DATA . 'prescriptions.json');
$rx = findEntry($all_rx, 'id', $id);

if (!$rx) die("Prescription not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rx: <?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light py-5">
    <div class="container col-md-6">
        <div class="glass-panel p-5 bg-white position-relative">
            <div class="text-center border-bottom pb-4 mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/3004/3004458.png" width="60" class="mb-2">
                <h3 class="fw-bold text-primary mb-0">DIGITAL PRESCRIPTION</h3>
                <small class="text-muted">Rx ID: <?php echo $rx['id']; ?></small>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted text-uppercase">Doctor</small>
                    <h5 class="fw-bold">Dr. <?php echo $rx['doctor_name']; ?></h5>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted text-uppercase">Date</small>
                    <h5 class="fw-bold"><?php echo date('d M Y', strtotime($rx['date'])); ?></h5>
                </div>
            </div>

            <div class="bg-light p-3 rounded mb-4">
                <strong>Diagnosis:</strong>
                <p class="mb-0"><?php echo $rx['diagnosis']; ?></p>
            </div>

            <h5 class="fw-bold border-bottom pb-2 mb-3">Medicines</h5>
            <table class="table table-sm mb-4">
                <thead><tr><th>Name</th><th>Dose</th><th>Days</th></tr></thead>
                <tbody>
                    <?php foreach($rx['medicines'] as $med): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $med['name']; ?></td>
                        <td><?php echo $med['dose']; ?></td>
                        <td><?php echo $med['dur']; ?> Days</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if(!empty($rx['lab_requests'])): ?>
            <div class="alert alert-warning small">
                <strong><i class="fas fa-flask"></i> Lab Tests Ordered:</strong>
                <?php echo implode(', ', $rx['lab_requests']); ?>
            </div>
            <?php endif; ?>

            <div class="text-center mt-5 no-print">
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4">Print Rx</button>
                <a href="my_records.php" class="btn btn-outline-secondary rounded-pill px-4">Back</a>
            </div>
        </div>
    </div>
</body>
</html>