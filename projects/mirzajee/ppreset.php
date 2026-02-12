<?php
include 'config.php';

// This generates the secure code for 'admin123' automatically
$new_pass = password_hash('admin123', PASSWORD_DEFAULT);

// Update the database
$sql = "UPDATE users SET password='$new_pass' WHERE username='admin'";

if ($conn->query($sql) === TRUE) {
    echo "<h1>Success!</h1>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin123</strong><br><br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "Error updating record: " . $conn->error;
}
?>