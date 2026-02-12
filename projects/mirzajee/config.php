<?php
$host = 'sdb-75.hosting.stackcp.net';
$db   = 'mirzajeeproperty-353037312c95';
$user = 'mirzajeeproperty-353037312c95';
$pass = 'Skaea@mirzajeeproperty1';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");

function cleanInput($data) { return htmlspecialchars(stripslashes(trim($data))); }
?>