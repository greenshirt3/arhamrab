<?php
session_start();
include 'config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Incorrect Password";
        }
    } else {
        $error = "Username not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Login | Mirza Ji Property</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/609/609803.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background-image: linear-gradient(rgba(15, 61, 62, 0.9), rgba(6, 78, 59, 0.95)), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center; }
    </style>
</head>
<body class="h-screen flex items-center justify-center font-sans p-4">
    <div class="bg-white/10 backdrop-blur-lg p-8 rounded-2xl shadow-2xl w-full max-w-md border border-white/20">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#D4AF37] rounded-xl mx-auto flex items-center justify-center text-[#0F3D3E] text-3xl shadow-lg mb-4"><i class="fas fa-building"></i></div>
            <h1 class="text-3xl font-bold text-white font-serif tracking-wide">MIRZA JI</h1>
            <p class="text-[#D4AF37] text-xs font-bold uppercase tracking-[0.3em]">Staff Portal</p>
        </div>
        <?php if($error): ?>
            <div class="bg-red-500/80 text-white p-3 rounded mb-6 text-sm text-center border border-red-400"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-5">
                <label class="block text-gray-300 text-xs font-bold uppercase mb-2">Username</label>
                <div class="relative"><span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-user"></i></span><input type="text" name="username" class="w-full bg-white/5 border border-white/10 rounded-lg py-3 pl-10 pr-4 text-white focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition" placeholder="Enter ID" required></div>
            </div>
            <div class="mb-8">
                <label class="block text-gray-300 text-xs font-bold uppercase mb-2">Password</label>
                <div class="relative"><span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-lock"></i></span><input type="password" name="password" class="w-full bg-white/5 border border-white/10 rounded-lg py-3 pl-10 pr-4 text-white focus:outline-none focus:border-[#D4AF37] focus:ring-1 focus:ring-[#D4AF37] transition" placeholder="••••••••" required></div>
            </div>
            <button type="submit" class="w-full bg-[#D4AF37] text-[#0F3D3E] font-bold py-3.5 rounded-lg hover:bg-yellow-500 transition shadow-lg transform hover:-translate-y-1">Secure Login <i class="fas fa-arrow-right ml-2"></i></button>
        </form>
        <div class="mt-6 text-center"><a href="index.php" class="text-gray-400 text-sm hover:text-white transition">← Back to Website</a></div>
    </div>
</body>
</html>