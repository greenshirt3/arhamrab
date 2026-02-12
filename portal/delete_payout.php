<?php
require 'includes/config.php';
requireAuth(); // Security Check

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // 1. Check if this payout was linked to a Token
    // (You might need to add token_id to transactions table if not there, 
    // strictly speaking this is optional but good for consistency)
    
    // 2. Delete the Transaction
    $pdo->prepare("DELETE FROM transactions WHERE id = ?")->execute([$id]);
    
    // Log the action
    if(function_exists('logActivity')) {
        logActivity($pdo, "Delete Transaction", "Deleted ID: $id");
    }
}

header("Location: reports.php"); // Redirect back to reports, not payout
exit();
?>