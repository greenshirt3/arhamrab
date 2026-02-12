<?php 
// ==========================================
// 1. INITIALIZATION & SECURITY
// ==========================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load the system header (Connects to DB)
require 'includes/header.php'; 

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// CHECK PERMISSION (Admin or Shop/Bill Staff)
// We check if the user is an Admin to allow critical actions
$is_admin_user = false;

// Method 1: Check by Hardcoded IDs (Safest)
if (in_array($_SESSION['user_id'], [1, 14, 15])) {
    $is_admin_user = true;
}
// Method 2: Check by Role
elseif (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') {
    $is_admin_user = true;
}
// Method 3: Check by Permission Function (if exists)
elseif (function_exists('has_perm') && has_perm('admin')) {
    $is_admin_user = true;
}

// ==========================================
// 2. HANDLE ACTIONS (POST REQUESTS)
// ==========================================
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // SECURITY BLOCK: Only Admins can Pay or Delete
    if (!$is_admin_user) {
        die("<div class='container mt-5'><div class='alert alert-danger text-center shadow p-4'><h3>⛔ ACCESS DENIED</h3><p>Only Administrators can process payments or delete bills.</p><a href='night_mode.php' class='btn btn-dark'>Go Back</a></div></div>");
    }

    // --- ACTION A: PAY BILL ---
    if (isset($_POST['confirm_payment'])) {
        $id = $_POST['bill_id'];
        $tid = trim($_POST['tid']);
        
        if (empty($tid)) {
            $error = "Transaction ID (TID) is missing!";
        } else {
            // 1. Get Bill Info
            $stmt = $pdo->prepare("SELECT * FROM bill_queue WHERE id = ?");
            $stmt->execute([$id]);
            $bill = $stmt->fetch();

            if ($bill && $bill['status'] == 'pending') {
                try {
                    $pdo->beginTransaction();

                    // A. Update Bill Status
                    $stmt = $pdo->prepare("UPDATE bill_queue SET status='paid', transaction_id=?, created_at=NOW() WHERE id=?");
                    $stmt->execute([$tid, $id]);
                    
                    // B. Deduct Balance from HBL Device
                    // We use account_name because IDs might change, but names are usually stable
                    $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = 'HBL Konnect BVS'");
                    $stmt->execute([$bill['amount']]);
                    
                    // C. Add to Ledger
                    $desc = "Bill Paid: " . $bill['bill_type'] . " (" . $bill['consumer_number'] . ") TID: " . $tid;
                    $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head) VALUES (CURDATE(), 'expense', 'Utility Bill', ?, ?, 'Konnect', 'HBL Konnect BVS')");
                    $stmt->execute([$desc, $bill['amount']]);

                    // D. Audit Log (Optional, if table exists)
                    try {
                        $stmt = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address, created_at) VALUES (?, 'Pay Bill', ?, ?, NOW())");
                        $stmt->execute([$_SESSION['user_id'], "Paid Bill #$id ($bill[amount])", $_SERVER['REMOTE_ADDR']]);
                    } catch (Exception $logEx) {} // Ignore log errors

                    $pdo->commit();
                    $msg = "Bill Paid Successfully! TID: " . htmlspecialchars($tid);
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "System Error: " . $e->getMessage();
                }
            } else {
                $error = "Bill not found or already paid.";
            }
        }
    }

    // --- ACTION B: DELETE BILL ---
    if (isset($_POST['delete_bill'])) {
        $id = $_POST['delete_id'];
        $reason = trim($_POST['delete_reason']);
        
        $stmt = $pdo->prepare("SELECT * FROM bill_queue WHERE id = ?");
        $stmt->execute([$id]);
        $bill = $stmt->fetch();

        if ($bill && $bill['status'] == 'pending') {
            try {
                $pdo->beginTransaction();

                // A. Delete from Queue
                $pdo->prepare("DELETE FROM bill_queue WHERE id=?")->execute([$id]);
                
                // B. Refund Logic (Only if it was a CASH entry)
                if (isset($bill['payment_status']) && $bill['payment_status'] == 'cash') {
                    // Refund to Cash Drawer
                    $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name='Shop Cash Drawer'")->execute([$bill['amount']]);
                    
                    // Log Refund
                    $desc = "Refund: " . $bill['consumer_number'] . " ($reason)";
                    $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, account_head) VALUES (CURDATE(), 'expense', 'Refund', ?, ?, 'Shop Cash Drawer')")->execute([$desc, $bill['amount']]);
                }
                
                // C. Credit Logic (If it was a LOAN entry)
                if (isset($bill['payment_status']) && $bill['payment_status'] == 'credit' && !empty($bill['customer_id'])) {
                    // Reduce Customer Debt
                    $pdo->prepare("UPDATE loans SET total_amount = total_amount - ? WHERE id=?")->execute([$bill['amount'], $bill['customer_id']]);
                }

                $pdo->commit();
                $msg = "Bill Deleted and Financials Reversed.";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Delete Failed: " . $e->getMessage();
            }
        }
    }
}

// ==========================================
// 3. FETCH DATA FOR UI
// ==========================================

// A. Pending Bills
$pending_bills = $pdo->query("SELECT * FROM bill_queue WHERE status = 'pending' ORDER BY id ASC")->fetchAll();

// B. Paid History (Last 50)
$history_bills = $pdo->query("SELECT * FROM bill_queue WHERE status = 'paid' ORDER BY created_at DESC LIMIT 50")->fetchAll();

// C. Device Balance (Admin Only)
$bvs_bal = 0;
if ($is_admin_user) {
    try {
        $stmt = $pdo->prepare("SELECT current_balance FROM accounts WHERE account_name = 'HBL Konnect BVS'");
        $stmt->execute();
        $bvs_bal = $stmt->fetchColumn();
    } catch (Exception $e) { $bvs_bal = 0; }
}
?>

<div class="row mb-3">
    <?php if ($is_admin_user): ?>
    <div class="col-md-4">
        <div class="glass-panel p-3 bg-dark text-white border-warning border-start border-5 h-100 shadow-sm">
            <small class="text-white-50 fw-bold">DEVICE BALANCE</small>
            <h2 class="text-warning fw-bold my-1">Rs. <?php echo number_format($bvs_bal); ?></h2>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="<?php echo $is_admin_user ? 'col-md-8' : 'col-md-12'; ?>">
        <div class="glass-panel p-3 h-100 d-flex flex-column justify-content-center">
            <input type="text" id="tableSearch" class="form-control mb-2 border-primary" placeholder="🔍 Search by Reference No, Name, or TID...">
            
            <ul class="nav nav-pills nav-fill gap-2" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold border" data-bs-toggle="tab" data-bs-target="#tab-pending">
                        <i class="fas fa-clock me-2"></i> PENDING QUEUE (<?php echo count($pending_bills); ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold border" data-bs-toggle="tab" data-bs-target="#tab-history">
                        <i class="fas fa-history me-2"></i> PAYMENT HISTORY
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php if($msg): ?>
<div class="alert alert-success text-center fw-bold shadow-sm">
    <i class="fas fa-check-circle me-2"></i> <?php echo $msg; ?>
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger text-center fw-bold shadow-sm">
    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="tab-content">
    
    <div class="tab-pane fade show active" id="tab-pending">
        <div class="glass-panel p-0 overflow-hidden bg-white shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th class="ps-3">Reference / Type</th>
                            <th>Consumer Details</th>
                            <th>Amount</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="pendingBody">
                        <?php foreach($pending_bills as $b): ?>
                        <tr class="search-row">
                            <td class="ps-3">
                                <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($b['bill_type']); ?></span>
                                <div class="font-monospace fw-bold fs-5 select-all" id="ref-<?php echo $b['id']; ?>">
                                    <?php echo htmlspecialchars($b['consumer_number']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($b['consumer_name'] ?: 'Unknown'); ?></div>
                                <div class="small text-muted">
                                    <?php echo htmlspecialchars($b['mobile_no'] ?: '-'); ?>
                                </div>
                            </td>
                            <td class="fw-bold text-danger fs-5">Rs. <?php echo number_format($b['amount']); ?></td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-dark me-1" onclick="copyText('<?php echo $b['id']; ?>')" title="Copy Reference">
                                    <i class="fas fa-copy"></i>
                                </button>

                                <?php if ($is_admin_user): ?>
                                    <button class="btn btn-sm btn-success fw-bold shadow-sm me-1" onclick="openPayModal('<?php echo $b['id']; ?>', '<?php echo $b['amount']; ?>', '<?php echo $b['consumer_number']; ?>')">PAY</button>
                                    <button class="btn btn-sm btn-danger shadow-sm" onclick="openDeleteModal('<?php echo $b['id']; ?>', '<?php echo $b['consumer_number']; ?>')"><i class="fas fa-trash"></i></button>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border p-2"><i class="fas fa-lock"></i> Admin Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($pending_bills)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">No pending bills.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-history">
        <div class="glass-panel p-0 overflow-hidden bg-white shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th class="ps-3">Date Paid</th>
                            <th>Bill Details</th>
                            <th>TID</th>
                            <th class="text-end pe-3">Receipt / Share</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <?php foreach($history_bills as $b): 
                            // Prepare WhatsApp Number (0300 -> 92300)
                            $mobile = $b['mobile_no'] ?? '';
                            $clean_mobile = preg_replace('/^0/', '92', $mobile);
                            
                            $wa_msg = "*ARHAM PRINTERS RECEIPT*\n\n" .
                                      "Bill Type: " . $b['bill_type'] . "\n" .
                                      "Ref No: " . $b['consumer_number'] . "\n" .
                                      "Amount: Rs. " . number_format($b['amount']) . "\n" .
                                      "Status: PAID ✅\n" .
                                      "TID: " . $b['transaction_id'];
                            
                            $wa_link = "https://wa.me/" . $clean_mobile . "?text=" . urlencode($wa_msg);
                        ?>
                        <tr class="search-row">
                            <td class="ps-3 small text-muted">
                                <?php echo date('d-M h:i A', strtotime($b['created_at'])); ?>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($b['bill_type']); ?></div>
                                <div class="small text-muted font-monospace"><?php echo htmlspecialchars($b['consumer_number']); ?></div>
                                <div class="small text-primary"><?php echo htmlspecialchars($b['consumer_name']); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    <?php echo htmlspecialchars($b['transaction_id']); ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="invoice_bill.php?id=<?php echo $b['id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark fw-bold me-1">
                                    <i class="fas fa-print"></i>
                                </a>
                                
                                <?php if($mobile): ?>
                                <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-sm btn-success fw-bold">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled><i class="fab fa-whatsapp"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($is_admin_user): ?>

<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Confirm Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="confirm_payment" value="1">
                    <input type="hidden" name="bill_id" id="p_id">
                    
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-success" id="p_amt"></h2>
                        <div class="text-muted font-monospace" id="p_ref"></div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold small text-muted text-uppercase">Transaction ID (TID)</label>
                        <input type="text" name="tid" class="form-control form-control-lg fw-bold border-success font-monospace" placeholder="Enter TID from Device" required autocomplete="off">
                    </div>
                    
                    <button class="btn btn-success w-100 btn-lg fw-bold rounded-pill shadow-sm">SAVE & MARK PAID</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Delete Bill</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST">
                    <input type="hidden" name="delete_bill" value="1">
                    <input type="hidden" name="delete_id" id="d_id">
                    
                    <div class="alert alert-warning small border-warning">
                        <i class="fas fa-exclamation-triangle"></i> <b>Warning:</b> This removes the bill and refunds the cash (if applicable).
                    </div>
                    
                    <div class="mb-3 text-center">
                        <h5 class="fw-bold text-danger" id="d_ref"></h5>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small">Reason</label>
                        <input type="text" name="delete_reason" class="form-control" placeholder="e.g. Duplicate Entry" required>
                    </div>
                    
                    <button class="btn btn-danger w-100 fw-bold rounded-pill">CONFIRM DELETE</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Logic for Modals
var pModal = new bootstrap.Modal(document.getElementById('payModal'));
var dModal = new bootstrap.Modal(document.getElementById('deleteModal'));

function openPayModal(id, amount, ref) {
    document.getElementById('p_id').value = id;
    document.getElementById('p_amt').innerText = "Rs. " + parseInt(amount).toLocaleString();
    document.getElementById('p_ref').innerText = "Ref: " + ref;
    pModal.show();
}

function openDeleteModal(id, ref) {
    document.getElementById('d_id').value = id;
    document.getElementById('d_ref').innerText = "Deleting Ref: " + ref;
    dModal.show();
}
</script>
<?php endif; ?>

<script>
function copyText(id) {
    var text = document.getElementById("ref-" + id).innerText;
    navigator.clipboard.writeText(text);
}

// Search Filter
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let rows = document.querySelectorAll('.search-row');
    rows.forEach(row => {
        let txt = row.innerText;
        if (txt.toUpperCase().indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<style>
    .select-all { user-select: all; cursor: pointer; }
    .glass-panel { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; }
</style>

<?php include 'includes/footer.php'; ?>