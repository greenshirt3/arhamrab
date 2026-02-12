<?php
// FILE: index.php
session_start();

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    // Not logged in? Go to Login
    header("Location: login.php");
    exit();
} else {
    // Logged in? Go to Dashboard
    header("Location: dashboard.php");
    exit();
}
?>