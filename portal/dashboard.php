<?php
// 1. ENABLE ERROR REPORTING (But hide Deprecation warnings to be safe)
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

require 'includes/header.php';

// 2. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 3. DETERMINE USER ROLE AND PERMISSIONS
$user_id = $_SESSION['user_id'];
$user_role = strtolower($_SESSION['role'] ?? 'staff');
$username = htmlspecialchars($_SESSION['username']);

// Check if admin (by ID or role)
$admin_ids = [1, 14, 15];
$is_admin = (in_array($user_id, $admin_ids) || $user_role === 'admin');
$is_staff = !$is_admin;

// Get user permissions
$permissions = $_SESSION['permissions'] ?? [];
$has_shop_access = isset($permissions['shop']) && $permissions['shop'] == 1;
$has_bisp_access = isset($permissions['bisp']) && $permissions['bisp'] == 1;
$has_loans_access = isset($permissions['loans']) && $permissions['loans'] == 1;
$has_closing_access = isset($permissions['closing']) && $permissions['closing'] == 1;
$has_hbl_access = isset($permissions['hbl']) && $permissions['hbl'] == 1;

// 4. FETCH METRICS (FIXED NULL ISSUES)
// A. CASH IN HAND
$stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE account_name = 'Shop Cash Drawer'");
$stmt->execute();
$cash_hand = (float) $stmt->fetchColumn();

// B. RECEIVABLES (Money people owe YOU)
$stmt = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM loans WHERE type = 'given' OR loan_category = 'installment'");
$receivables = (float) $stmt->fetchColumn();

// C. PAYABLES (Money YOU owe)
$stmt = $pdo->query("SELECT SUM(total_amount - paid_amount) FROM loans WHERE type = 'taken' OR loan_category = 'bank'");
$payables = (float) $stmt->fetchColumn();

// D. STOCK VALUE
$stmt = $pdo->query("SELECT SUM(stock_qty * purchase_price) FROM inventory WHERE stock_qty > 0");
$stock_val = (float) $stmt->fetchColumn();

// E. TODAY'S SALES
$today_sale = $pdo->query("SELECT SUM(amount) FROM finance_ledger WHERE type = 'sale' AND DATE(trans_date) = CURDATE()")->fetchColumn();
$today_sale = (float) $today_sale;

// F. TODAY'S BILL COLLECTION
$today_bills = $pdo->query("SELECT SUM(amount) FROM bill_queue WHERE DATE(created_at) = CURDATE() AND payment_status='cash'")->fetchColumn();
$today_bills = (float) $today_bills;

// G. TODAY'S EXPENSES
$today_expenses = $pdo->query("SELECT SUM(amount) FROM finance_ledger WHERE type = 'expense' AND DATE(trans_date) = CURDATE() AND category NOT IN ('Loan Repayment')")->fetchColumn();
$today_expenses = (float) $today_expenses;

// H. TOTAL CUSTOMERS
$total_customers = $pdo->query("SELECT COUNT(*) FROM loans WHERE type='given'")->fetchColumn();

// I. TOTAL SUPPLIERS
$total_suppliers = $pdo->query("SELECT COUNT(*) FROM loans WHERE type='taken'")->fetchColumn();

// J. LOW STOCK ALERTS
$stmt = $pdo->query("SELECT * FROM inventory WHERE stock_qty < 5 AND stock_qty > -1 ORDER BY stock_qty ASC LIMIT 5");
$low_stock = $stmt->fetchAll();

// K. RECENT ACTIVITY (limit based on role)
$limit = $is_admin ? 10 : 5;
$recent = $pdo->query("SELECT * FROM finance_ledger ORDER BY id DESC LIMIT $limit")->fetchAll();

// L. PENDING BILLS
$pending_bills = $pdo->query("SELECT COUNT(*) FROM bill_queue WHERE status='pending'")->fetchColumn();

// M. ACTIVE LOANS (Installments)
$active_loans = $pdo->query("SELECT COUNT(*) FROM loans WHERE loan_category='installment' AND status='active'")->fetchColumn();

// N. NET PROFIT TODAY (Sales - Expenses)
$net_profit_today = $today_sale - $today_expenses;

// O. BVS BALANCE
$bvs_balance = $pdo->query("SELECT current_balance FROM accounts WHERE account_name = 'HBL Konnect BVS'")->fetchColumn();
$bvs_balance = (float) $bvs_balance;

// 5. CHART DATA FOR ADMIN (Last 7 days)
$chart_data = [];
if ($is_admin) {
    $stmt = $pdo->query("
        SELECT DATE(trans_date) as date, 
               SUM(CASE WHEN type IN ('sale', 'income') THEN amount ELSE 0 END) as income,
               SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
        FROM finance_ledger 
        WHERE trans_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(trans_date)
        ORDER BY date ASC
    ");
    $chart_data = $stmt->fetchAll();
}

// 6. TOP CUSTOMERS (for admin)
$top_customers = [];
if ($is_admin) {
    $stmt = $pdo->query("
        SELECT person_name, (total_amount - paid_amount) as balance_due
        FROM loans 
        WHERE type='given' 
        HAVING balance_due > 0
        ORDER BY balance_due DESC 
        LIMIT 5
    ");
    $top_customers = $stmt->fetchAll();
}

// 7. TOP SELLING PRODUCTS
$top_products = [];
if ($has_shop_access || $is_admin) {
    $stmt = $pdo->query("
        SELECT description, COUNT(*) as count, SUM(amount) as revenue
        FROM finance_ledger 
        WHERE type='sale' 
        AND DATE(trans_date) = CURDATE()
        GROUP BY description 
        ORDER BY revenue DESC 
        LIMIT 5
    ");
    $top_products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ARHAM ERP</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        /* ===== CUSTOM STYLES ===== */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-danger: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            --gradient-warning: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* ===== GLASS EFFECT ===== */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: var(--shadow);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .glass-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
        }

        /* ===== METRIC CARDS ===== */
        .metric-card {
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 25px;
            border-radius: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: 1;
        }

        .metric-card i {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 60px;
            opacity: 0.2;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .metric-card:hover i {
            transform: scale(1.2) rotate(5deg);
            opacity: 0.3;
        }

        .metric-card-content {
            position: relative;
            z-index: 2;
        }

        .metric-title {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .metric-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .metric-trend {
            font-size: 12px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-btn {
            height: 110px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: white;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            color: white;
        }

        .action-btn i {
            font-size: 32px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .action-btn:hover i {
            transform: scale(1.2);
        }

        .action-label {
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }

        /* ===== BADGES ===== */
        .badge-custom {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .metric-card {
                min-height: 120px;
                padding: 20px;
            }
            
            .metric-value {
                font-size: 1.8rem;
            }
            
            .action-btn {
                height: 90px;
            }
            
            .action-btn i {
                font-size: 24px;
            }
        }

        /* ===== CHART CONTAINER ===== */
        .chart-container {
            height: 300px;
            position: relative;
        }

        /* ===== USER PROFILE ===== */
        .user-profile-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        .user-profile-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        /* ===== PERMISSION INDICATORS ===== */
        .permission-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .permission-badge.active {
            background: rgba(46, 204, 113, 0.2);
            color: var(--success);
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .permission-badge.inactive {
            background: rgba(231, 76, 60, 0.1);
            color: var(--danger);
            border: 1px solid rgba(231, 76, 60, 0.2);
        }

        /* ===== TAB STYLES ===== */
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            color: var(--primary);
            border-color: rgba(44, 62, 80, 0.3);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: transparent;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* ===== NOTIFICATION BELL ===== */
        .notification-bell {
            position: relative;
            cursor: pointer;
        }

        .notification-bell .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 3px 6px;
            font-size: 10px;
        }

        /* ===== LOADING ANIMATION ===== */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== DASHBOARD HEADER ===== */
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,192C672,181,768,139,864,138.7C960,139,1056,181,1152,197.3C1248,213,1344,203,1392,197.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .greeting {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .greeting-time {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        /* ===== ROLE BADGE ===== */
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .role-badge.admin {
            background: linear-gradient(45deg, #ff8a00, #e52e71);
            color: white;
        }

        .role-badge.staff {
            background: linear-gradient(45deg, #3498db, #2ecc71);
            color: white;
        }

        /* ===== WELCOME MESSAGE ===== */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .welcome-card h3 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-card p {
            opacity: 0.9;
            margin-bottom: 0;
        }

        /* ===== QUICK STATS ===== */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-item {
            background: white;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== DASHBOARD SECTIONS ===== */
        .dashboard-section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .section-actions {
            display: flex;
            gap: 10px;
        }

        /* ===== TABLE STYLES ===== */
        .dashboard-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .dashboard-table .table {
            margin-bottom: 0;
        }

        .dashboard-table th {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
        }

        .dashboard-table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .dashboard-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* ===== PROGRESS BARS ===== */
        .progress-custom {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }

        .progress-custom .progress-bar {
            border-radius: 4px;
        }

        /* ===== TOOLTIPS ===== */
        .custom-tooltip {
            position: relative;
            cursor: help;
        }

        .custom-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .custom-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 10px);
        }

        /* ===== SCROLLBAR STYLING ===== */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
    </style>
</head>
<body>
    <!-- Main Container -->
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header animate__animated animate__fadeInDown">
            <div class="header-content">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="greeting">
                            <?php 
                                $hour = date('H');
                                if ($hour < 12) echo "Good Morning";
                                elseif ($hour < 17) echo "Good Afternoon";
                                else echo "Good Evening";
                            ?>, <?php echo $username; ?>!
                        </h1>
                        <p class="greeting-time mb-0">
                            <i class="fas fa-calendar-alt me-2"></i><?php echo date('l, F j, Y'); ?>
                            <i class="fas fa-clock ms-4 me-2"></i><?php echo date('h:i A'); ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="role-badge <?php echo $is_admin ? 'admin' : 'staff'; ?>">
                            <i class="fas fa-<?php echo $is_admin ? 'crown' : 'user-tie'; ?> me-2"></i>
                            <?php echo $is_admin ? 'Administrator' : 'Staff Member'; ?>
                        </span>
                        <div class="mt-2">
                            <small class="opacity-75">
                                <i class="fas fa-id-card me-1"></i> ID: <?php echo $user_id; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-based Welcome Message -->
        <div class="welcome-card animate__animated animate__fadeInUp">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3>
                        <?php if($is_admin): ?>
                            <i class="fas fa-chart-line me-2"></i>Complete Business Overview
                        <?php else: ?>
                            <i class="fas fa-tasks me-2"></i>Your Assigned Tasks
                        <?php endif; ?>
                    </h3>
                    <p>
                        <?php if($is_admin): ?>
                            You have full access to all system modules. Monitor performance, manage finances, and oversee operations.
                        <?php else: ?>
                            You can access modules assigned by administrator. Contact admin for additional permissions.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="notification-bell">
                        <i class="fas fa-bell fa-2x float-animation"></i>
                        <?php if($pending_bills > 0): ?>
                            <span class="badge bg-danger rounded-pill"><?php echo $pending_bills; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tachometer-alt me-2"></i>Quick Overview
                </h2>
                <div class="section-actions">
                    <?php if($has_closing_access || $is_admin): ?>
                        <a href="closing.php" class="btn btn-warning btn-sm">
                            <i class="fas fa-lock me-1"></i> Day Closing
                        </a>
                    <?php endif; ?>
                    <button onclick="location.reload()" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="quick-stats">
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60); color: white;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value text-success">Rs. <?php echo number_format($cash_hand); ?></div>
                        <div class="stat-label">Cash in Hand</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white;">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value text-primary">Rs. <?php echo number_format($today_sale + $today_bills); ?></div>
                        <div class="stat-label">Today's Collection</div>
                    </div>
                </div>

                <?php if($is_admin): ?>
                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value text-danger">Rs. <?php echo number_format($receivables); ?></div>
                        <div class="stat-label">Receivables</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon" style="background: linear-gradient(45deg, #f39c12, #d35400); color: white;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value text-warning">Rs. <?php echo number_format($stock_val); ?></div>
                        <div class="stat-label">Stock Value</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="row">
            <!-- Left Column: Metrics & Quick Actions -->
            <div class="col-lg-8">
                <!-- Quick Actions Grid -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-rocket me-2"></i>Quick Actions
                        </h2>
                        <div class="section-actions">
                            <span class="badge bg-info"><?php echo count($permissions); ?> Permissions</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Always show basic actions -->
                        <div class="col-md-3 col-6">
                            <a href="shop_pos.php" class="action-btn" 
                               style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white;">
                                <i class="fas fa-cash-register"></i>
                                <span class="action-label">New Sale</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="bills.php" class="action-btn"
                               style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span class="action-label">Add Bill</span>
                            </a>
                        </div>

                        <!-- Role-based actions -->
                        <?php if($has_bisp_access || $is_admin): ?>
                        <div class="col-md-3 col-6">
                            <a href="payout.php" class="action-btn"
                               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span class="action-label">BISP Payout</span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if($has_shop_access || $is_admin): ?>
                        <div class="col-md-3 col-6">
                            <a href="inventory.php" class="action-btn"
                               style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #333;">
                                <i class="fas fa-boxes"></i>
                                <span class="action-label">Inventory</span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if($is_admin): ?>
                        <div class="col-md-3 col-6">
                            <a href="financial_reports.php" class="action-btn"
                               style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                                <i class="fas fa-chart-bar"></i>
                                <span class="action-label">Reports</span>
                            </a>
                        </div>

                        <div class="col-md-3 col-6">
                            <a href="contacts.php" class="action-btn"
                               style="background: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%); color: #333;">
                                <i class="fas fa-address-book"></i>
                                <span class="action-label">Contacts</span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if($has_loans_access || $is_admin): ?>
                        <div class="col-md-3 col-6">
                            <a href="loans.php" class="action-btn"
                               style="background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #333;">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span class="action-label">Loans</span>
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if($has_hbl_access || $is_admin): ?>
                        <div class="col-md-3 col-6">
                            <a href="bvs_balance.php" class="action-btn"
                               style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                <i class="fas fa-wallet"></i>
                                <span class="action-label">BVS Balance</span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-history me-2"></i>Recent Activity
                        </h2>
                        <div class="section-actions">
                            <a href="financial_reports.php" class="btn btn-sm btn-outline-primary">
                                View Full Ledger <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-table">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($recent) > 0): ?>
                                        <?php foreach($recent as $r): 
                                            $is_in = in_array($r['type'], ['sale','income','loan_return']);
                                            $cls = $is_in ? 'text-success' : 'text-danger';
                                            $icon = $is_in ? 'fa-arrow-down text-success' : 'fa-arrow-up text-danger';
                                            $bg = $is_in ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10';
                                        ?>
                                        <tr>
                                            <td class="text-muted small">
                                                <?php echo date('h:i A', strtotime($r['trans_date'])); ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo htmlspecialchars($r['description']); ?></div>
                                                <small class="text-muted">
                                                    <?php echo date('d M', strtotime($r['trans_date'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $bg; ?> text-dark">
                                                    <?php echo strtoupper($r['category']); ?>
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold <?php echo $cls; ?>">
                                                <i class="fas <?php echo $icon; ?> me-1"></i>
                                                Rs. <?php echo number_format($r['amount']); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-3"></i><br>
                                                No recent activity
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Admin-only: Charts -->
                <?php if($is_admin && !empty($chart_data)): ?>
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-chart-line me-2"></i>7-Day Financial Trend
                        </h2>
                        <div class="section-actions">
                            <select class="form-select form-select-sm w-auto" onchange="updateChart(this.value)">
                                <option value="7">Last 7 Days</option>
                                <option value="30">Last 30 Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="glass-panel p-3">
                        <div class="chart-container">
                            <canvas id="financialChart"></canvas>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Alerts, Stats, Permissions -->
            <div class="col-lg-4">
                <!-- User Profile Card -->
                <div class="user-profile-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="rounded-circle bg-white p-3" style="width: 70px; height: 70px;">
                                <i class="fas fa-user fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1"><?php echo $username; ?></h4>
                            <div class="d-flex align-items-center gap-2">
                                <span class="role-badge <?php echo $is_admin ? 'admin' : 'staff'; ?>">
                                    <?php echo $is_admin ? 'Administrator' : 'Staff'; ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('h:i A'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6 border-end border-white-50">
                            <small class="opacity-75">Session</small>
                            <div class="fw-bold">Active</div>
                        </div>
                        <div class="col-6">
                            <small class="opacity-75">IP Address</small>
                            <div class="fw-bold"><?php echo $_SERVER['REMOTE_ADDR']; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Panel (for staff) -->
                <?php if(!$is_admin): ?>
                <div class="glass-panel p-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-key me-2"></i>Your Permissions
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="permission-badge <?php echo $has_shop_access ? 'active' : 'inactive'; ?>">
                            <i class="fas fa-<?php echo $has_shop_access ? 'check' : 'times'; ?>"></i>
                            Shop Access
                        </span>
                        <span class="permission-badge <?php echo $has_bisp_access ? 'active' : 'inactive'; ?>">
                            <i class="fas fa-<?php echo $has_bisp_access ? 'check' : 'times'; ?>"></i>
                            BISP Access
                        </span>
                        <span class="permission-badge <?php echo $has_loans_access ? 'active' : 'inactive'; ?>">
                            <i class="fas fa-<?php echo $has_loans_access ? 'check' : 'times'; ?>"></i>
                            Loans Access
                        </span>
                        <span class="permission-badge <?php echo $has_closing_access ? 'active' : 'inactive'; ?>">
                            <i class="fas fa-<?php echo $has_closing_access ? 'check' : 'times'; ?>"></i>
                            Closing Access
                        </span>
                        <span class="permission-badge <?php echo $has_hbl_access ? 'active' : 'inactive'; ?>">
                            <i class="fas fa-<?php echo $has_hbl_access ? 'check' : 'times'; ?>"></i>
                            HBL Access
                        </span>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Contact administrator for permission changes
                        </small>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Low Stock Alerts -->
                <?php if($has_shop_access || $is_admin): ?>
                <div class="glass-panel p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Low Stock Alerts
                        </h5>
                        <span class="badge bg-danger"><?php echo count($low_stock); ?></span>
                    </div>
                    
                    <?php if(count($low_stock) > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($low_stock as $ls): ?>
                            <div class="list-group-item px-0 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($ls['item_name']); ?></div>
                                        <small class="text-muted">Stock: <?php echo $ls['stock_qty']; ?> units</small>
                                    </div>
                                    <a href="purchases.php" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-plus"></i> Restock
                                    </a>
                                </div>
                                <div class="progress-custom mt-2">
                                    <div class="progress-bar bg-danger" 
                                         style="width: <?php echo min(($ls['stock_qty'] / 5) * 100, 100); ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-0">Inventory levels are good</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="inventory.php" class="text-decoration-none small">
                            <i class="fas fa-arrow-right me-1"></i> View Full Inventory
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Top Products (Shop Access Only) -->
                <?php if(($has_shop_access || $is_admin) && !empty($top_products)): ?>
                <div class="glass-panel p-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Today's Top Products
                    </h5>
                    <div class="list-group list-group-flush">
                        <?php foreach($top_products as $index => $product): ?>
                        <div class="list-group-item px-0 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">#<?php echo $index + 1; ?></span>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars(substr($product['description'], 0, 30)); ?></div>
                                        <small class="text-muted">Sold: <?php echo $product['count']; ?> times</small>
                                    </div>
                                </div>
                                <span class="fw-bold text-success">
                                    Rs. <?php echo number_format($product['revenue']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Pending Bills -->
                <div class="glass-panel p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-clock text-warning me-2"></i>Pending Bills
                        </h5>
                        <span class="badge bg-warning"><?php echo $pending_bills; ?></span>
                    </div>
                    
                    <?php if($pending_bills > 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong><?php echo $pending_bills; ?> bills</strong> are waiting in night queue
                        </div>
                        <div class="text-center">
                            <a href="night_mode.php" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-moon me-2"></i> Process Now
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-2 text-muted">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="mb-0">All bills are processed</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Stats -->
                <div class="glass-panel p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-chart-pie text-info me-2"></i>Quick Stats
                    </h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Today's Sales</small>
                                <div class="fw-bold text-success">Rs. <?php echo number_format($today_sale); ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Today's Expenses</small>
                                <div class="fw-bold text-danger">Rs. <?php echo number_format($today_expenses); ?></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Net Profit</small>
                                <div class="fw-bold <?php echo $net_profit_today >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    Rs. <?php echo number_format($net_profit_today); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">BVS Balance</small>
                                <div class="fw-bold text-primary">Rs. <?php echo number_format($bvs_balance); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin-only: Additional Stats -->
        <?php if($is_admin): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-chart-area me-2"></i>Business Analytics
                        </h2>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="metric-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="metric-card-content">
                                    <div class="metric-title">Total Customers</div>
                                    <div class="metric-value"><?php echo $total_customers; ?></div>
                                    <div class="metric-trend">
                                        <i class="fas fa-users"></i> Active accounts
                                    </div>
                                </div>
                                <i class="fas fa-users"></i>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="metric-card-content">
                                    <div class="metric-title">Active Loans</div>
                                    <div class="metric-value"><?php echo $active_loans; ?></div>
                                    <div class="metric-trend">
                                        <i class="fas fa-hand-holding-usd"></i> Installments
                                    </div>
                                </div>
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="metric-card-content">
                                    <div class="metric-title">Total Suppliers</div>
                                    <div class="metric-value"><?php echo $total_suppliers; ?></div>
                                    <div class="metric-trend">
                                        <i class="fas fa-truck"></i> Business partners
                                    </div>
                                </div>
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="metric-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <div class="metric-card-content">
                                    <div class="metric-title">Payables</div>
                                    <div class="metric-value">Rs. <?php echo number_format($payables); ?></div>
                                    <div class="metric-trend">
                                        <i class="fas fa-file-invoice-dollar"></i> Amount due
                                    </div>
                                </div>
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Top Customers with Debt -->
                    <?php if(!empty($top_customers)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="glass-panel p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-exclamation-circle text-danger me-2"></i>Top Customers with Outstanding Balance
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Balance Due</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($top_customers as $customer): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($customer['person_name']); ?></td>
                                                <td class="text-danger fw-bold">Rs. <?php echo number_format($customer['balance_due']); ?></td>
                                                <td>
                                                    <span class="badge bg-danger">Overdue</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="contacts.php" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-phone-alt me-1"></i> Follow Up
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-3 text-center text-muted">
        <div class="container">
            <small>
                <i class="fas fa-shield-alt me-1"></i> ARHAM ERP System v2.0 • 
                <?php echo date('Y'); ?> • 
                Logged in as: <?php echo $username; ?>
            </small>
        </div>
    </footer>

    <script>
        // Initialize Charts (Admin Only)
        <?php if($is_admin && !empty($chart_data)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('financialChart').getContext('2d');
            
            const labels = <?php echo json_encode(array_column($chart_data, 'date')); ?>;
            const incomeData = <?php echo json_encode(array_column($chart_data, 'income')); ?>;
            const expenseData = <?php echo json_encode(array_column($chart_data, 'expense')); ?>;
            
            const financialChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Income',
                            data: incomeData,
                            borderColor: '#2ecc71',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: expenseData,
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
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
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

        function updateChart(days) {
            // In a real implementation, you would fetch new data via AJAX
            console.log('Update chart for', days, 'days');
            // For now, just show a notification
            showNotification('Chart would update for ' + days + ' days', 'info');
        }
        <?php endif; ?>

        // Notification System
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `toast align-items-center text-white bg-${type} border-0`;
            notification.setAttribute('role', 'alert');
            notification.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            const container = document.getElementById('notificationContainer') || (() => {
                const div = document.createElement('div');
                div.id = 'notificationContainer';
                div.className = 'toast-container position-fixed top-0 end-0 p-3';
                div.style.zIndex = '9999';
                document.body.appendChild(div);
                return div;
            })();
            
            container.appendChild(notification);
            const bsToast = new bootstrap.Toast(notification);
            bsToast.show();
            
            notification.addEventListener('hidden.bs.toast', () => {
                notification.remove();
            });
        }

        // Auto-refresh data every 5 minutes (for real-time updates)
        setInterval(() => {
            // You can implement AJAX refresh here
            // For now, just update the time
            const now = new Date();
            document.querySelector('.greeting-time .fa-clock').parentElement.innerHTML = 
                `<i class="fas fa-clock me-2"></i>${now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
        }, 300000); // 5 minutes

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl + R to refresh
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                location.reload();
            }
            
            // Ctrl + S for new sale
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                window.location.href = 'shop_pos.php';
            }
            
            // Ctrl + B for new bill
            if (e.ctrlKey && e.key === 'b') {
                e.preventDefault();
                window.location.href = 'bills.php';
            }
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);

        // Observe all metric cards and sections
        document.querySelectorAll('.metric-card, .dashboard-section').forEach(el => {
            observer.observe(el);
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>

<?php include 'includes/footer.php'; ?>