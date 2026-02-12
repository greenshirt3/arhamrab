<?php
// FILE: migrate_bills.php
require 'includes/config.php';

echo "<h2>Starting Migration...</h2>";

// 1. Fetch all PAID bills that are NOT in the ledger yet
$sql = "SELECT * FROM bill_queue 
        WHERE status = 'paid' 
        AND id NOT IN (SELECT related_id FROM finance_ledger WHERE category = 'Utility Bill')";

$bills = $pdo->query($sql)->fetchAll();
$count = 0;
$total_amt = 0;

foreach ($bills as $b) {
    // 2. Insert into Finance Ledger
    $desc = "Bill Paid: {$b['bill_type']} ({$b['consumer_number']})";
    
    // Check if duplicate (Double Safety)
    $check = $pdo->prepare("SELECT id FROM finance_ledger WHERE related_id = ? AND category = 'Utility Bill'");
    $check->execute([$b['id']]);
    
    if ($check->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head, related_id) VALUES (?, 'expense', 'Utility Bill', ?, ?, 'Konnect', 'HBL Konnect BVS', ?)");
        
        // Use the bill's original creation date as transaction date
        $date = date('Y-m-d', strtotime($b['created_at']));
        
        $stmt->execute([$date, $desc, $b['amount'], $b['id']]);
        
        // 3. Deduct from BVS Balance (Retrospectively)
        $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = 'HBL Konnect BVS'")->execute([$b['amount']]);
        
        $count++;
        $total_amt += $b['amount'];
        echo "Migrated: $desc - Rs. {$b['amount']}<br>";
    }
}

echo "<h3>✅ Success! Migrated $count bills. Total Value: Rs. " . number_format($total_amt) . "</h3>";
echo "<p>Your Dashboard and BVS Balance are now updated.</p>";
echo "<a href='dashboard.php'>Go to Dashboard</a>";
?>