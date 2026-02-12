<?php
require_once '../config.php';
require_once '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') header("Location: login.php");

$pid = $_SESSION['user_id'];
$prescriptions = array_filter(getJSON(DIR_DATA . 'prescriptions.json'), function($r) use ($pid) { return $r['patient_id'] == $pid; });
$lab_reports = array_filter(getJSON(FILE_LAB), function($l) use ($pid) { 
    // Lab file stores name, not ID in this simple version, but let's assume we link via name or improve logic
    // For this demo, let's filter via ID if available or Name match
    return ($l['patient_name'] == $_SESSION['name']); 
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Medical Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light">
    
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold">Medical History</h2>
            <a href="dashboard.php" class="btn btn-outline-dark">Back</a>
        </div>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#rx">Prescriptions</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lab">Lab Reports</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            
            <div class="tab-pane fade show active" id="rx">
                <?php if(empty($prescriptions)): ?>
                    <div class="alert alert-info">No prescriptions found.</div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach(array_reverse($prescriptions) as $rx): ?>
                        <div class="col-md-6">
                            <div class="glass-panel p-4 bg-white hover-3d">
                                <div class="d-flex justify-content-between mb-2">
                                    <h5 class="fw-bold text-primary">Dr. <?php echo $rx['doctor_name']; ?></h5>
                                    <small class="text-muted"><?php echo date('d M Y', strtotime($rx['date'])); ?></small>
                                </div>
                                <p class="fst-italic text-dark mb-3">"<?php echo $rx['diagnosis']; ?>"</p>
                                <a href="view_rx.php?id=<?php echo $rx['id']; ?>" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="lab">
                <?php if(empty($lab_reports)): ?>
                    <div class="alert alert-info">No lab reports found.</div>
                <?php else: ?>
                    <table class="table table-hover bg-white rounded shadow-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Test Name</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($lab_reports as $lab): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $lab['test_name']; ?></td>
                                <td><?php echo date('d M Y', strtotime($lab['timestamp'] ?? 'now')); ?></td>
                                <td>
                                    <?php if($lab['status']=='completed'): ?>
                                        <span class="badge bg-success">Ready</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Processing</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($lab['status']=='completed'): ?>
                                        <a href="../lab/print_report.php?id=<?php echo $lab['id']; ?>" target="_blank" class="btn btn-sm btn-dark"><i class="fas fa-download"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>