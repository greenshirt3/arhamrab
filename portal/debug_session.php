<?php
session_start();
echo "<h1>Session Debugger</h1>";
echo "<b>User ID:</b> " . ($_SESSION['user_id'] ?? 'Not Set') . "<br>";
echo "<b>Username:</b> " . ($_SESSION['username'] ?? 'Not Set') . "<br>";
echo "<b>Role:</b> " . ($_SESSION['role'] ?? 'Not Set') . " (Must be 'admin' lowercase)<br>";
echo "<h3>Permissions:</h3>";
echo "<pre>";
print_r($_SESSION['permissions']);
echo "</pre>";
echo "<a href='logout.php'>Logout and Fix</a>";
?>