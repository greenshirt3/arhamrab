<?php
// =========================================================
// ARHAM PRINTERS - MAIN CONFIGURATION FILE
// Path: public_html/includes/config.php
// =========================================================

// 1. Set Timezone (Pakistan Standard Time)
date_default_timezone_set('Asia/Karachi');

// 2. Session Configuration
// This ensures sessions are secure and last for 30 days
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400 * 30, // 30 days
        'path' => '/',
        'secure' => true,      // Requires HTTPS (Essential for RabHost)
        'httponly' => true,    // Protects against JavaScript attacks
        'samesite' => 'Lax'    // Best for normal browsing
    ]);
    session_start();
}

// 3. Error Reporting
// Keep these lines COMMENTED OUT for your live website (Security).
// Uncomment them only if you are fixing a "White Screen" error.
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

?>