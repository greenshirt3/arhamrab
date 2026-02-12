<?php
require 'includes/config.php';

$stmt = $pdo->query("
    SELECT invoice_no, category as customer_name, amount, trans_date
    FROM finance_ledger 
    WHERE type = 'sale'
    ORDER BY id DESC 
    LIMIT 10
");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($sales);
?>