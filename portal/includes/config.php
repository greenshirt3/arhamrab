<?php
// includes/config.php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. GLOBAL SETTINGS
date_default_timezone_set('Asia/Karachi');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off visible errors for users
ini_set('log_errors', 1);

// 2. RABHOST DATABASE CONNECTION
define('DB_HOST', 'sdb-w.hosting.stackcp.net');
define('DB_NAME', 'arham_portal-323133bb14');
define('DB_USER', 'arham_portal-323133bb14');
define('DB_PASS', 'Skaea@rabhost1');

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET time_zone = '+05:00';");
} catch (PDOException $e) {
    // Graceful failure
    die("<h2 style='color:red;text-align:center;font-family:sans-serif;margin-top:50px;'>⚠️ System Offline</h2>");
}

// 3. PERMISSION HELPER FUNCTION
if (!function_exists('has_perm')) {
    function has_perm($key) {
        // A. Master Admins (Hardcoded IDs for safety)
        if (isset($_SESSION['user_id']) && in_array($_SESSION['user_id'], [1, 14, 15])) return true;
        
        // B. Admin Role
        if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') return true;
        
        // C. Specific Permission Check
        $perms = $_SESSION['permissions'] ?? [];
        
        // Handle JSON format if permissions are stored as string
        if (is_string($perms)) {
            $perms = json_decode($perms, true);
        }
        
        // Check key
        return isset($perms[$key]) && $perms[$key] == 1;
    }
}
?>