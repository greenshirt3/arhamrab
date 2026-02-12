<?php
require_once __DIR__ . '/../auth_check.php';
requireRole(['admin', 'reception']);

$users = getJSON(FILE_USERS);
$doctors = array_filter($users, function($u) { return $u['role'] == 'doctor'; });

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visits = getJSON(FILE_VISITS);
    $visit = [
        'id' => generateID('OPD'),
        'patient_id' => $_POST['patient_id'], // Now comes from Smart Search
        'doctor_id' => $_POST['doctor_id'],
        'department' => 'OPD',
        'status' => 'waiting',
        'visit_date' => date('Y-m-d H:i:s')
    ];
    saveJSON(FILE_VISITS, array_merge($visits, [$visit]));
    header("Location: billing.php?visit_id=" . $visit['id']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light p-5">
    <div class="container">
        <div class="glass-panel p-5 bg-white">
            <h2 class="mb-4">Book OPD Appointment</h2>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Patient Search</label>
                    <input type="text" name="patient_id" class="form-control smart-search" 
                           data-type="patient" placeholder="Type Name, Phone or ID..." autocomplete="off" required>
                    <small class="text-muted">Select from the dropdown list to auto-fill ID.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Select Specialist</label>
                    <select name="doctor_id" class="form-select" required>
                        <option value="">Choose Doctor...</option>
                        <?php foreach($doctors as $d): ?>
                            <option value="<?php echo $d['id']; ?>">
                                <?php echo $d['name']; ?> (<?php echo $d['dept']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-success w-100 py-3 fw-bold">Book & Proceed to Billing</button>
            </form>
        </div>
    </div>
    <script src="../assets/smart-search.js"></script>
</body>
</html>