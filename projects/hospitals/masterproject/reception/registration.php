<?php
require_once '../auth_check.php';
requireRole(['admin', 'reception']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patients = getJSON(FILE_PATIENTS);
    
    $new_id = generateID('PAT');
    $new_patient = [
        'id' => $new_id,
        'name' => $_POST['name'],
        'dob' => $_POST['dob'], // Date of Birth
        'gender' => $_POST['gender'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $patients[] = $new_patient;
    saveJSON(FILE_PATIENTS, $patients);
    
    // Redirect to Print Card
    header("Location: print_card.php?id=" . $new_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Patient | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">

    <div class="container col-md-8 col-lg-6">
        <div class="glass-panel p-5 bg-white shadow-lg">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h3 class="fw-bold text-primary">Patient Registration</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Cancel</a>
            </div>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Residential Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="mt-4 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Register & Print Card</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>a