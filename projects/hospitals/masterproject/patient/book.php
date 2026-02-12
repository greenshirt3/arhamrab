<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if (!isset($_SESSION['role'])) header("Location: login.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visits = getJSON(FILE_VISITS);
    $visit = [
        'id' => generateID('OPD'),
        'patient_id' => $_SESSION['user_id'],
        'doctor_id' => $_POST['doctor_id'], // Now comes from Smart Search value
        'department' => 'OPD',
        'status' => 'waiting',
        'visit_date' => date('Y-m-d H:i:s')
    ];
    saveJSON(FILE_VISITS, array_merge($visits, [$visit]));
    header("Location: dashboard.php?booked=1");
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
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="glass-panel p-5 bg-white shadow-lg col-md-5">
        <h3 class="fw-bold text-center mb-4">Book Appointment</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Search Specialist</label>
                <input type="text" name="doctor_id" class="form-control smart-search" 
                       data-type="doctor" placeholder="Type Dr. Name..." autocomplete="off" required>
                <small class="text-muted">Select from the list to confirm.</small>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Preferred Date</label>
                <input type="date" class="form-control form-control-lg" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <button class="btn btn-primary w-100 btn-lg fw-bold rounded-pill">Confirm Booking</button>
            <a href="dashboard.php" class="btn btn-link w-100 mt-2">Cancel</a>
        </form>
    </div>
    <script src="../assets/smart-search.js"></script>
</body>
</html>