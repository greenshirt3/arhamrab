<?php
require 'includes/config.php';
requireAuth();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Option A: Hard Delete (Remove completely)
    // $pdo->prepare("DELETE FROM queue_tokens WHERE id = ?")->execute([$id]);

    // Option B: Soft Delete (Mark as cancelled - Recommended)
    $pdo->prepare("UPDATE queue_tokens SET status='cancelled' WHERE id = ?")->execute([$id]);
}

header("Location: queue.php");
exit();
?>