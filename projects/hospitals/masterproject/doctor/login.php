<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = getJSON(FILE_USERS);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    foreach ($users as $u) {
        if ($u['username'] === $username && password_verify($password, $u['password'])) {
            // Success! Set Session
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['role'] = $u['role'];
            $_SESSION['name'] = $u['name'];
            $_SESSION['dept'] = $u['dept'] ?? 'General';
            
            // TRAFFIC CONTROLLER: Send users to their specific portals
            switch ($u['role']) {
                case 'doctor':
                    header("Location: dashboard.php");
                    break;
                case 'lab':
                    header("Location: ../lab/dashboard.php");
                    break;
                case 'pharmacy':
                    header("Location: ../pharmacy/dashboard.php");
                    break;
                case 'reception':
                case 'admin':
                    header("Location: ../reception/dashboard.php");
                    break;
                default:
                    header("Location: ../index.php");
            }
            exit();
        }
    }
    $error = "Invalid Username or Password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Login | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); display: flex; align-items: center; justify-content: center; height: 100vh; }
    </style>
</head>
<body>
    <div class="glass-panel p-5 text-center text-white" style="width: 400px; background: rgba(255,255,255,0.1);">
        <i class="fas fa-user-md fa-4x mb-3 text-info"></i>
        <h2 class="fw-bold mb-4">Staff Portal</h2>
        
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label>Username</label>
                <input type="text" name="username" class="form-control bg-transparent text-white" placeholder="admin / doctor / lab" required>
            </div>
            <div class="mb-4 text-start">
                <label>Password</label>
                <input type="password" name="password" class="form-control bg-transparent text-white" required>
            </div>
            <button class="btn btn-info w-100 fw-bold py-2">Secure Access</button>
        </form>
        <p class="mt-4 text-white-50 small">Restricted Area. Authorized Personnel Only.</p>
    </div>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</body>
</html>