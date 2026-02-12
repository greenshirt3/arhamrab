<?php
require_once __DIR__ . '/../auth_check.php';
requireRole(['admin', 'doctor']);

$history = []; $patient = null;
if (isset($_GET['search_id'])) {
    $patient = findEntry(getJSON(FILE_PATIENTS), 'id', $_GET['search_id']);
    if ($patient) {
        $history = array_filter(getJSON(DIR_DATA . 'prescriptions.json'), function($rx) use ($patient) {
            return $rx['patient_id'] == $patient['id'];
        });
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light p-5">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold">Medical Records</h2>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </div>
        
        <div class="glass-panel p-4 bg-white mb-4">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search_id" class="form-control form-control-lg smart-search" 
                       data-type="patient" placeholder="Type Patient Name or ID..." autocomplete="off" 
                       value="<?php echo $_GET['search_id'] ?? ''; ?>" required>
                <button class="btn btn-primary px-4">Search</button>
            </form>
        </div>

        <?php if($patient): ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="glass-panel p-4 bg-white">
                        <h4 class="fw-bold text-primary"><?php echo $patient['name']; ?></h4>
                        <p class="text-muted">ID: <?php echo $patient['id']; ?></p>
                        <p>Age: <?php echo date_diff(date_create($patient['dob']), date_create('today'))->y; ?> Yrs</p>
                    </div>
                </div>
                <div class="col-md-8">
                    <h5 class="mb-3">Visit Timeline</h5>
                    <?php if(empty($history)): ?><div class="alert alert-warning">No history found.</div><?php else: ?>
                    <div class="list-group">
                        <?php foreach(array_reverse($history) as $r): ?>
                        <div class="list-group-item p-4 mb-3 border rounded shadow-sm">
                            <div class="d-flex justify-content-between"><h5 class="text-primary">Dr. <?php echo $r['doctor_name']; ?></h5><small><?php echo $r['date']; ?></small></div>
                            <p><strong>Diagnosis:</strong> <?php echo $r['diagnosis']; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="../assets/smart-search.js"></script>
</body>
</html>