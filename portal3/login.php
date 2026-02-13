<?php
require 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Arham Portal - Secure Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #004d26 0%, #000000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }
        
        /* Animated Background Particles */
        .bg-particles {
            position: absolute; width: 100%; height: 100%; top: 0; left: 0;
            background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
            animation: move 60s linear infinite;
        }
        @keyframes move { from { background-position: 0 0; } to { background-position: 100% 100%; } }

        .glass-login {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 50px 40px;
            border-radius: 25px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            position: relative;
            z-index: 10;
        }

        .brand-title {
            color: #fff;
            font-weight: 900;
            font-size: 28px;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .brand-title span { color: #00E5FF; }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            color: #fff;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            box-shadow: none;
        }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.5); }

        .btn-login {
            background: linear-gradient(45deg, #00E5FF, #00b8cc);
            border: none;
            border-radius: 50px;
            padding: 15px;
            width: 100%;
            font-weight: bold;
            font-size: 18px;
            color: #000;
            box-shadow: 0 5px 15px rgba(0, 229, 255, 0.4);
            transition: 0.3s;
        }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0, 229, 255, 0.6); }

        .alert-custom {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ffcccc;
            border-radius: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="bg-particles"></div>

    <div class="glass-login text-center animate__animated animate__fadeInUp">
        <div class="brand-title">Arham <span>Portal</span></div>
        
        <?php if($error): ?>
            <div class="alert alert-custom mb-4"><i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" class="form-control" placeholder="Username" required autofocus autocomplete="off">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            
            <button type="submit" class="btn btn-login mt-3">
                SECURE ACCESS <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="mt-4 text-white-50 small">
            &copy; 2025 Arham Printers & Communications
        </div>
    </div>

</body>
</html>