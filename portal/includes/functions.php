<?php
// Function to record every move
function logActivity($pdo, $action, $details) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $user_id = $_SESSION['user_id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip]);
    } catch (Exception $e) {
        // Silently fail logging if DB issue, don't stop the app
    }
}

// Function to check strict Admin access
function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        die("<div class='alert alert-danger text-center m-5 fw-bold'>⛔ ACCESS DENIED: Admin Authorization Required</div>");
    }
}
?>