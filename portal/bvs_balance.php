<?php 
// bvs_balance.php - Enhanced HBL Konnect BVS Manager
// Author: ARHAM ERP System
// Date: 2024
// Version: 3.0

// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');

require 'includes/header.php'; 

// 2. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Check permissions (HBL Access or Admin)
$has_hbl_access = isset($_SESSION['permissions']['hbl']) && $_SESSION['permissions']['hbl'] == 1;
$is_admin = (in_array($_SESSION['user_id'], [1, 14, 15]) || strtolower($_SESSION['role'] ?? '') === 'admin');

if (!$has_hbl_access && !$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED: HBL Konnect BVS Access Required</div></div>");
}

// 3. INITIALIZE ACCOUNT IF MISSING
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE account_name = 'HBL Konnect BVS'");
$stmt->execute();
$acc = $stmt->fetch();

if (!$acc) {
    try {
        $pdo->exec("INSERT INTO accounts (account_name, account_type, current_balance, last_updated) 
                    VALUES ('HBL Konnect BVS', 'device', 0, NOW())");
        header("Refresh:0"); 
        exit();
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// 4. HANDLE FORM SUBMISSION (Load/Withdraw)
$msg = "";
$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_trans'])) {
        $amount = (float) $_POST['amount'];
        $type = $_POST['trans_type']; // 'deposit' or 'withdraw'
        $date = $_POST['trans_date'];
        $desc_note = trim($_POST['desc']) ? " (" . $_POST['desc'] . ")" : "";
        $receipt_no = $_POST['receipt_no'] ?? null;

        if ($amount <= 0) {
            $error = "Please enter a valid amount greater than zero.";
        } else {
            try {
                $pdo->beginTransaction();

                // Get current balances for validation
                $stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE account_name = 'HBL Konnect BVS'");
                $stmt->execute();
                $bvs_balance = (float) $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE account_name = 'Shop Cash Drawer'");
                $stmt->execute();
                $shop_balance = (float) $stmt->fetchColumn();

                if ($type == 'deposit') {
                    // LOAD: Shop Cash -> BVS
                    if ($shop_balance < $amount) {
                        throw new Exception("Insufficient funds in Shop Cash Drawer. Available: Rs. " . number_format($shop_balance));
                    }
                    
                    $desc = "Load BVS Device" . $desc_note;
                    
                    // Update balances
                    $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ?, last_updated = NOW() WHERE account_name = 'Shop Cash Drawer'")
                         ->execute([$amount]);
                    $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ?, last_updated = NOW() WHERE account_name = 'HBL Konnect BVS'")
                         ->execute([$amount]);
                    
                    // Record in Ledger
                    $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head, receipt_no) 
                                          VALUES (?, 'transfer', 'BVS Load', ?, ?, 'Cash', 'HBL Konnect BVS', ?)");
                    $stmt->execute([$date, $desc, $amount, $receipt_no]);
                    
                } else {
                    // WITHDRAW: BVS -> Shop Cash
                    if ($bvs_balance < $amount) {
                        throw new Exception("Insufficient funds in BVS Device. Available: Rs. " . number_format($bvs_balance));
                    }
                    
                    $desc = "Withdraw from BVS" . $desc_note;
                    
                    // Update balances
                    $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ?, last_updated = NOW() WHERE account_name = 'HBL Konnect BVS'")
                         ->execute([$amount]);
                    $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ?, last_updated = NOW() WHERE account_name = 'Shop Cash Drawer'")
                         ->execute([$amount]);
                    
                    // Record in Ledger
                    $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head, receipt_no) 
                                          VALUES (?, 'transfer', 'BVS Withdrawal', ?, ?, 'Cash', 'HBL Konnect BVS', ?)");
                    $stmt->execute([$date, $desc, $amount, $receipt_no]);
                }

                // Log activity
                $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
                $log_details = "BVS {$type}: Rs. {$amount} on {$date}. Note: " . ($_POST['desc'] ?: 'None');
                $stmt->execute([$_SESSION['user_id'], 'BVS Transaction', $log_details, $_SERVER['REMOTE_ADDR']]);

                $pdo->commit();
                $msg = "✅ Transaction recorded successfully!";
                $success = true;
                
                // Refresh account data
                $stmt = $pdo->prepare("SELECT * FROM accounts WHERE account_name = 'HBL Konnect BVS'");
                $stmt->execute();
                $acc = $stmt->fetch();
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Transaction Failed: " . $e->getMessage();
            }
        }
    }
}

// 5. DATE FILTER LOGIC
$start_date = $_GET['start'] ?? date('Y-m-01'); // Default: 1st of Month
$end_date   = $_GET['end']   ?? date('Y-m-d');  // Default: Today
$status_filter = $_GET['status'] ?? 'all';

// Validate dates
if (strtotime($start_date) > strtotime($end_date)) {
    $temp = $start_date;
    $start_date = $end_date;
    $end_date = $temp;
}

// 6. CALCULATE OPENING BALANCE & TOTALS
// Opening Balance = Sum of (Loads - Withdraws - Bill Payments) BEFORE start_date
$sql_open = "SELECT 
    SUM(CASE 
        WHEN description LIKE '%Load BVS%' OR description LIKE '%BVS Load%' THEN amount 
        WHEN description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%' THEN -amount 
        WHEN category = 'Utility Bill' THEN -amount
        ELSE 0 
    END) as opening_bal
    FROM finance_ledger 
    WHERE account_head = 'HBL Konnect BVS' AND trans_date < ?";

$stmt = $pdo->prepare($sql_open);
$stmt->execute([$start_date]);
$opening_balance = (float) $stmt->fetchColumn();

// Calculate Range Totals
$sql_range = "SELECT 
    SUM(CASE WHEN (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%') THEN amount ELSE 0 END) as total_in,
    SUM(CASE WHEN (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%' OR category = 'Utility Bill') THEN amount ELSE 0 END) as total_out
    FROM finance_ledger 
    WHERE account_head = 'HBL Konnect BVS' AND trans_date BETWEEN ? AND ?";

$stmt = $pdo->prepare($sql_range);
$stmt->execute([$start_date, $end_date]);
$range_stats = $stmt->fetch();

$total_in = (float) ($range_stats['total_in'] ?? 0);
$total_out = (float) ($range_stats['total_out'] ?? 0);
$closing_balance = $opening_balance + $total_in - $total_out;

// 7. FETCH DETAILED ENTRIES WITH FILTERS
$sql_entries = "SELECT * FROM finance_ledger 
                WHERE account_head = 'HBL Konnect BVS' 
                AND trans_date BETWEEN ? AND ?";

$params = [$start_date, $end_date];

if ($status_filter == 'load') {
    $sql_entries .= " AND (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%')";
} elseif ($status_filter == 'withdraw') {
    $sql_entries .= " AND (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%')";
} elseif ($status_filter == 'bill') {
    $sql_entries .= " AND category = 'Utility Bill'";
}

$sql_entries .= " ORDER BY trans_date DESC, id DESC";

$stmt = $pdo->prepare($sql_entries);
$stmt->execute($params);
$entries = $stmt->fetchAll();

// 8. STATISTICS
$stmt = $pdo->query("SELECT 
    SUM(CASE WHEN (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%') THEN amount ELSE 0 END) as total_loaded,
    SUM(CASE WHEN (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%') THEN amount ELSE 0 END) as total_withdrawn,
    SUM(CASE WHEN category = 'Utility Bill' THEN amount ELSE 0 END) as total_bills,
    COUNT(CASE WHEN (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%') THEN 1 END) as load_count,
    COUNT(CASE WHEN (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%') THEN 1 END) as withdraw_count
    FROM finance_ledger 
    WHERE account_head = 'HBL Konnect BVS' AND DATE(trans_date) = CURDATE()");

$today_stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HBL Konnect BVS Manager - ARHAM ERP</title>
    <style>
        /* Custom Styles for BVS Manager */
        :root {
            --bvs-primary: #f39c12;
            --bvs-secondary: #e67e22;
            --bvs-success: #27ae60;
            --bvs-danger: #e74c3c;
            --bvs-info: #3498db;
            --bvs-light: #f8f9fa;
            --bvs-dark: #2c3e50;
        }
        
        .bvs-gradient {
            background: linear-gradient(135deg, var(--bvs-primary) 0%, var(--bvs-secondary) 100%);
            color: white;
        }
        
        .bvs-card {
            border-left: 5px solid var(--bvs-primary);
            transition: all 0.3s ease;
        }
        
        .bvs-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: 1;
        }
        
        .stat-card-content {
            position: relative;
            z-index: 2;
        }
        
        .transaction-type {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .type-load {
            background: rgba(39, 174, 96, 0.15);
            color: var(--bvs-success);
            border: 1px solid rgba(39, 174, 96, 0.3);
        }
        
        .type-withdraw {
            background: rgba(231, 76, 60, 0.15);
            color: var(--bvs-danger);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        
        .type-bill {
            background: rgba(52, 152, 219, 0.15);
            color: var(--bvs-info);
            border: 1px solid rgba(52, 152, 219, 0.3);
        }
        
        .quick-amount-btn {
            padding: 8px 15px;
            margin: 2px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .quick-amount-btn:hover {
            background: #f8f9fa;
            border-color: var(--bvs-primary);
        }
        
        .balance-animation {
            animation: balancePulse 2s ease-in-out;
        }
        
        @keyframes balancePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 10px 20px;
            transition: all 0.3s;
        }
        
        .nav-tabs .nav-link:hover {
            color: var(--bvs-primary);
            border-bottom-color: rgba(243, 156, 18, 0.3);
        }
        
        .nav-tabs .nav-link.active {
            color: var(--bvs-primary);
            border-bottom-color: var(--bvs-primary);
            background: transparent;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
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
                        <i class="fas fa-wallet text-warning"></i> HBL Konnect BVS Manager
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">BVS Balance</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="financial_reports.php?type=transfer" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-book me-1"></i> Full Ledger
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <?php if($msg): ?>
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Main Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card bvs-gradient text-white p-3 h-100">
                <div class="stat-card-content">
                    <small class="opacity-75 d-block mb-1">CURRENT BALANCE</small>
                    <h3 class="fw-bold mb-2 <?php echo $success ? 'balance-animation' : ''; ?>">
                        Rs. <?php echo number_format($acc['current_balance'] ?? 0); ?>
                    </h3>
                    <small class="opacity-75">
                        <i class="fas fa-sync-alt me-1"></i> Updated: <?php echo date('h:i A', strtotime($acc['last_updated'])); ?>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-white border p-3 h-100">
                <div class="stat-card-content">
                    <small class="text-muted d-block mb-1">OPENING BALANCE</small>
                    <h4 class="fw-bold text-secondary mb-0">Rs. <?php echo number_format($opening_balance); ?></h4>
                    <small class="text-muted">
                        Before <?php echo date('d M', strtotime($start_date)); ?>
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-success bg-opacity-10 p-3 h-100">
                <div class="stat-card-content">
                    <small class="text-success d-block mb-1">TODAY'S LOADED</small>
                    <h4 class="fw-bold text-success mb-0">+ <?php echo number_format($today_stats['total_loaded'] ?? 0); ?></h4>
                    <small class="text-muted">
                        <i class="fas fa-calendar-day me-1"></i> <?php echo number_format($today_stats['load_count'] ?? 0); ?> transactions
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-danger bg-opacity-10 p-3 h-100">
                <div class="stat-card-content">
                    <small class="text-danger d-block mb-1">TODAY'S WITHDRAWN</small>
                    <h4 class="fw-bold text-danger mb-0">- <?php echo number_format($today_stats['total_withdrawn'] ?? 0); ?></h4>
                    <small class="text-muted">
                        <i class="fas fa-calendar-day me-1"></i> <?php echo number_format($today_stats['withdraw_count'] ?? 0); ?> transactions
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Transaction Form -->
    <div class="row mb-4">
        <!-- Left Column: Transaction Form -->
        <div class="col-lg-4">
            <div class="bvs-card bg-white shadow-sm p-4 h-100">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-plus-circle me-2 text-primary"></i>New Transaction
                </h5>
                
                <form method="POST" id="transactionForm">
                    <input type="hidden" name="save_trans" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction Type</label>
                        <div class="d-flex gap-2 mb-3">
                            <div class="form-check flex-fill">
                                <input class="form-check-input" type="radio" name="trans_type" 
                                       id="type_deposit" value="deposit" checked 
                                       onchange="updateFormLabels()">
                                <label class="form-check-label w-100 text-center p-2 border rounded bg-success bg-opacity-10" for="type_deposit">
                                    <i class="fas fa-arrow-down text-success mb-1 d-block"></i>
                                    <span>Load BVS</span>
                                </label>
                            </div>
                            <div class="form-check flex-fill">
                                <input class="form-check-input" type="radio" name="trans_type" 
                                       id="type_withdraw" value="withdraw" 
                                       onchange="updateFormLabels()">
                                <label class="form-check-label w-100 text-center p-2 border rounded bg-danger bg-opacity-10" for="type_withdraw">
                                    <i class="fas fa-arrow-up text-danger mb-1 d-block"></i>
                                    <span>Withdraw</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="trans_date" class="form-control" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" id="amountLabel">Load Amount</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text fw-bold bg-dark text-white">Rs.</span>
                            <input type="number" name="amount" class="form-control fw-bold" 
                                   placeholder="0" min="0" step="1" required
                                   id="amountInput">
                            <button type="button" class="input-group-text btn btn-outline-dark" 
                                    onclick="clearAmount()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <!-- Quick Amount Buttons -->
                        <div class="mt-2">
                            <small class="text-muted d-block mb-1">Quick Amount:</small>
                            <div class="d-flex flex-wrap gap-1">
                                <?php 
                                $quick_amounts = [500, 1000, 2000, 5000, 10000, 20000];
                                foreach($quick_amounts as $amt): 
                                ?>
                                    <button type="button" class="quick-amount-btn" 
                                            onclick="setAmount(<?php echo $amt; ?>)">
                                        <?php echo number_format($amt); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Receipt / Reference No (Optional)</label>
                        <input type="text" name="receipt_no" class="form-control" 
                               placeholder="e.g., Receipt #12345">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Description / Note</label>
                        <textarea name="desc" class="form-control" rows="2" 
                                  placeholder="Optional note about this transaction..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">
                            <i class="fas fa-check-circle me-2"></i>
                            <span id="submitText">Confirm Load</span>
                        </button>
                    </div>
                    
                    <div class="mt-3 small text-muted text-center">
                        <div id="balanceInfo">
                            <i class="fas fa-info-circle me-1"></i>
                            Loading will deduct from Shop Cash. Current BVS Balance: 
                            <span class="fw-bold">Rs. <?php echo number_format($acc['current_balance'] ?? 0); ?></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Filters & Stats -->
        <div class="col-lg-8">
            <div class="bvs-card bg-white shadow-sm p-0 h-100">
                <!-- Filters Header -->
                <div class="p-3 bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-filter me-2"></i>Transaction History
                        </h5>
                        
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm" style="width: auto;">
                                <input type="date" name="start" class="form-control form-control-sm" 
                                       value="<?php echo $start_date; ?>">
                                <span class="input-group-text">to</span>
                                <input type="date" name="end" class="form-control form-control-sm" 
                                       value="<?php echo $end_date; ?>">
                            </div>
                            
                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Types</option>
                                <option value="load" <?php echo $status_filter == 'load' ? 'selected' : ''; ?>>Load Only</option>
                                <option value="withdraw" <?php echo $status_filter == 'withdraw' ? 'selected' : ''; ?>>Withdraw Only</option>
                                <option value="bill" <?php echo $status_filter == 'bill' ? 'selected' : ''; ?>>Bill Payments</option>
                            </select>
                            
                            <button type="submit" class="btn btn-dark btn-sm">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="bvs_balance.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </form>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="p-3 border-bottom">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted d-block">Total Loaded</small>
                            <div class="fw-bold text-success">+ <?php echo number_format($total_in); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Total Withdrawn</small>
                            <div class="fw-bold text-danger">- <?php echo number_format($total_out); ?></div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">Net Movement</small>
                            <div class="fw-bold <?php echo ($total_in - $total_out) >= 0 ? 'text-primary' : 'text-danger'; ?>">
                                <?php echo ($total_in - $total_out >= 0 ? '+' : '-') . ' ' . number_format(abs($total_in - $total_out)); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-dark text-white sticky-top">
                            <tr>
                                <th class="ps-3">Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-end pe-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($entries) > 0): ?>
                                <?php foreach ($entries as $e): 
                                    $is_load = (strpos($e['description'], 'Load') !== false || $e['category'] == 'BVS Load');
                                    $is_bill = ($e['category'] == 'Utility Bill');
                                    $type_class = $is_load ? 'type-load' : ($is_bill ? 'type-bill' : 'type-withdraw');
                                    $type_text = $is_load ? 'LOAD' : ($is_bill ? 'BILL' : 'WITHDRAW');
                                    $sign = $is_load ? '+' : '-';
                                    $color = $is_load ? 'text-success' : ($is_bill ? 'text-info' : 'text-danger');
                                ?>
                                <tr>
                                    <td class="ps-3 small">
                                        <?php echo date('d M Y', strtotime($e['trans_date'])); ?><br>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($e['trans_date'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="transaction-type <?php echo $type_class; ?>">
                                            <?php echo $type_text; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($e['description']); ?></div>
                                        <?php if($e['receipt_no']): ?>
                                            <small class="text-muted">Ref: <?php echo htmlspecialchars($e['receipt_no']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3 fw-bold <?php echo $color; ?>">
                                        <?php echo $sign; ?> Rs. <?php echo number_format($e['amount']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3 opacity-25"></i><br>
                                        No transactions found in this date range.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Reports & Analytics -->
    <?php if($is_admin): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="bvs-card bg-white shadow-sm p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">
                    <i class="fas fa-chart-line me-2"></i>BVS Analytics & Reports
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-exchange-alt fa-2x text-primary mb-3"></i>
                                <h6 class="fw-bold">Transaction Summary</h6>
                                <div class="small text-muted mb-2">Last 30 Days</div>
                                
                                <?php
                                // Get 30-day summary
                                $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
                                $stmt = $pdo->prepare("SELECT 
                                    SUM(CASE WHEN (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%') THEN amount ELSE 0 END) as total_loaded_30,
                                    SUM(CASE WHEN (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%') THEN amount ELSE 0 END) as total_withdrawn_30,
                                    COUNT(*) as transaction_count
                                    FROM finance_ledger 
                                    WHERE account_head = 'HBL Konnect BVS' AND trans_date >= ?");
                                $stmt->execute([$thirty_days_ago]);
                                $thirty_day_stats = $stmt->fetch();
                                ?>
                                
                                <div class="row text-center mt-3">
                                    <div class="col-6 border-end">
                                        <small class="text-muted">Loaded</small>
                                        <div class="fw-bold text-success">
                                            +<?php echo number_format($thirty_day_stats['total_loaded_30'] ?? 0); ?>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Withdrawn</small>
                                        <div class="fw-bold text-danger">
                                            -<?php echo number_format($thirty_day_stats['total_withdrawn_30'] ?? 0); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Transactions: <?php echo $thirty_day_stats['transaction_count'] ?? 0; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-2x text-warning mb-3"></i>
                                <h6 class="fw-bold">Monthly Activity</h6>
                                <div class="small text-muted mb-2">Current Month</div>
                                
                                <?php
                                $current_month = date('Y-m');
                                $stmt = $pdo->prepare("SELECT 
                                    DAY(trans_date) as day,
                                    SUM(CASE WHEN (description LIKE '%Load BVS%' OR description LIKE '%BVS Load%') THEN amount ELSE 0 END) as daily_load,
                                    SUM(CASE WHEN (description LIKE '%Withdraw%' OR description LIKE '%BVS Withdrawal%') THEN amount ELSE 0 END) as daily_withdraw
                                    FROM finance_ledger 
                                    WHERE account_head = 'HBL Konnect BVS' AND DATE_FORMAT(trans_date, '%Y-%m') = ?
                                    GROUP BY DAY(trans_date) ORDER BY day DESC LIMIT 5");
                                $stmt->execute([$current_month]);
                                $monthly_activity = $stmt->fetchAll();
                                ?>
                                
                                <div class="text-start mt-3">
                                    <?php if(count($monthly_activity) > 0): ?>
                                        <?php foreach($monthly_activity as $activity): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Day <?php echo $activity['day']; ?>:</span>
                                            <span>
                                                <span class="text-success">+<?php echo number_format($activity['daily_load']); ?></span>
                                                <span class="text-danger">-<?php echo number_format($activity['daily_withdraw']); ?></span>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted small">No activity this month</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-lightbulb fa-2x text-info mb-3"></i>
                                <h6 class="fw-bold">Quick Actions</h6>
                                <div class="small text-muted mb-3">Common Operations</div>
                                
                                <div class="d-grid gap-2">
                                    <a href="bills.php" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-invoice-dollar me-2"></i>Pay Bill via BVS
                                    </a>
                                    <a href="accounts.php" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-university me-2"></i>Manage Accounts
                                    </a>
                                    <a href="financial_reports.php?type=transfer" class="btn btn-outline-dark btn-sm">
                                        <i class="fas fa-book me-2"></i>View Full Ledger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Current Balance & Info Footer -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Current Balance Status</h6>
                        <small>
                            Opening Balance (<?php echo date('d M', strtotime($start_date)); ?>): 
                            <span class="fw-bold">Rs. <?php echo number_format($opening_balance); ?></span> 
                            • Closing Balance (<?php echo date('d M', strtotime($end_date)); ?>): 
                            <span class="fw-bold">Rs. <?php echo number_format($closing_balance); ?></span>
                            • System Current: 
                            <span class="fw-bold text-primary">Rs. <?php echo number_format($acc['current_balance'] ?? 0); ?></span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Form Handling Script
function updateFormLabels() {
    const isDeposit = document.getElementById('type_deposit').checked;
    const amountLabel = document.getElementById('amountLabel');
    const submitText = document.getElementById('submitText');
    const balanceInfo = document.getElementById('balanceInfo');
    const currentBalance = <?php echo $acc['current_balance'] ?? 0; ?>;
    
    if (isDeposit) {
        amountLabel.textContent = 'Load Amount';
        submitText.textContent = 'Confirm Load';
        balanceInfo.innerHTML = `<i class="fas fa-info-circle me-1"></i>
                               Loading will deduct from Shop Cash. Current BVS Balance: 
                               <span class="fw-bold">Rs. ${currentBalance.toLocaleString()}</span>`;
    } else {
        amountLabel.textContent = 'Withdraw Amount';
        submitText.textContent = 'Confirm Withdraw';
        balanceInfo.innerHTML = `<i class="fas fa-info-circle me-1"></i>
                               Withdrawing will add to Shop Cash. Current BVS Balance: 
                               <span class="fw-bold">Rs. ${currentBalance.toLocaleString()}</span>`;
    }
}

function setAmount(amount) {
    const input = document.getElementById('amountInput');
    input.value = amount;
    input.focus();
    input.select();
}

function clearAmount() {
    document.getElementById('amountInput').value = '';
}

// Form validation
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amountInput').value);
    const type = document.querySelector('input[name="trans_type"]:checked').value;
    const currentBalance = <?php echo $acc['current_balance'] ?? 0; ?>;
    const shopBalance = <?php 
        $stmt = $pdo->query("SELECT current_balance FROM accounts WHERE account_name = 'Shop Cash Drawer'");
        echo $stmt->fetchColumn() ?? 0;
    ?>;
    
    if (isNaN(amount) || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount greater than zero.');
        return;
    }
    
    if (type === 'withdraw' && amount > currentBalance) {
        e.preventDefault();
        alert(`Insufficient BVS Balance!\nRequested: Rs. ${amount.toLocaleString()}\nAvailable: Rs. ${currentBalance.toLocaleString()}`);
        return;
    }
    
    if (type === 'deposit' && amount > shopBalance) {
        e.preventDefault();
        alert(`Insufficient Shop Cash!\nRequested: Rs. ${amount.toLocaleString()}\nAvailable: Rs. ${shopBalance.toLocaleString()}`);
        return;
    }
    
    // Show confirmation
    const action = type === 'deposit' ? 'Load' : 'Withdraw';
    if (!confirm(`Confirm ${action} of Rs. ${amount.toLocaleString()}?\n\n${action} will ${type === 'deposit' ? 'deduct from Shop Cash and add to BVS' : 'deduct from BVS and add to Shop Cash'}.`)) {
        e.preventDefault();
    }
});

// Initialize form labels
updateFormLabels();

// Auto-focus amount input
document.getElementById('amountInput').focus();

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + L for Load
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        document.getElementById('type_deposit').checked = true;
        updateFormLabels();
        document.getElementById('amountInput').focus();
    }
    
    // Ctrl + W for Withdraw
    if (e.ctrlKey && e.key === 'w') {
        e.preventDefault();
        document.getElementById('type_withdraw').checked = true;
        updateFormLabels();
        document.getElementById('amountInput').focus();
    }
    
    // Ctrl + S to submit
    if (e.ctrlKey && e.key === 's' && !e.target.tagName.match(/^(INPUT|TEXTAREA|SELECT)$/)) {
        e.preventDefault();
        document.querySelector('button[type="submit"]').click();
    }
});

// Auto-refresh page every 5 minutes for live updates
setTimeout(function() {
    location.reload();
}, 300000); // 5 minutes

// Add visual feedback for success
<?php if($success): ?>
    document.querySelector('.balance-animation').addEventListener('animationend', function() {
        this.classList.remove('balance-animation');
    });
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>