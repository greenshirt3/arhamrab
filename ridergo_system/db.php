<?php
// StackCP Database Credentials
$host = 'sdb-56.hosting.stackcp.net'; 
$user = 'ridergo_db-353031330e88'; // Usually same as DB name on StackCP, check your dashboard if different
$pass = 'Ridergo@2733';        // ⚠️ REPLACE WITH YOUR ACTUAL DATABASE PASSWORD
$db   = 'ridergo_db-353031330e88';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>