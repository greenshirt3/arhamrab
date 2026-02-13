<?php
// PORTAL ROUTER
session_start();

// 1. If user is already logged in, go to Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// 2. If not logged in, go to Login Page
header("Location: login.php");
exit();
?>