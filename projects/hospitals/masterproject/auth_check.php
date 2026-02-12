<?php
// PREVENT DIRECT ACCESS & LOAD CONFIG
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/config.php';
}

// FIX: Include functions.php here so getJSON() works in all portals
require_once __DIR__ . '/functions.php';

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login if session missing
        header("Location: ../doctor/login.php");
        exit();
    }
}

function requireRole($allowed_roles = []) {
    requireLogin();
    if (!in_array($_SESSION['role'], $allowed_roles) && $_SESSION['role'] !== 'admin') {
        echo "<div style='padding:50px; text-align:center; font-family:sans-serif;'>
                <h1 style='color:red;'>Access Denied</h1>
                <p>You do not have permission to view this portal.</p>
                <a href='../index.php'>Go Home</a>
              </div>";
        exit();
    }
}
?>