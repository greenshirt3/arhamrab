<?php
require_once '../config.php';
require_once '../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patients = getJSON(FILE_PATIENTS);
    $id = strtoupper(trim($_POST['patient_id']));
    $phone = trim($_POST['phone']);
    
    $user = null;
    foreach ($patients as $p) {
        if ($p['id'] === $id && $p['phone'] === $phone) {
            $user = $p;
            break;
        }
    }

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'patient';
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Patient ID or Phone Number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient Portal | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { background: url('https://images.unsplash.com/photo-1579684385136-137af75461bb?auto=format&fit=crop&q=80') center/cover; }
        .overlay { background: rgba(15, 23, 42, 0.85); position: absolute; inset: 0; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 position-relative">
    <div class="overlay"></div>
    
    <div class="glass-panel p-5 position-relative text-center" style="width: 400px; z-index: 2;">
        <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-3 shadow">
            <i class="fas fa-user-injured fa-2x"></i>
        </div>
        <h3 class="fw-bold text-white mb-4">Patient Portal</h3>
        
        <?php if(isset($error)) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>

        <form method="POST">
            <div class="form-floating mb-3">
                <input type="text" name="patient_id" class="form-control" placeholder="ID" required>
                <label>Patient ID (e.g. PAT-25-101)</label>
            </div>
            <div class="form-floating mb-4">
                <input type="text" name="phone" class="form-control" placeholder="Phone" required>
                <label>Registered Phone Number</label>
            </div>
            <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill">Access My Health</button>
        </form>
        <div class="mt-4 text-white-50 small">
            Don't have an account? Visit the Hospital Reception.
        </div>
    </div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>