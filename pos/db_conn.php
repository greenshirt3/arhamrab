<?php
// db_conn.php
// CHANGE THESE TO YOUR REAL CPANEL DETAILS
$host = "localhost";
$user = "arham_db-3138368585";   // Your Database Username
$pass = "Skaea@rabhost1";   // Your Database Password
$dbname = "arham_db";      // Your Database Name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(["error" => "DB Connection Failed: " . $e->getMessage()]));
}
?>