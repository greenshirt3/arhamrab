<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/header.php';

// 1. ACCESS CONTROL (Admin Only)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Master Admin Check
$allowed_ids = [1, 14, 15];
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');

if (!$is_admin) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED</div></div>");
}

// 2. HANDLE ACTIONS
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // A. ADD NEW ACCOUNT
    if (isset($_POST['add_account'])) {
        $name = trim($_POST['name']);
        $type = $_POST['type']; // cash, bank, mobile_wallet
        $balance = $_POST['balance'];

        if (empty($name)) {
            $error = "Account Name is required.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO accounts (account_name, account_type, current_balance) VALUES (?, ?, ?)");
                $stmt->execute([$name, $type, $balance]);
                $msg = "Account created successfully!";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }

    // B. UPDATE BALANCE (Manual Adjustment)
    if (isset($_POST['update_balance'])) {
        $id = $_POST['account_id'];
        $new_balance = $_POST['new_balance'];
        $reason = trim($_POST['reason']);

        if (empty($reason)) {
            $error = "Reason is required for audit logs.";
        } else {
            try {
                // Get old balance for logging
                $stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE id = ?");
                $stmt->execute([$id]);
                $old_balance = (float) $stmt->fetchColumn();

                $difference = $new_balance - $old_balance;

                // Update Account
                $stmt = $pdo->prepare("UPDATE accounts SET current_balance = ? WHERE id = ?");
                $stmt->execute([$new_balance, $id]);

                // Log to Ledger
                $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, account_head) VALUES (CURDATE(), 'adjustment', 'Manual Update', ?, ?, ?)");
                $desc = "Adjustment: " . $reason;
                $acc_ref = "Account ID: " . $id;
                $stmt->execute([$desc, $difference, $acc_ref]);

                $msg = "Balance updated successfully!";
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// 3. FETCH ACCOUNTS
$accounts = $pdo->query("SELECT * FROM accounts ORDER BY id ASC")->fetchAll();
?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h3 class="fw-bold"><i class="fas fa-university text-primary"></i> Banking & Cash Accounts</h3>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow" onclick="openAddModal()">
            <i class="fas fa-plus me-2"></i> Add Account
        </button>
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert alert-success fw-bold"><?php echo $msg; ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger fw-bold"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row g-4">
    <?php foreach ($accounts as $acc): ?>
        <div class="col-md-4">
            <div class="glass-panel p-4 border-start border-5 border-primary position-relative bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($acc['account_name']); ?></h5>
                        <span class="badge bg-light text-dark border">
                            <?php echo strtoupper($acc['account_type']); ?>
                        </span>
                    </div>
                    <small class="text-muted">
                        Last Update: <?php echo date('d M Y, h:i A', strtotime($acc['last_updated'])); ?>
                    </small>
                    
                    <h2 class="fw-bold text-primary mt-3 mb-3">
                        Rs. <?php echo number_format($acc['current_balance']); ?>
                    </h2>
                </div>

                <button class="btn btn-sm btn-outline-dark w-100 fw-bold mt-3" onclick='openEditModal(<?php echo json_encode($acc); ?>)'>
                    <i class="fas fa-edit me-1"></i> Adjust Balance
                </button>
            </div>
        </div>
    <?php endforeach; ?>
    
    <?php if (empty($accounts)): ?>
        <div class="col-12">
            <div class="alert alert-warning text-center">
                No accounts found. Please add "Shop Cash Drawer" first.
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="add_account" value="1">
                    
                    <div class="mb-3">
                        <label class="fw-bold small">Account Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Shop Cash Drawer" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold small">Account Type</label>
                        <select name="type" class="form-select">
                            <option value="cash">Cash Drawer</option>
                            <option value="bank">Bank Account</option>
                            <option value="mobile_wallet">EasyPaisa / JazzCash</option>
                            <option value="device">POS Device</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold small">Opening Balance</label>
                        <input type="number" name="balance" class="form-control" value="0" required>
                    </div>
                    
                    <button class="btn btn-primary w-100 fw-bold py-2">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">Adjust Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="update_balance" value="1">
                    <input type="hidden" name="account_id" id="edit_id">
                    
                    <div class="text-center mb-4">
                        <h5 id="edit_name" class="fw-bold mb-1"></h5>
                        <small class="text-muted">Enter the actual amount currently present.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold small text-success">New Actual Balance</label>
                        <input type="number" name="new_balance" id="edit_balance" class="form-control fw-bold fs-4 text-center border-success" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold small">Reason for Change</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Calculation Correction, Theft, Found Cash" required>
                    </div>
                    
                    <button class="btn btn-warning w-100 fw-bold py-2">Update Balance</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var addModal = new bootstrap.Modal(document.getElementById('addModal'));
    var editModal = new bootstrap.Modal(document.getElementById('editModal'));

    function openAddModal() {
        addModal.show();
    }

    function openEditModal(acc) {
        document.getElementById('edit_id').value = acc.id;
        document.getElementById('edit_name').innerText = acc.account_name;
        document.getElementById('edit_balance').value = acc.current_balance;
        editModal.show();
    }
</script>

<?php include 'includes/footer.php'; ?>