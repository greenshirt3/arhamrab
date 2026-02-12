<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiderGo | Admin Access</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            background: #111827; height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', sans-serif; color: white; margin: 0;
        }
        .login-card {
            background: #1F2937; padding: 40px; border-radius: 16px; width: 100%; max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); border: 1px solid #374151;
        }
        .brand { text-align: center; margin-bottom: 30px; font-size: 1.8rem; font-weight: 800; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #9CA3AF; font-size: 0.9rem; }
        .form-input {
            width: 100%; background: #374151; border: 1px solid #4B5563; color: white;
            padding: 12px; border-radius: 8px; font-size: 1rem; outline: none; box-sizing: border-box;
        }
        .form-input:focus { border-color: #F37021; }
        .btn-login {
            width: 100%; background: #F37021; color: white; border: none; padding: 14px;
            border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 10px;
            transition: 0.2s;
        }
        .btn-login:hover { background: #d35400; }
        .error-msg { color: #ef4444; text-align: center; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand"><i class="fas fa-bolt" style="color:#F37021"></i> RiderGo <span style="color:#666">Admin</span></div>
        
        <?php if(isset($error)): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Command ID</label>
                <input type="text" name="username" class="form-input" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label>Secure Passkey</label>
                <input type="password" name="password" class="form-input" placeholder="••••••" required>
            </div>
            <button type="submit" name="login" class="btn-login">AUTHENTICATE</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px; color: #6B7280; font-size: 0.8rem;">
            Restricted Access. Logistics Only.
        </div>
    </div>
</body>
</html>