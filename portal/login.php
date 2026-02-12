<?php
session_start();
require 'includes/config.php';

if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit(); }
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] == 0) {
            $error = "⛔ Account Disabled.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // FIX: Force role to lowercase and Admin Bypass
            $role = strtolower($user['role']);
            $_SESSION['role'] = $role; 

            if ($role === 'admin') {
                // FORCE FULL ACCESS (Fixes 'saif' empty permissions)
                $_SESSION['permissions'] = [
                    'bisp' => 1, 'hbl' => 1, 'shop' => 1, 'loans' => 1, 'closing' => 1
                ];
            } else {
                $perms = json_decode($user['permissions'] ?? '', true);
                $_SESSION['permissions'] = is_array($perms) ? $perms : [];
            }
            
            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid Username or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Arham ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #222; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; }
        .card { background: #333; padding: 40px; border-radius: 10px; color: white; width: 100%; max-width: 400px; }
        .form-control { background: #444; border: 1px solid #555; color: white; margin-bottom: 15px; padding: 12px; }
        .btn-login { background: #00E5FF; border: none; padding: 12px; width: 100%; font-weight: bold; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="card text-center">
        <h2 class="mb-4">ARHAM <span class="text-info">ERP</span></h2>
        <?php if($error): ?><div class="alert alert-danger p-2 small"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button class="btn btn-login">LOGIN</button>
        </form>
    </div>
</body>
</html>