<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'includes/config.php';

echo "<h2>🛠️ Database Auto-Fixer</h2>";

// Helper function to add column safely
function addCol($pdo, $table, $col, $def) {
    try {
        $check = $pdo->query("SHOW COLUMNS FROM $table LIKE '$col'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $col $def");
            echo "<div style='color:green'>✅ Added column <b>$col</b> to <b>$table</b>.</div>";
        } else {
            echo "<div style='color:gray'>Current column <b>$col</b> already exists in <b>$table</b>. (Skipped)</div>";
        }
    } catch (PDOException $e) {
        echo "<div style='color:red'>❌ Error on $col: " . $e->getMessage() . "</div>";
    }
}

// 1. FIX 'bill_queue' TABLE
echo "<h3>Checking 'bill_queue'...</h3>";
addCol($pdo, 'bill_queue', 'customer_id', 'INT DEFAULT NULL');
addCol($pdo, 'bill_queue', 'payment_status', "ENUM('cash', 'credit') DEFAULT 'cash'");
addCol($pdo, 'bill_queue', 'mobile_no', 'VARCHAR(20) DEFAULT NULL');
addCol($pdo, 'bill_queue', 'transaction_id', 'VARCHAR(100) DEFAULT NULL');

// Fix Bill Type Length
try {
    $pdo->exec("ALTER TABLE bill_queue MODIFY bill_type VARCHAR(100)");
    echo "<div style='color:green'>✅ Updated <b>bill_type</b> length.</div>";
} catch (Exception $e) {}

// 2. FIX 'saved_consumers' TABLE
echo "<h3>Checking 'saved_consumers'...</h3>";
addCol($pdo, 'saved_consumers', 'mobile_no', 'VARCHAR(20) DEFAULT NULL');
try {
    $pdo->exec("ALTER TABLE saved_consumers MODIFY bill_type VARCHAR(100)");
    echo "<div style='color:green'>✅ Updated <b>bill_type</b> length.</div>";
} catch (Exception $e) {}

// 3. FIX 'beneficiaries' TABLE
echo "<h3>Checking 'beneficiaries'...</h3>";
addCol($pdo, 'beneficiaries', 'last_visit', 'DATE DEFAULT NULL');
addCol($pdo, 'beneficiaries', 'phone', 'VARCHAR(20) DEFAULT NULL');
addCol($pdo, 'beneficiaries', 'name', 'VARCHAR(100) DEFAULT NULL');

echo "<hr><h3>🎉 Database Repair Complete!</h3>";
echo "<a href='bills.php' style='font-size:20px; font-weight:bold;'>Click here to go back to Bills</a>";
?>