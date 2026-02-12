<?php
/**
 * ARHAM PORTAL - Database Connection Engine
 * Location: /db.php (or /includes/db.php)
 */

// 1. DATABASE CONFIGURATION
// These details are matched to your uploaded SQL dump: arham_portal-323133bb14
$host     = 'localhost'; 
$db_name  = 'arham_portal-323133bb14';
$username = 'root'; // Change this to your database username if different
$password = '';     // Change this to your database password

// 2. PDO OPTIONS
// We set error mode to Exception for better debugging and 
// Emulation to false for better security and performance.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Ensure UTF8 for special characters (Urdu/Hindi names)
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

// 3. ESTABLISH CONNECTION
try {
    $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Optional: Log success (only for very deep debugging)
    // error_log("Database Connected Successfully");

} catch (PDOException $e) {
    // 4. ERROR HANDLING
    // In production, we don't want to show the password or host in error messages
    error_log("Connection Failed: " . $e->getMessage());
    
    // User-friendly error message
    die("<div style='padding:20px; border:2px solid red; background:#fff5f5; font-family:sans-serif;'>
            <h2 style='color:red;'>⚠️ Database Connection Error</h2>
            <p>The system is unable to connect to the database. This might be due to maintenance or incorrect credentials.</p>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}

/**
 * HELPER FUNCTIONS
 * You can use these globally across your files since db.php is required in header.php
 */

// Simple function to log system activities
function logSystemEvent($pdo, $user_id, $action, $details = null) {
    try {
        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $action, $details]);
    } catch (Exception $e) {
        // Silently fail to prevent breaking the main process
    }
}

// Function to safely clean input
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>