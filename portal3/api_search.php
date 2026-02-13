<?php
require 'includes/config.php';

if (!isset($_SESSION['user_id'])) { header("HTTP/1.1 403 Forbidden"); exit; }

$term = $_GET['term'] ?? '';
$type = $_GET['type'] ?? 'all'; 
$results = [];
$clean_term = str_replace(['-', ' '], '', $term);

// =========================================================
// 1. ADMIN AUDIT SEARCH (History)
// Used by: night_mode.php (Admin only)
// =========================================================
if ($type == 'bill_history') {
    if ($clean_term !== '') {
        $stmt = $pdo->prepare("SELECT * FROM bill_queue WHERE consumer_number LIKE ? OR consumer_name LIKE ? ORDER BY id DESC LIMIT 50");
        $stmt->execute(["%$clean_term%", "%$clean_term%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM bill_queue ORDER BY id DESC LIMIT 50");
    }

    while($row = $stmt->fetch()) {
        $status_icon = ($row['status'] == 'paid') ? '✅' : '⏳';
        $results[] = [
            'label' => "$status_icon " . $row['consumer_name'] . " (" . $row['consumer_number'] . ") - Rs." . $row['amount'],
            'value' => $row['consumer_number'],
            'data'  => $row
        ];
    }
    echo json_encode($results);
    exit;
}

// =========================================================
// 2. STAFF BILL ENTRY (Auto-Show Dropdown)
// Used by: bills.php
// =========================================================
if ($type == 'consumer_suggest') {
    if ($clean_term !== '') {
        $stmt = $pdo->prepare("SELECT * FROM saved_consumers WHERE consumer_number LIKE ? ORDER BY consumer_number ASC LIMIT 50");
        $stmt->execute(["%$clean_term%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM saved_consumers ORDER BY consumer_number ASC LIMIT 100");
    }
    while($row = $stmt->fetch()) {
        $results[] = [
            'label' => $row['consumer_number'] . " - " . $row['consumer_name'],
            'value' => $row['consumer_number'],
            'data'  => ['name' => $row['consumer_name'], 'type' => $row['bill_type']]
        ];
    }
    echo json_encode($results);
    exit;
}

// =========================================================
// 3. SCANNER CHECK (Single Result)
// =========================================================
if ($type == 'consumer') {
    if(strlen($clean_term) > 5) {
        $stmt = $pdo->prepare("SELECT * FROM saved_consumers WHERE consumer_number LIKE ? LIMIT 1");
        $stmt->execute(["%$clean_term%"]);
        $row = $stmt->fetch();
        
        if ($row) {
            echo json_encode(['status' => 'found', 'name' => $row['consumer_name'], 'type' => $row['bill_type']]);
        } else {
            echo json_encode(['status' => 'new']);
        }
        exit;
    }
}

// =========================================================
// 4. TOKEN / CNIC SEARCH
// =========================================================
if ($type == 'token' || $type == 'all' || strpos($term, '-') !== false) {
    $stmt = $pdo->prepare("SELECT token_number, cnic, name FROM queue_tokens WHERE token_number LIKE ? AND DATE(issued_at) = CURDATE() LIMIT 5");
    $stmt->execute(["%$term%"]);
    while($row = $stmt->fetch()) {
        $results[] = [
            'label' => "🎫 Token #" . $row['token_number'],
            'value' => $row['token_number'],
            'data'  => ['token'=>$row['token_number'], 'cnic'=>$row['cnic'], 'name'=>$row['name'], 'phone'=>'']
        ];
    }
}
if ($type == 'cnic' || $type == 'all' || is_numeric($clean_term)) {
    $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE REPLACE(cnic, '-', '') LIKE ? LIMIT 5");
    $stmt->execute(["%$clean_term%"]);
    while($row = $stmt->fetch()) {
        $results[] = [
            'label' => "👤 " . $row['cnic'] . " (" . $row['name'] . ")",
            'value' => $row['cnic'],
            'data'  => ['token'=>'', 'cnic'=>$row['cnic'], 'name'=>$row['name'], 'phone'=>$row['phone']]
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>