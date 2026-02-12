<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');

require 'includes/header.php';

// ACCESS CONTROL
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has finance access
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_role = $stmt->fetchColumn();

if (!in_array($user_role, ['admin', 'finance_manager', 'accountant'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$error = "";
$success = "";

// Set expense type from URL
$expense_type = isset($_GET['type']) && $_GET['type'] == 'business' ? 'business' : 'home';
$page_title = $expense_type == 'business' ? 'Business Expenses' : 'Home Expenses';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ADD NEW EXPENSE
    if (isset($_POST['add_expense'])) {
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['description']);
        $payment_method = $_POST['payment_method'];
        $account_head = $_POST['account_head'];
        $expense_date = $_POST['expense_date'] ?: date('Y-m-d');
        $receipt_no = trim($_POST['receipt_no']);
        $vendor_name = trim($_POST['vendor_name']);
        $attachment_path = null;

        // Handle file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['attachment']['type'], $allowed_types) && 
                $_FILES['attachment']['size'] <= $max_size) {
                
                $file_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $file_name = 'expense_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                $upload_dir = 'uploads/expenses/';
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_dir . $file_name)) {
                    $attachment_path = $upload_dir . $file_name;
                }
            }
        }

        try {
            $pdo->beginTransaction();

            // Insert into expenses table
            $stmt = $pdo->prepare("INSERT INTO expenses (expense_date, category, description, amount, payment_method, account_head, expense_type, receipt_no, vendor_name, attachment_path, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$expense_date, $category, $description, $amount, $payment_method, $account_head, $expense_type, $receipt_no, $vendor_name, $attachment_path, $_SESSION['user_id']]);

            // Add to finance ledger
            $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head, reference_id, expense_type) VALUES (?, 'expense', ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$expense_date, $category, $description, $amount, $payment_method, $account_head, $pdo->lastInsertId(), $expense_type]);

            // Update account balance
            $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = ?");
            $stmt->execute([$amount, $account_head]);

            $pdo->commit();
            $success = "✅ Expense added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }

    // UPDATE EXPENSE
    if (isset($_POST['update_expense'])) {
        $expense_id = $_POST['expense_id'];
        $category = trim($_POST['category']);
        $amount = floatval($_POST['amount']);
        $description = trim($_POST['description']);
        $payment_method = $_POST['payment_method'];
        $account_head = $_POST['account_head'];
        $expense_date = $_POST['expense_date'];
        $receipt_no = trim($_POST['receipt_no']);
        $vendor_name = trim($_POST['vendor_name']);

        // Get old expense details
        $stmt = $pdo->prepare("SELECT amount, account_head FROM expenses WHERE id = ?");
        $stmt->execute([$expense_id]);
        $old_expense = $stmt->fetch();

        try {
            $pdo->beginTransaction();

            // Update expense
            $stmt = $pdo->prepare("UPDATE expenses SET expense_date = ?, category = ?, description = ?, amount = ?, payment_method = ?, account_head = ?, receipt_no = ?, vendor_name = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$expense_date, $category, $description, $amount, $payment_method, $account_head, $receipt_no, $vendor_name, $expense_id]);

            // Update ledger
            $stmt = $pdo->prepare("UPDATE finance_ledger SET trans_date = ?, category = ?, description = ?, amount = ?, payment_method = ?, account_head = ? WHERE reference_id = ? AND type = 'expense'");
            $stmt->execute([$expense_date, $category, $description, $amount, $payment_method, $account_head, $expense_id]);

            // Revert old account balance
            $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name = ?");
            $stmt->execute([$old_expense['amount'], $old_expense['account_head']]);

            // Apply new account balance
            $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = ?");
            $stmt->execute([$amount, $account_head]);

            $pdo->commit();
            $success = "✅ Expense updated successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }

    // DELETE EXPENSE
    if (isset($_POST['delete_expense'])) {
        $expense_id = $_POST['expense_id'];

        // Get expense details
        $stmt = $pdo->prepare("SELECT amount, account_head, attachment_path FROM expenses WHERE id = ?");
        $stmt->execute([$expense_id]);
        $expense = $stmt->fetch();

        try {
            $pdo->beginTransaction();

            // Delete from expenses
            $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
            $stmt->execute([$expense_id]);

            // Delete from ledger
            $stmt = $pdo->prepare("DELETE FROM finance_ledger WHERE reference_id = ? AND type = 'expense'");
            $stmt->execute([$expense_id]);

            // Revert account balance
            $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name = ?");
            $stmt->execute([$expense['amount'], $expense['account_head']]);

            // Delete attachment file if exists
            if ($expense['attachment_path'] && file_exists($expense['attachment_path'])) {
                unlink($expense['attachment_path']);
            }

            $pdo->commit();
            $success = "✅ Expense deleted successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }

    // BULK ACTION: Approve Selected
    if (isset($_POST['bulk_approve'])) {
        if (!empty($_POST['selected_expenses'])) {
            try {
                $pdo->beginTransaction();
                $placeholders = implode(',', array_fill(0, count($_POST['selected_expenses']), '?'));
                
                $stmt = $pdo->prepare("UPDATE expenses SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id IN ($placeholders)");
                $stmt->execute(array_merge([$_SESSION['user_id']], $_POST['selected_expenses']));
                
                $pdo->commit();
                $success = "✅ " . count($_POST['selected_expenses']) . " expenses approved!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error: " . $e->getMessage();
            }
        }
    }

    // BULK ACTION: Delete Selected
    if (isset($_POST['bulk_delete'])) {
        if (!empty($_POST['selected_expenses'])) {
            if (isset($_POST['confirm_bulk_delete']) && $_POST['confirm_bulk_delete'] == '1') {
                try {
                    $pdo->beginTransaction();
                    $placeholders = implode(',', array_fill(0, count($_POST['selected_expenses']), '?'));
                    
                    // Get amounts and account heads for reversal
                    $stmt = $pdo->prepare("SELECT amount, account_head FROM expenses WHERE id IN ($placeholders)");
                    $stmt->execute($_POST['selected_expenses']);
                    $expenses = $stmt->fetchAll();
                    
                    // Revert account balances
                    foreach ($expenses as $exp) {
                        $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name = ?");
                        $stmt->execute([$exp['amount'], $exp['account_head']]);
                    }
                    
                    // Delete expenses
                    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id IN ($placeholders)");
                    $stmt->execute($_POST['selected_expenses']);
                    
                    // Delete from ledger
                    $stmt = $pdo->prepare("DELETE FROM finance_ledger WHERE reference_id IN ($placeholders) AND type = 'expense'");
                    $stmt->execute($_POST['selected_expenses']);
                    
                    $pdo->commit();
                    $success = "✅ " . count($_POST['selected_expenses']) . " expenses deleted!";
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Error: " . $e->getMessage();
                }
            } else {
                $error = "Please confirm bulk deletion by checking the confirmation box.";
            }
        }
    }
}

// Get expense categories
$categories = $pdo->query("SELECT DISTINCT category FROM expenses WHERE expense_type = '$expense_type' ORDER BY category")->fetchAll();

// Get accounts
$accounts = $pdo->query("SELECT account_name, current_balance FROM accounts WHERE account_type IN ('cash', 'bank') ORDER BY account_name")->fetchAll();

// Get payment methods
$payment_methods = $pdo->query("SELECT DISTINCT payment_method FROM expenses ORDER BY payment_method")->fetchAll();

// Get filters
$filter_category = $_GET['category'] ?? '';
$filter_date_from = $_GET['date_from'] ?? date('Y-m-01');
$filter_date_to = $_GET['date_to'] ?? date('Y-m-d');
$filter_status = $_GET['status'] ?? '';
$filter_min_amount = $_GET['min_amount'] ?? '';
$filter_max_amount = $_GET['max_amount'] ?? '';
$filter_payment_method = $_GET['payment_method'] ?? '';

// Build query for expenses
$query = "SELECT e.*, u.username as created_by_name, ua.username as approved_by_name 
          FROM expenses e 
          LEFT JOIN users u ON e.created_by = u.id 
          LEFT JOIN users ua ON e.approved_by = ua.id 
          WHERE e.expense_type = :expense_type";
$params = [':expense_type' => $expense_type];

if ($filter_category) {
    $query .= " AND e.category = :category";
    $params[':category'] = $filter_category;
}
if ($filter_date_from) {
    $query .= " AND e.expense_date >= :date_from";
    $params[':date_from'] = $filter_date_from;
}
if ($filter_date_to) {
    $query .= " AND e.expense_date <= :date_to";
    $params[':date_to'] = $filter_date_to;
}
if ($filter_status) {
    $query .= " AND e.status = :status";
    $params[':status'] = $filter_status;
}
if ($filter_min_amount) {
    $query .= " AND e.amount >= :min_amount";
    $params[':min_amount'] = $filter_min_amount;
}
if ($filter_max_amount) {
    $query .= " AND e.amount <= :max_amount";
    $params[':max_amount'] = $filter_max_amount;
}
if ($filter_payment_method) {
    $query .= " AND e.payment_method = :payment_method";
    $params[':payment_method'] = $filter_payment_method;
}

$query .= " ORDER BY e.expense_date DESC, e.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$expenses = $stmt->fetchAll();

// Get statistics
$stats_query = "SELECT 
                COUNT(*) as total_count,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount,
                MIN(amount) as min_amount,
                MAX(amount) as max_amount,
                COUNT(DISTINCT category) as category_count
                FROM expenses 
                WHERE expense_type = :expense_type 
                AND expense_date BETWEEN :date_from AND :date_to";
$stats_params = [
    ':expense_type' => $expense_type,
    ':date_from' => $filter_date_from,
    ':date_to' => $filter_date_to
];

if ($filter_category) {
    $stats_query .= " AND category = :category";
    $stats_params[':category'] = $filter_category;
}

$stmt = $pdo->prepare($stats_query);
$stmt->execute($stats_params);
$stats = $stmt->fetch();

// Get monthly trend
$monthly_trend = $pdo->prepare("SELECT 
    DATE_FORMAT(expense_date, '%Y-%m') as month,
    SUM(amount) as total,
    COUNT(*) as count
    FROM expenses 
    WHERE expense_type = ? 
    AND expense_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6");
$monthly_trend->execute([$expense_type]);
$trend_data = $monthly_trend->fetchAll();

// Get top categories
$top_categories = $pdo->prepare("SELECT 
    category,
    SUM(amount) as total,
    COUNT(*) as count
    FROM expenses 
    WHERE expense_type = ? 
    AND expense_date BETWEEN ? AND ?
    GROUP BY category
    ORDER BY total DESC
    LIMIT 10");
$top_categories->execute([$expense_type, $filter_date_from, $filter_date_to]);
$categories_data = $top_categories->fetchAll();
?>

<div class="container-fluid py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-3">
                <h1 class="h2 fw-bold mb-0">
                    <i class="fas fa-home text-primary me-2"></i>
                    <?php echo $page_title; ?>
                </h1>
                <div class="ms-3">
                    <div class="btn-group">
                        <a href="expenses.php?type=home" class="btn btn-<?php echo $expense_type == 'home' ? 'primary' : 'outline-primary'; ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                        <a href="expenses.php?type=business" class="btn btn-<?php echo $expense_type == 'business' ? 'primary' : 'outline-primary'; ?>">
                            <i class="fas fa-briefcase"></i> Business
                        </a>
                    </div>
                </div>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="finance.php">Finance</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $page_title; ?></li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="fas fa-plus-circle me-2"></i> Add Expense
                </button>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
                <a href="reports.php?report=expenses&type=<?php echo $expense_type; ?>" class="btn btn-outline-info">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-1">Total Expenses</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_count'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-1">Total Amount</h6>
                            <h3 class="mb-0">Rs. <?php echo number_format($stats['total_amount'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-white-50 mb-1">Categories</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['category_count'] ?? 0); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-dark-50 mb-1">Avg. Expense</h6>
                            <h3 class="mb-0">Rs. <?php echo number_format($stats['avg_amount'] ?? 0, 2); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Insights -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Monthly Trend</h5>
                    <small class="text-muted">Last 6 months</small>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-success me-2"></i>Top Categories</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Expense Records</h5>
                </div>
                <div class="col-md-6 text-end">
                    <form method="POST" class="d-inline" onsubmit="return validateBulkAction()">
                        <input type="hidden" name="confirm_bulk_delete" id="confirmBulkDelete" value="0">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllRows()">
                                <i class="fas fa-check-square me-1"></i> Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAllRows()">
                                <i class="fas fa-square me-1"></i> Deselect All
                            </button>
                            <button type="submit" name="bulk_approve" class="btn btn-success btn-sm">
                                <i class="fas fa-check-circle me-1"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete()">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                            </th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Account</th>
                            <th>Status</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                        <h5>No expenses found</h5>
                                        <p class="text-muted">Add your first expense to get started</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                            <i class="fas fa-plus-circle me-2"></i> Add Expense
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_expenses[]" value="<?php echo $expense['id']; ?>" class="expense-checkbox">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo date('d M Y', strtotime($expense['expense_date'])); ?></div>
                                        <small class="text-muted"><?php echo date('h:i A', strtotime($expense['created_at'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($expense['category']); ?></span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;">
                                            <?php echo htmlspecialchars($expense['description']); ?>
                                        </div>
                                        <?php if ($expense['vendor_name']): ?>
                                            <small class="text-muted">Vendor: <?php echo htmlspecialchars($expense['vendor_name']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-danger">Rs. <?php echo number_format($expense['amount'], 2); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $expense['payment_method']; ?></span>
                                    </td>
                                    <td>
                                        <small><?php echo $expense['account_head']; ?></small>
                                    </td>
                                    <td>
                                        <?php if ($expense['status'] == 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($expense['status'] == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo $expense['status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($expense['receipt_no']): ?>
                                            <span class="badge bg-light text-dark">#<?php echo $expense['receipt_no']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($expense['attachment_path']): ?>
                                            <a href="<?php echo $expense['attachment_path']; ?>" target="_blank" class="ms-1" title="View Attachment">
                                                <i class="fas fa-paperclip text-primary"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="viewExpense(<?php echo $expense['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-primary" onclick="editExpense(<?php echo $expense['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user_role == 'admin'): ?>
                                                <button class="btn btn-outline-danger" onclick="deleteExpense(<?php echo $expense['id']; ?>, '<?php echo htmlspecialchars($expense['description']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Showing <?php echo count($expenses); ?> expenses
                        <?php if ($filter_date_from || $filter_date_to): ?>
                            from <?php echo date('d M Y', strtotime($filter_date_from)); ?> to <?php echo date('d M Y', strtotime($filter_date_to)); ?>
                        <?php endif; ?>
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <form method="GET" class="d-inline">
                        <input type="hidden" name="type" value="<?php echo $expense_type; ?>">
                        <input type="hidden" name="category" value="<?php echo $filter_category; ?>">
                        <input type="hidden" name="date_from" value="<?php echo $filter_date_from; ?>">
                        <input type="hidden" name="date_to" value="<?php echo $filter_date_to; ?>">
                        <button type="submit" name="export" value="csv" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </button>
                        <button type="submit" name="export" value="pdf" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Export PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary by Category -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-chart-bar text-info me-2"></i>Expense Summary by Category</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Total Amount</th>
                            <th>Average</th>
                            <th>% of Total</th>
                            <th>Last Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories_data as $cat): ?>
                            <?php 
                            $percentage = $stats['total_amount'] > 0 ? ($cat['total'] / $stats['total_amount']) * 100 : 0;
                            $last_expense = $pdo->prepare("SELECT expense_date FROM expenses WHERE category = ? AND expense_type = ? ORDER BY expense_date DESC LIMIT 1");
                            $last_expense->execute([$cat['category'], $expense_type]);
                            $last_date = $last_expense->fetchColumn();
                            ?>
                            <tr>
                                <td><span class="badge bg-info"><?php echo $cat['category']; ?></span></td>
                                <td><?php echo $cat['count']; ?></td>
                                <td class="fw-bold text-danger">Rs. <?php echo number_format($cat['total'], 2); ?></td>
                                <td>Rs. <?php echo number_format($cat['total'] / $cat['count'], 2); ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%" 
                                             aria-valuenow="<?php echo $percentage; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?php echo number_format($percentage, 1); ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $last_date ? date('d M Y', strtotime($last_date)) : 'N/A'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Add New Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Groceries">Groceries</option>
                                <option value="Utilities">Utilities (Electricity, Water, Gas)</option>
                                <option value="Rent/Mortgage">Rent/Mortgage</option>
                                <option value="Transportation">Transportation</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Entertainment">Entertainment</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Food & Dining">Food & Dining</option>
                                <option value="Maintenance">Home Maintenance</option>
                                <option value="Insurance">Insurance</option>
                                <option value="Personal Care">Personal Care</option>
                                <option value="Gifts/Donations">Gifts/Donations</option>
                                <option value="Travel">Travel</option>
                                <option value="Subscriptions">Subscriptions</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Mobile Payment">Mobile Payment</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account Head <span class="text-danger">*</span></label>
                            <select name="account_head" class="form-select" required>
                                <?php foreach ($accounts as $account): ?>
                                    <option value="<?php echo $account['account_name']; ?>">
                                        <?php echo $account['account_name']; ?> (Rs. <?php echo number_format($account['current_balance']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Receipt/Bill Number</label>
                            <input type="text" name="receipt_no" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vendor/Shop Name</label>
                            <input type="text" name="vendor_name" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attachment (Bill/Receipt)</label>
                            <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                            <small class="text-muted">Max 5MB - JPG, PNG, GIF, PDF</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="Enter expense details..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="expense_type" value="<?php echo $expense_type; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_expense" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Expenses</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET">
                <div class="modal-body">
                    <input type="hidden" name="type" value="<?php echo $expense_type; ?>">
                    <div class="mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" name="date_from" class="form-control" value="<?php echo $filter_date_from; ?>">
                            </div>
                            <div class="col-6">
                                <input type="date" name="date_to" class="form-control" value="<?php echo $filter_date_to; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['category']; ?>" <?php echo $filter_category == $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['category']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <?php foreach ($payment_methods as $pm): ?>
                                <option value="<?php echo $pm['payment_method']; ?>" <?php echo $filter_payment_method == $pm['payment_method'] ? 'selected' : ''; ?>>
                                    <?php echo $pm['payment_method']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="approved" <?php echo $filter_status == 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $filter_status == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Range (Rs.)</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_amount" class="form-control" placeholder="Min" value="<?php echo $filter_min_amount; ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_amount" class="form-control" placeholder="Max" value="<?php echo $filter_max_amount; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="expenses.php?type=<?php echo $expense_type; ?>" class="btn btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($trend_data, 'month')); ?>,
            datasets: [{
                label: 'Expense Amount',
                data: <?php echo json_encode(array_column($trend_data, 'total')); ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($categories_data, 'category')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($categories_data, 'total')); ?>,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                    '#17a2b8', '#fd7e14', '#20c997', '#e83e8c', '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
});

// Bulk Actions Functions
function toggleSelectAll(source) {
    const checkboxes = document.getElementsByClassName('expense-checkbox');
    for (let checkbox of checkboxes) {
        checkbox.checked = source.checked;
    }
}

function selectAllRows() {
    const checkboxes = document.getElementsByClassName('expense-checkbox');
    for (let checkbox of checkboxes) {
        checkbox.checked = true;
    }
    document.getElementById('selectAll').checked = true;
}

function deselectAllRows() {
    const checkboxes = document.getElementsByClassName('expense-checkbox');
    for (let checkbox of checkboxes) {
        checkbox.checked = false;
    }
    document.getElementById('selectAll').checked = false;
}

function confirmBulkDelete() {
    const selected = document.querySelectorAll('.expense-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select expenses to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selected.length} expense(s)? This action cannot be undone.`)) {
        document.getElementById('confirmBulkDelete').value = '1';
        document.querySelector('form').submit();
    }
}

function validateBulkAction() {
    const selected = document.querySelectorAll('.expense-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one expense.');
        return false;
    }
    return true;
}

// Expense CRUD Functions
function viewExpense(id) {
    // Load expense details via AJAX and show in modal
    fetch(`ajax/get_expense.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Create and show view modal
            showExpenseModal(data, 'view');
        });
}

function editExpense(id) {
    fetch(`ajax/get_expense.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Create and show edit modal
            showExpenseModal(data, 'edit');
        });
}

function deleteExpense(id, description) {
    if (confirm(`Are you sure you want to delete expense: "${description}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'expense_id';
        inputId.value = id;
        
        const inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'delete_expense';
        inputAction.value = '1';
        
        form.appendChild(inputId);
        form.appendChild(inputAction);
        document.body.appendChild(form);
        form.submit();
    }
}

function showExpenseModal(data, mode) {
    // Create modal dynamically
    const modalId = 'expenseDetailModal';
    let modal = document.getElementById(modalId);
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Expense Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="expenseModalBody"></div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // Populate modal content based on mode
    const modalBody = modal.querySelector('#expenseModalBody');
    
    if (mode === 'view') {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Date</label>
                    <div class="fw-bold">${data.expense_date}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Amount</label>
                    <div class="fw-bold text-danger">Rs. ${parseFloat(data.amount).toLocaleString()}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Category</label>
                    <div><span class="badge bg-info">${data.category}</span></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Payment Method</label>
                    <div><span class="badge bg-secondary">${data.payment_method}</span></div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label text-muted">Description</label>
                    <div class="fw-bold">${data.description}</div>
                </div>
                ${data.vendor_name ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Vendor</label>
                    <div>${data.vendor_name}</div>
                </div>` : ''}
                ${data.receipt_no ? `
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Receipt No.</label>
                    <div>${data.receipt_no}</div>
                </div>` : ''}
                ${data.attachment_path ? `
                <div class="col-12 mb-3">
                    <label class="form-label text-muted">Attachment</label>
                    <div>
                        <a href="${data.attachment_path}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-paperclip me-1"></i> View Attachment
                        </a>
                    </div>
                </div>` : ''}
            </div>
        `;
    }
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Quick add category
function quickAddCategory() {
    const category = prompt('Enter new category name:');
    if (category && category.trim() !== '') {
        // You would typically save this via AJAX
        const select = document.querySelector('select[name="category"]');
        const option = document.createElement('option');
        option.value = category;
        option.textContent = category;
        select.appendChild(option);
        select.value = category;
    }
}

// Export functions
function exportToCSV() {
    // Implement CSV export
    alert('CSV export feature would be implemented here');
}

function exportToPDF() {
    // Implement PDF export
    alert('PDF export feature would be implemented here');
}

// Quick stats update on date change
function updateQuickStats() {
    const dateFrom = document.querySelector('[name="date_from"]').value;
    const dateTo = document.querySelector('[name="date_to"]').value;
    
    // You would typically fetch updated stats via AJAX
    console.log('Fetching stats for:', dateFrom, 'to', dateTo);
}
</script>

<style>
.stat-card {
    border-radius: 10px;
    transition: transform 0.3s ease;
    border: none;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.stat-icon {
    opacity: 0.8;
}
.empty-state {
    text-align: center;
    padding: 40px 20px;
}
.empty-state i {
    font-size: 3rem;
    margin-bottom: 20px;
}
.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.table td {
    vertical-align: middle;
}
.chart-container {
    position: relative;
}
.progress {
    border-radius: 3px;
}
.progress-bar {
    font-size: 0.75rem;
    line-height: 20px;
}
.badge {
    font-weight: 500;
}
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<?php include 'includes/footer.php'; ?>