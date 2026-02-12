<?php
require 'includes/config.php';

// Security: Block direct access if not logged in
if (!isset($_SESSION['user_id'])) { 
    header("HTTP/1.1 403 Forbidden"); 
    exit; 
}

$term = $_GET['term'] ?? '';
$type = $_GET['type'] ?? 'all'; 
$results = [];

// --- 1. PRODUCT SEARCH (For Shop POS) ---
if ($type == 'product') {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE item_name LIKE ? LIMIT 20");
    $stmt->execute(["%$term%"]);
    while($row = $stmt->fetch()) {
        $results[] = [
            'id'    => $row['id'],            // Required for JS
            'label' => $row['item_name'] . " (Stock: " . $row['stock_qty'] . ")",
            'value' => $row['item_name'],     // Puts text in input
            'data'  => $row                   // Contains sale_price, etc.
        ];
    }
}

// --- 2. ACTIVE LOAN SEARCH (For Installments) ---
if ($type == 'loan_active') {
    $stmt = $pdo->prepare("SELECT * FROM loans WHERE person_name LIKE ? AND status='active' LIMIT 20");
    $stmt->execute(["%$term%"]);
    while($row = $stmt->fetch()) {
        $bal = $row['total_amount'] - $row['paid_amount'];
        $results[] = [
            'label' => $row['person_name'] . " (Pending: Rs. " . number_format($bal) . ")",
            'value' => $row['person_name'],
            'data'  => ['id' => $row['id'], 'balance' => $bal]
        ];
    }
}

// --- 3. UTILITY BILL CONSUMER (For Bills.php) ---
if ($type == 'consumer_suggest') {
    $clean_term = str_replace(['-', ' '], '', $term);
    
    // If user types, search. If empty, show recent 50.
    if ($clean_term !== '') {
        $stmt = $pdo->prepare("SELECT * FROM saved_consumers WHERE consumer_number LIKE ? OR consumer_name LIKE ? ORDER BY consumer_number ASC LIMIT 20");
        $stmt->execute(["%$clean_term%", "%$clean_term%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM saved_consumers ORDER BY last_paid_date DESC LIMIT 20");
    }

    while($row = $stmt->fetch()) {
        $results[] = [
            'label' => $row['consumer_number'] . " - " . $row['consumer_name'],
            'value' => $row['consumer_number'],
            'data'  => ['name' => $row['consumer_name'], 'type' => $row['bill_type']]
        ];
    }
}

// --- 4. BISP CNIC (For Payouts & Queue) ---
if ($type == 'cnic' || $type == 'all') {
    $clean_term = str_replace(['-', ' '], '', $term);
    $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE REPLACE(cnic, '-', '') LIKE ? LIMIT 5");
    $stmt->execute(["%$clean_term%"]);
    while($row = $stmt->fetch()) {
        $results[] = [
            'label' => "👤 " . $row['cnic'] . " (" . $row['name'] . ")",
            'value' => $row['cnic'],
            'data'  => $row
        ];
    }
}

// --- 5. ADMIN BILL HISTORY (For Audit) ---
if ($type == 'bill_history') {
    $clean_term = str_replace(['-', ' '], '', $term);
    $stmt = $pdo->prepare("SELECT * FROM bill_queue WHERE consumer_number LIKE ? OR consumer_name LIKE ? ORDER BY id DESC LIMIT 20");
    $stmt->execute(["%$clean_term%", "%$clean_term%"]);
    
    while($row = $stmt->fetch()) {
        $status_icon = ($row['status'] == 'paid') ? '✅' : '⏳';
        $results[] = [
            'label' => "$status_icon " . $row['consumer_name'] . " (" . $row['consumer_number'] . ")",
            'value' => $row['consumer_number'],
            'data'  => $row
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>