<?php 
// financial_reports.php - Enhanced Financial Ledger & Reports
// Author: ARHAM ERP System
// Date: 2024
// Version: 3.0

// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');

require 'includes/header.php'; 

// 2. FALLBACK HELPER (Prevents Crash)
if (!function_exists('logActivity')) { 
    function logActivity($pdo, $action, $details) {
        try {
            $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'] ?? 0, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
        } catch (Exception $e) {
            // Silent fail
        }
    }
}

// 3. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$allowed_ids = [1, 14, 15]; 
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role'] ?? '') === 'admin');

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED: Financial Reports Access Required</div></div>");
}

// 4. HANDLE DELETE (Admin Only)
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $reason = $_POST['delete_reason'] ?? 'No reason provided';
    
    try {
        $pdo->beginTransaction();
        
        // Fetch entry to reverse balance
        $stmt = $pdo->prepare("SELECT * FROM finance_ledger WHERE id = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch();
        
        if($entry) {
            $acct = $entry['account_head'];
            $amt = $entry['amount'];
            $type = $entry['type'];
            $desc = $entry['description'];
            
            // Reverse Balance Logic
            if (in_array($type, ['income', 'sale', 'loan_return', 'adjustment'])) {
                // Was Income/Positive, so Remove from Balance
                $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = ?")->execute([$amt, $acct]);
            } elseif (in_array($type, ['expense', 'purchase', 'loan_given', 'transfer'])) {
                // Was Expense/Negative, so Add back to Balance
                $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name = ?")->execute([$amt, $acct]);
            }
            
            // Archive before deletion
            $stmt = $pdo->prepare("INSERT INTO deleted_transactions 
                                  (original_id, trans_date, type, category, description, amount, account_head, 
                                   invoice_no, payment_method, deleted_by, deleted_at, delete_reason)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            $stmt->execute([$id, $entry['trans_date'], $entry['type'], $entry['category'], 
                           $entry['description'], $entry['amount'], $entry['account_head'],
                           $entry['invoice_no'], $entry['payment_method'], $_SESSION['user_id'], $reason]);
            
            // Delete from ledger
            $pdo->prepare("DELETE FROM finance_ledger WHERE id = ?")->execute([$id]);
            
            // Log activity
            logActivity($pdo, 'Delete Transaction', "Deleted ID: $id - $desc - Reason: $reason");
            
            $pdo->commit();
            $success_msg = "✅ Transaction deleted and balance reverted successfully!";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "❌ Delete failed: " . $e->getMessage();
    }
}

// 5. HANDLE BULK ACTIONS
if (isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
    $action = $_POST['bulk_action'];
    $selected_ids = $_POST['selected_ids'];
    
    if ($action === 'export' && !empty($selected_ids)) {
        // Prepare export data
        $ids = implode(',', array_map('intval', $selected_ids));
        $stmt = $pdo->prepare("SELECT * FROM finance_ledger WHERE id IN ($ids) ORDER BY trans_date DESC");
        $stmt->execute();
        $export_data = $stmt->fetchAll();
        
        // Generate CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=financial_report_' . date('Y-m-d_H-i') . '.csv');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Date', 'Type', 'Category', 'Description', 'Amount', 'Account', 'Invoice #', 'Payment Method']);
        
        foreach ($export_data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['trans_date'],
                $row['type'],
                $row['category'],
                $row['description'],
                $row['amount'],
                $row['account_head'],
                $row['invoice_no'] ?? '',
                $row['payment_method'] ?? ''
            ]);
        }
        fclose($output);
        exit();
    }
}

// 6. DATE & FILTER LOGIC
$start = $_GET['start'] ?? date('Y-m-01'); // Default: 1st of this month
$end = $_GET['end'] ?? date('Y-m-d');     // Default: Today
$type_filter = $_GET['type'] ?? '';
$category_filter = $_GET['category'] ?? '';
$account_filter = $_GET['account'] ?? '';
$search = $_GET['search'] ?? '';
$min_amount = $_GET['min_amount'] ?? '';
$max_amount = $_GET['max_amount'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'id_desc';
$items_per_page = $_GET['limit'] ?? 50;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $items_per_page;

// Validate dates
if (strtotime($start) > strtotime($end)) {
    $temp = $start;
    $start = $end;
    $end = $temp;
}

// Build Query
$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM finance_ledger WHERE 1=1";
$params = [];
$types = [];

if (!empty($start)) {
    $sql .= " AND trans_date >= ?";
    $params[] = $start;
    $types[] = 'date';
}

if (!empty($end)) {
    $sql .= " AND trans_date <= ?";
    $params[] = $end;
    $types[] = 'date';
}

if (!empty($type_filter)) {
    $sql .= " AND type = ?";
    $params[] = $type_filter;
    $types[] = 'string';
}

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types[] = 'string';
}

if (!empty($account_filter)) {
    $sql .= " AND account_head = ?";
    $params[] = $account_filter;
    $types[] = 'string';
}

if (!empty($search)) {
    $sql .= " AND (description LIKE ? OR invoice_no LIKE ? OR account_head LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types = array_merge($types, ['string', 'string', 'string']);
}

if (!empty($min_amount)) {
    $sql .= " AND amount >= ?";
    $params[] = $min_amount;
    $types[] = 'float';
}

if (!empty($max_amount)) {
    $sql .= " AND amount <= ?";
    $params[] = $max_amount;
    $types[] = 'float';
}

// Sorting
$sort_options = [
    'id_desc' => 'id DESC',
    'id_asc' => 'id ASC',
    'date_desc' => 'trans_date DESC, id DESC',
    'date_asc' => 'trans_date ASC, id ASC',
    'amount_desc' => 'amount DESC',
    'amount_asc' => 'amount ASC'
];

$sql .= " ORDER BY " . ($sort_options[$sort_by] ?? 'id DESC');

// Pagination
$sql .= " LIMIT ? OFFSET ?";
$params[] = (int)$items_per_page;
$params[] = (int)$offset;
$types = array_merge($types, ['int', 'int']);

// Execute
try {
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters with types
    foreach ($params as $i => $param) {
        $type = $types[$i] ?? 'string';
        switch ($type) {
            case 'int': $stmt->bindValue($i+1, $param, PDO::PARAM_INT); break;
            case 'float': $stmt->bindValue($i+1, $param, PDO::PARAM_STR); break;
            case 'date': $stmt->bindValue($i+1, $param, PDO::PARAM_STR); break;
            default: $stmt->bindValue($i+1, $param, PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    $rows = $stmt->fetchAll();
    
    // Get total count
    $total_stmt = $pdo->query("SELECT FOUND_ROWS()");
    $total_rows = $total_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $items_per_page);
    
} catch (PDOException $e) {
    die("<div class='alert alert-danger'>SQL Error: " . $e->getMessage() . "</div>");
}

// Calculate Totals
$total_in = 0; 
$total_out = 0;
$category_totals = [];
$account_totals = [];

foreach ($rows as $r) {
    $is_income = in_array($r['type'], ['income', 'sale', 'loan_return', 'adjustment']);
    $amount = (float)$r['amount'];
    
    if ($is_income) {
        $total_in += $amount;
    } else {
        $total_out += $amount;
    }
    
    // Category totals
    $category = $r['category'] ?? 'Uncategorized';
    if (!isset($category_totals[$category])) {
        $category_totals[$category] = ['in' => 0, 'out' => 0];
    }
    if ($is_income) {
        $category_totals[$category]['in'] += $amount;
    } else {
        $category_totals[$category]['out'] += $amount;
    }
    
    // Account totals
    $account = $r['account_head'] ?? 'Unknown';
    if (!isset($account_totals[$account])) {
        $account_totals[$account] = ['in' => 0, 'out' => 0];
    }
    if ($is_income) {
        $account_totals[$account]['in'] += $amount;
    } else {
        $account_totals[$account]['out'] += $amount;
    }
}

// Fetch unique categories and accounts for filters
$categories = $pdo->query("SELECT DISTINCT category FROM finance_ledger WHERE category != '' ORDER BY category")->fetchAll();
$accounts = $pdo->query("SELECT DISTINCT account_head FROM finance_ledger WHERE account_head != '' ORDER BY account_head")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Ledger & Reports - ARHAM ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom Styles for Financial Reports */
        :root {
            --report-primary: #2c3e50;
            --report-secondary: #3498db;
            --report-success: #27ae60;
            --report-danger: #e74c3c;
            --report-warning: #f39c12;
            --report-info: #1abc9c;
            --report-light: #f8f9fa;
            --report-dark: #212529;
        }
        
        .report-gradient {
            background: linear-gradient(135deg, var(--report-primary) 0%, var(--report-secondary) 100%);
            color: white;
        }
        
        .report-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            border-left: 5px solid;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.05), rgba(255,255,255,0));
            z-index: 1;
        }
        
        .stat-card-content {
            position: relative;
            z-index: 2;
        }
        
        .transaction-type-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .type-income {
            background: rgba(39, 174, 96, 0.15);
            color: var(--report-success);
            border: 1px solid rgba(39, 174, 96, 0.3);
        }
        
        .type-expense {
            background: rgba(231, 76, 60, 0.15);
            color: var(--report-danger);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        
        .type-transfer {
            background: rgba(52, 152, 219, 0.15);
            color: var(--report-secondary);
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .type-adjustment {
            background: rgba(155, 89, 182, 0.15);
            color: #9b59b6;
            border: 1px solid rgba(155, 89, 182, 0.3);
        }
        
        .category-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        
        .filter-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            margin: 2px;
            background: #e9ecef;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 24px;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--report-primary);
            border-bottom-color: rgba(44, 62, 80, 0.3);
        }
        
        .nav-tabs .nav-link.active {
            color: var(--report-primary);
            border-bottom-color: var(--report-primary);
            background: transparent;
        }
        
        .data-table th {
            background: var(--report-primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 15px 12px;
            border: none;
        }
        
        .data-table td {
            padding: 12px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }
        
        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .amount-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }
        
        .checkbox-cell {
            width: 40px;
        }
        
        .pagination-custom .page-link {
            color: var(--report-primary);
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 8px;
        }
        
        .pagination-custom .page-item.active .page-link {
            background: var(--report-primary);
            border-color: var(--report-primary);
            color: white;
        }
        
        .chart-container {
            height: 300px;
            position: relative;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            border: none;
        }
        
        .export-btn:hover {
            background: linear-gradient(135deg, #229954 0%, #27ae60 100%);
            color: white;
        }
        
        .bulk-actions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            .report-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
        
        /* Animation for new entries */
        @keyframes highlightRow {
            0% { background-color: rgba(52, 152, 219, 0.3); }
            100% { background-color: transparent; }
        }
        
        .highlight-new {
            animation: highlightRow 2s ease-out;
        }
        
        /* Summary cards */
        .summary-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .summary-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255,255,255,0.1);
            transform: rotate(30deg);
        }
        
        .summary-card-income {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        }
        
        .summary-card-expense {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .summary-card-net {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }
        
        /* Filter panel */
        .filter-panel {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="container-fluid py-3">
    
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-chart-line text-primary"></i> Financial Ledger & Reports
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Financial Reports</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="financial_reports.php?export=pdf&start=<?php echo $start; ?>&end=<?php echo $end; ?>" 
                       class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>
                    <button class="btn export-btn btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <?php if(isset($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-card summary-card-income">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="opacity-75 d-block mb-1">TOTAL INCOME</small>
                        <h3 class="fw-bold mb-2">Rs. <?php echo number_format($total_in); ?></h3>
                        <small class="opacity-75">
                            <i class="fas fa-arrow-up me-1"></i> Positive transactions
                        </small>
                    </div>
                    <i class="fas fa-arrow-down fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="summary-card summary-card-expense">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="opacity-75 d-block mb-1">TOTAL EXPENSE</small>
                        <h3 class="fw-bold mb-2">Rs. <?php echo number_format($total_out); ?></h3>
                        <small class="opacity-75">
                            <i class="fas fa-arrow-down me-1"></i> Negative transactions
                        </small>
                    </div>
                    <i class="fas fa-arrow-up fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="summary-card summary-card-net">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="opacity-75 d-block mb-1">NET BALANCE</small>
                        <h3 class="fw-bold mb-2 <?php echo ($total_in - $total_out) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            Rs. <?php echo number_format($total_in - $total_out); ?>
                        </h3>
                        <small class="opacity-75">
                            <i class="fas fa-balance-scale me-1"></i> Income - Expense
                        </small>
                    </div>
                    <i class="fas fa-chart-line fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="report-card bg-white p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">TRANSACTIONS</small>
                        <h4 class="fw-bold mb-2"><?php echo number_format($total_rows); ?></h4>
                        <small class="text-muted">
                            Showing <?php echo count($rows); ?> of <?php echo number_format($total_rows); ?>
                        </small>
                    </div>
                    <i class="fas fa-receipt fa-2x text-muted"></i>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar" style="width: <?php echo min(100, (count($rows) / max(1, $total_rows)) * 100); ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="filter-panel mb-4">
        <form method="GET" id="filterForm" class="row g-3">
            <!-- Date Range -->
            <div class="col-md-3">
                <label class="form-label fw-bold small">Date Range</label>
                <div class="input-group input-group-sm">
                    <input type="date" name="start" class="form-control" value="<?php echo $start; ?>">
                    <span class="input-group-text">to</span>
                    <input type="date" name="end" class="form-control" value="<?php echo $end; ?>">
                </div>
            </div>
            
            <!-- Type Filter -->
            <div class="col-md-2">
                <label class="form-label fw-bold small">Transaction Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="sale" <?php echo $type_filter == 'sale' ? 'selected' : ''; ?>>Sales</option>
                    <option value="expense" <?php echo $type_filter == 'expense' ? 'selected' : ''; ?>>Expenses</option>
                    <option value="purchase" <?php echo $type_filter == 'purchase' ? 'selected' : ''; ?>>Purchases</option>
                    <option value="income" <?php echo $type_filter == 'income' ? 'selected' : ''; ?>>Other Income</option>
                    <option value="loan_given" <?php echo $type_filter == 'loan_given' ? 'selected' : ''; ?>>Loans Given</option>
                    <option value="loan_return" <?php echo $type_filter == 'loan_return' ? 'selected' : ''; ?>>Loan Returns</option>
                    <option value="transfer" <?php echo $type_filter == 'transfer' ? 'selected' : ''; ?>>Transfers</option>
                    <option value="adjustment" <?php echo $type_filter == 'adjustment' ? 'selected' : ''; ?>>Adjustments</option>
                </select>
            </div>
            
            <!-- Category Filter -->
            <div class="col-md-2">
                <label class="form-label fw-bold small">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                            <?php echo $category_filter == $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Account Filter -->
            <div class="col-md-2">
                <label class="form-label fw-bold small">Account</label>
                <select name="account" class="form-select form-select-sm">
                    <option value="">All Accounts</option>
                    <?php foreach($accounts as $acc): ?>
                        <option value="<?php echo htmlspecialchars($acc['account_head']); ?>" 
                            <?php echo $account_filter == $acc['account_head'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($acc['account_head']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Amount Range -->
            <div class="col-md-3">
                <label class="form-label fw-bold small">Amount Range</label>
                <div class="input-group input-group-sm">
                    <input type="number" name="min_amount" class="form-control" placeholder="Min" 
                           value="<?php echo $min_amount; ?>">
                    <span class="input-group-text">to</span>
                    <input type="number" name="max_amount" class="form-control" placeholder="Max" 
                           value="<?php echo $max_amount; ?>">
                </div>
            </div>
            
            <!-- Search -->
            <div class="col-md-4">
                <label class="form-label fw-bold small">Search</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Description, Invoice #, Account..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <!-- Sort & Limit -->
            <div class="col-md-2">
                <label class="form-label fw-bold small">Sort By</label>
                <select name="sort_by" class="form-select form-select-sm">
                    <option value="id_desc" <?php echo $sort_by == 'id_desc' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="id_asc" <?php echo $sort_by == 'id_asc' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="date_desc" <?php echo $sort_by == 'date_desc' ? 'selected' : ''; ?>>Date (Newest)</option>
                    <option value="date_asc" <?php echo $sort_by == 'date_asc' ? 'selected' : ''; ?>>Date (Oldest)</option>
                    <option value="amount_desc" <?php echo $sort_by == 'amount_desc' ? 'selected' : ''; ?>>Amount (High to Low)</option>
                    <option value="amount_asc" <?php echo $sort_by == 'amount_asc' ? 'selected' : ''; ?>>Amount (Low to High)</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label class="form-label fw-bold small">Per Page</label>
                <select name="limit" class="form-select form-select-sm">
                    <option value="20" <?php echo $items_per_page == 20 ? 'selected' : ''; ?>>20</option>
                    <option value="50" <?php echo $items_per_page == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?php echo $items_per_page == 100 ? 'selected' : ''; ?>>100</option>
                    <option value="200" <?php echo $items_per_page == 200 ? 'selected' : ''; ?>>200</option>
                    <option value="500" <?php echo $items_per_page == 500 ? 'selected' : ''; ?>>500</option>
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i> Apply
                    </button>
                    <a href="financial_reports.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Active Filters -->
        <div class="mt-3">
            <small class="fw-bold text-muted me-2">Active Filters:</small>
            <?php if($start && $end): ?>
                <span class="filter-tag">
                    Date: <?php echo date('d M', strtotime($start)); ?> - <?php echo date('d M', strtotime($end)); ?>
                    <a href="?" class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                </span>
            <?php endif; ?>
            <?php if($type_filter): ?>
                <span class="filter-tag">
                    Type: <?php echo ucfirst($type_filter); ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['type' => ''])); ?>" class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                </span>
            <?php endif; ?>
            <?php if($search): ?>
                <span class="filter-tag">
                    Search: "<?php echo htmlspecialchars($search); ?>"
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['search' => ''])); ?>" class="ms-1 text-danger"><i class="fas fa-times"></i></a>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions no-print">
        <form method="POST" id="bulkForm" onsubmit="return confirmBulkAction()">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label fw-bold small" for="selectAll">
                            Select All (<?php echo count($rows); ?>)
                        </label>
                    </div>
                    <select name="bulk_action" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Bulk Actions</option>
                        <option value="export">Export Selected</option>
                        <option value="delete" disabled>Delete Selected</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Total: <?php echo number_format($total_rows); ?> transactions
                </div>
            </div>
            <input type="hidden" name="selected_ids" id="selectedIds">
        </form>
    </div>

    <!-- Main Table -->
    <div class="report-card bg-white mb-4">
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectPage" class="form-check-input">
                        </th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Account</th>
                        <th class="text-end">Amount</th>
                        <th>Invoice</th>
                        <th class="text-end no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) > 0): ?>
                        <?php foreach($rows as $r): 
                            $is_income = in_array($r['type'], ['income', 'sale', 'loan_return', 'adjustment']);
                            $type_class = $is_income ? 'type-income' : 'type-expense';
                            if ($r['type'] == 'transfer') $type_class = 'type-transfer';
                            if ($r['type'] == 'adjustment') $type_class = 'type-adjustment';
                            
                            $color = $is_income ? 'text-success' : 'text-danger';
                            if ($r['type'] == 'transfer') $color = 'text-info';
                            if ($r['type'] == 'adjustment') $color = 'text-purple';
                            
                            // Invoice Link
                            $ref = "-";
                            if (!empty($r['invoice_no'])) {
                                $ref = "<a href='invoice.php?id={$r['invoice_no']}' target='_blank' class='badge bg-light text-dark border text-decoration-none'>{$r['invoice_no']}</a>";
                            } elseif (!empty($r['related_id']) && $r['category']=='Utility Bill') {
                                $ref = "<a href='invoice_bill.php?id={$r['related_id']}' target='_blank' class='badge bg-warning text-dark'>BILL #{$r['related_id']}</a>";
                            }
                        ?>
                        <tr id="row-<?php echo $r['id']; ?>" class="<?php echo (isset($_GET['highlight']) && $_GET['highlight'] == $r['id']) ? 'highlight-new' : ''; ?>">
                            <td class="checkbox-cell">
                                <input type="checkbox" class="form-check-input row-checkbox" value="<?php echo $r['id']; ?>">
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo date('d M Y', strtotime($r['trans_date'])); ?></div>
                                <small class="text-muted"><?php echo date('h:i A', strtotime($r['trans_date'])); ?></small>
                            </td>
                            <td>
                                <span class="transaction-type-badge <?php echo $type_class; ?>">
                                    <?php echo strtoupper($r['type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="category-badge">
                                    <?php echo htmlspecialchars($r['category']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($r['description']); ?></div>
                                <?php if($r['payment_method']): ?>
                                    <small class="text-muted">Method: <?php echo $r['payment_method']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-bold text-primary"><?php echo $r['account_head']; ?></span>
                            </td>
                            <td class="text-end amount-cell fw-bold <?php echo $color; ?>">
                                <?php echo ($is_income ? '+' : '-'); ?> Rs. <?php echo number_format($r['amount']); ?>
                            </td>
                            <td><?php echo $ref; ?></td>
                            <td class="text-end no-print">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary border-0 dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showTransactionDetails(<?php echo $r['id']; ?>)">
                                                <i class="fas fa-eye me-2"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="editTransaction(<?php echo $r['id']; ?>)">
                                                <i class="fas fa-edit me-2"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirmDelete(<?php echo $r['id']; ?>, '<?php echo addslashes($r['description']); ?>')">
                                                <input type="hidden" name="delete_id" value="<?php echo $r['id']; ?>">
                                                <input type="hidden" name="delete_reason" id="delete_reason_<?php echo $r['id']; ?>" value="">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-25"></i><br>
                                No transactions found for the selected filters.
                                <?php if($start || $end || $type_filter || $search): ?>
                                    <div class="mt-2">
                                        <a href="financial_reports.php" class="btn btn-sm btn-outline-primary">
                                            Clear all filters
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                
                <!-- Summary Footer -->
                <tfoot class="bg-light fw-bold border-top">
                    <tr>
                        <td colspan="5" class="text-end pt-3">SUMMARY:</td>
                        <td class="text-center pt-3"><?php echo count($rows); ?> transactions</td>
                        <td class="text-end pt-3">
                            <div class="text-success">+ Rs. <?php echo number_format($total_in); ?></div>
                            <div class="text-danger">- Rs. <?php echo number_format($total_out); ?></div>
                        </td>
                        <td colspan="2" class="text-end pt-3">
                            <span class="<?php echo ($total_in - $total_out) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                NET: Rs. <?php echo number_format($total_in - $total_out); ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($total_pages > 1): ?>
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-custom justify-content-center">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="text-center text-muted small">
                Page <?php echo $page; ?> of <?php echo $total_pages; ?> • 
                <?php echo number_format($total_rows); ?> total transactions
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Analytics & Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="report-card bg-white p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-pie me-2 text-primary"></i>Category Analysis
                </h5>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Top 10 categories by total amount</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="report-card bg-white p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-bar me-2 text-success"></i>Daily Trend
                </h5>
                <div class="chart-container">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Daily income vs expense for selected period</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-card bg-white p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-university me-2 text-info"></i>Account Summary
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th class="text-end">Income</th>
                                <th class="text-end">Expense</th>
                                <th class="text-end">Net</th>
                                <th class="text-end">% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            arsort($account_totals);
                            $counter = 0;
                            foreach($account_totals as $account => $totals): 
                                if($counter++ >= 10) break;
                                $net = $totals['in'] - $totals['out'];
                                $percentage = ($total_in + $total_out) > 0 ? 
                                    (($totals['in'] + $totals['out']) / ($total_in + $total_out)) * 100 : 0;
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($account); ?></td>
                                <td class="text-end text-success">+ Rs. <?php echo number_format($totals['in']); ?></td>
                                <td class="text-end text-danger">- Rs. <?php echo number_format($totals['out']); ?></td>
                                <td class="text-end fw-bold <?php echo $net >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($net >= 0 ? '+' : '-'); ?> Rs. <?php echo number_format(abs($net)); ?>
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?php echo number_format($percentage, 1); ?>%</small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="report-card bg-white p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-bolt me-2 text-warning"></i>Quick Reports
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="financial_reports.php?type=sale&start=<?php echo date('Y-m-d'); ?>&end=<?php echo date('Y-m-d'); ?>" 
                           class="card text-decoration-none border h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-3"></i>
                                <h6>Today's Sales</h6>
                                <small class="text-muted">View all sales today</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3">
                        <a href="financial_reports.php?type=expense&start=<?php echo date('Y-m-01'); ?>&end=<?php echo date('Y-m-d'); ?>" 
                           class="card text-decoration-none border h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-receipt fa-2x text-danger mb-3"></i>
                                <h6>Monthly Expenses</h6>
                                <small class="text-muted">This month's expenses</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3">
                        <a href="financial_reports.php?type=transfer" 
                           class="card text-decoration-none border h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-exchange-alt fa-2x text-info mb-3"></i>
                                <h6>All Transfers</h6>
                                <small class="text-muted">Internal transfers</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col-md-3">
                        <a href="invoice_history.php" 
                           class="card text-decoration-none border h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-history fa-2x text-success mb-3"></i>
                                <h6>Invoice History</h6>
                                <small class="text-muted">All invoices</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this transaction?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    This action will reverse the balance in the affected account.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Reason for deletion:</label>
                    <textarea id="deleteReasonInput" class="form-control" rows="2" 
                              placeholder="Please provide a reason for deleting this transaction..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Transaction Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                Loading...
            </div>
        </div>
    </div>
</div>

<script>
// Chart Data Preparation
const categoryData = <?php 
    $chart_categories = [];
    $chart_amounts = [];
    $counter = 0;
    foreach($category_totals as $category => $totals) {
        if($counter++ >= 10) break;
        $chart_categories[] = $category;
        $chart_amounts[] = abs($totals['in'] + $totals['out']);
    }
    echo json_encode([
        'categories' => $chart_categories,
        'amounts' => $chart_amounts
    ]);
?>;

// Daily Trend Data
const dailyData = <?php
    // Get daily data for selected period
    $stmt = $pdo->prepare("SELECT 
        DATE(trans_date) as date,
        SUM(CASE WHEN type IN ('income', 'sale', 'loan_return', 'adjustment') THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type IN ('expense', 'purchase', 'loan_given', 'transfer') THEN amount ELSE 0 END) as expense
        FROM finance_ledger 
        WHERE trans_date BETWEEN ? AND ?
        GROUP BY DATE(trans_date)
        ORDER BY date ASC");
    $stmt->execute([$start, $end]);
    $daily_stats = $stmt->fetchAll();
    
    $dates = [];
    $income = [];
    $expense = [];
    
    foreach($daily_stats as $day) {
        $dates[] = date('d M', strtotime($day['date']));
        $income[] = $day['income'];
        $expense[] = $day['expense'];
    }
    
    echo json_encode([
        'dates' => $dates,
        'income' => $income,
        'expense' => $expense
    ]);
?>;

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Category Pie Chart
    const ctx1 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: categoryData.categories,
            datasets: [{
                data: categoryData.amounts,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6',
                    '#1abc9c', '#d35400', '#c0392b', '#7f8c8d', '#34495e'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Daily Trend Chart
    const ctx2 = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: dailyData.dates,
            datasets: [
                {
                    label: 'Income',
                    data: dailyData.income,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Expense',
                    data: dailyData.expense,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});

// Bulk Selection
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedIds();
});

document.getElementById('selectPage').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedIds();
});

document.querySelectorAll('.row-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedIds);
});

function updateSelectedIds() {
    const selected = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                         .map(cb => cb.value);
    document.getElementById('selectedIds').value = JSON.stringify(selected);
}

function confirmBulkAction() {
    const action = document.querySelector('select[name="bulk_action"]').value;
    const selectedIds = JSON.parse(document.getElementById('selectedIds').value || '[]');
    
    if (selectedIds.length === 0) {
        alert('Please select at least one transaction.');
        return false;
    }
    
    if (action === 'export') {
        return true;
    }
    
    return false;
}

// Delete Confirmation
let pendingDeleteId = null;
let pendingDeleteForm = null;

function confirmDelete(id, description) {
    pendingDeleteId = id;
    pendingDeleteForm = event.target.closest('form');
    
    document.getElementById('deleteReasonInput').value = '';
    document.getElementById('deleteReasonInput').focus();
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    
    return false;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const reason = document.getElementById('deleteReasonInput').value.trim();
    if (!reason) {
        alert('Please provide a reason for deletion.');
        return;
    }
    
    document.getElementById('delete_reason_' + pendingDeleteId).value = reason;
    pendingDeleteForm.submit();
});

// Transaction Details
function showTransactionDetails(id) {
    fetch(`api_transaction_details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const modalBody = document.getElementById('transactionDetails');
            modalBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-6">
                        <strong>Transaction ID:</strong><br>
                        <span class="badge bg-primary">${data.id}</span>
                    </div>
                    <div class="col-6">
                        <strong>Date:</strong><br>
                        ${new Date(data.trans_date).toLocaleDateString()} ${new Date(data.trans_date).toLocaleTimeString()}
                    </div>
                    <div class="col-12">
                        <strong>Description:</strong><br>
                        ${data.description}
                    </div>
                    <div class="col-6">
                        <strong>Type:</strong><br>
                        <span class="badge ${data.type === 'income' || data.type === 'sale' ? 'bg-success' : 'bg-danger'}">
                            ${data.type.toUpperCase()}
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>Category:</strong><br>
                        ${data.category}
                    </div>
                    <div class="col-6">
                        <strong>Amount:</strong><br>
                        <span class="fw-bold ${data.type === 'income' || data.type === 'sale' ? 'text-success' : 'text-danger'}">
                            ${data.type === 'income' || data.type === 'sale' ? '+' : '-'} Rs. ${Number(data.amount).toLocaleString()}
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>Account:</strong><br>
                        ${data.account_head}
                    </div>
                    ${data.invoice_no ? `
                    <div class="col-12">
                        <strong>Invoice:</strong><br>
                        <a href="invoice.php?id=${data.invoice_no}" target="_blank">${data.invoice_no}</a>
                    </div>` : ''}
                    ${data.payment_method ? `
                    <div class="col-12">
                        <strong>Payment Method:</strong><br>
                        ${data.payment_method}
                    </div>` : ''}
                    <div class="col-12">
                        <strong>Created:</strong><br>
                        ${new Date(data.created_at || data.trans_date).toLocaleString()}
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load transaction details.');
        });
}

function editTransaction(id) {
    // Redirect to edit page or show edit modal
    alert('Edit functionality for transaction #' + id + ' would open here.');
    // window.location.href = `edit_transaction.php?id=${id}`;
}

// Export to Excel
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.location.href = `financial_reports.php?${params.toString()}`;
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + F to focus search
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
    }
    
    // Ctrl + E to export
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        exportToExcel();
    }
    
    // Ctrl + P to print
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
});

// Auto-refresh every 2 minutes for live updates
setTimeout(function() {
    if (document.visibilityState === 'visible') {
        location.reload();
    }
}, 120000);

// Filter form submission with loading indicator
document.getElementById('filterForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});

// Initialize tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Highlight new rows on page load
document.querySelectorAll('.highlight-new').forEach(row => {
    setTimeout(() => {
        row.classList.remove('highlight-new');
    }, 2000);
});
</script>

<?php include 'includes/footer.php'; ?>