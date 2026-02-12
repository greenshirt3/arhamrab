<?php
// includes/config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. TIMEZONE
date_default_timezone_set('Asia/Karachi');

// 2. DATABASE CONFIG
define('DB_HOST', 'sdb-w.hosting.stackcp.net');
define('DB_NAME', 'arham_portal-323133bb14');
define('DB_USER', 'arham_portal-323133bb14');
define('DB_PASS', 'Skaea@rabhost1'); 

try {
    // --- THE FIX: Force utf8mb4 in the connection string ---
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // --- THE FIX: Force SQL Mode for Urdu Characters ---
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
    $pdo->exec("SET time_zone = '+05:00';");
    
} catch (PDOException $e) {
    // Show a clean error if DB fails
    die("<h3>Database Connection Error</h3><p>Please check your config settings.</p>");
}

// 3. AUTH HELPER
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// 4. LOGGER
function logActivity($pdo, $action, $details = "") {
    if(isset($_SESSION['user_id'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $action, $details, $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            // Ignore log errors
        }
    }
}
?>