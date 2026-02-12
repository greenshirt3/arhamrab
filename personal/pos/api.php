<?php
session_start();
// SECURITY
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403); exit(json_encode(["error" => "Unauthorized"]));
}

// DATA FILES MAPPING
$files = [
    'invoices' => 'data_invoices.json', // Sales
    'purchases' => 'data_purchases.json', // Stock In
    'items' => 'data_inventory.json',
    'suppliers' => 'data_suppliers.json',
    'customers' => 'data_customers.json', // Customer Ledgers
    'jobs' => 'data_jobs.json', // Printing Orders
    'expenses' => 'data_expenses.json'
];

$action = $_GET['action'] ?? '';

// READ
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (array_key_exists($action, $files)) {
        if (file_exists($files[$action])) {
            echo file_get_contents($files[$action]);
        } else {
            echo '[]';
        }
    } else { echo json_encode(["error" => "Invalid Type"]); }
} 

// WRITE
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    if (array_key_exists($action, $files) && json_decode($input) != null) {
        file_put_contents($files[$action], $input, LOCK_EX);
        echo json_encode(["status" => "success"]);
    } else { http_response_code(400); echo json_encode(["error" => "Fail"]); }
}
?>