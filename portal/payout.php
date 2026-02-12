<?php 
// 1. ENABLE ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'includes/header.php'; 

// 2. ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Permission Check (Admin OR BISP Permission)
$allowed_ids = [1, 14, 15]; 
$is_admin = (in_array($_SESSION['user_id'], $allowed_ids) || strtolower($_SESSION['role']) === 'admin');
$has_perm = (isset($_SESSION['permissions']['bisp']) && $_SESSION['permissions']['bisp'] == 1);

if (!$is_admin && !$has_perm) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow fw-bold'>⛔ ACCESS DENIED</div></div>");
}

// 3. HANDLE PAYOUT
$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payout'])) {
    $token_id = $_POST['token_id'];
    $cnic = $_POST['cnic'];
    $amount = (float) $_POST['amount'];
    $deduction = (float) ($_POST['deduction'] ?: 0); 
    
    // Validate
    if ($amount <= 0) {
        $error = "Invalid Amount.";
    } else {
        try {
            $pdo->beginTransaction();

            $final_payout = $amount - $deduction;
            
            // A. Mark Token Served (if applicable)
            if($token_id) {
                $pdo->prepare("UPDATE queue_tokens SET status='served', served_at=NOW() WHERE id=?")->execute([$token_id]);
            }

            // B. Record Payout Expense (Money leaving shop)
            // We record the full amount as payout, or net? 
            // Standard accounting: You paid 'Final Payout' from cash. 
            // The 'Deduction' is revenue you KEPT (didn't leave drawer).
            
            $desc = "BISP Payout ($cnic)";
            if ($deduction > 0) $desc .= " - Fee: $deduction";

            // Entry 1: The Expense (Actual Cash Given)
            $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head) VALUES (NOW(), 'expense', 'BISP', ?, ?, 'Cash', 'Shop Cash Drawer')");
            $stmt->execute([$desc, $final_payout]);
            
            // Entry 2: The Income (Commission/Deduction) - ONLY if you want to track it as 'Income' separately. 
            // Actually, if you kept the fee, you simply didn't pay it out. 
            // But to track "Profit" from BISP, we can record the fee as Income.
            if ($deduction > 0) {
                $stmt = $pdo->prepare("INSERT INTO finance_ledger (trans_date, type, category, description, amount, payment_method, account_head) VALUES (NOW(), 'income', 'Commission', ?, ?, 'Cash', 'Shop Cash Drawer')");
                $stmt->execute(["BISP Fee ($cnic)", $deduction]);
                
                // Add Fee to Cash Drawer (It technically stayed in, but if we treat 'Amount' as full withdrawal logic, this balances it. 
                // Simpler logic: Just deduct Final Payout from Drawer.)
            }
            
            // C. Update Cash Drawer (Subtract what physically left)
            $stmt = $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name='Shop Cash Drawer'");
            $stmt->execute([$final_payout]);

            // D. Update Beneficiary History
            $chk = $pdo->prepare("SELECT id FROM beneficiaries WHERE cnic = ?");
            $chk->execute([$cnic]);
            if ($row = $chk->fetch()) {
                $pdo->prepare("UPDATE beneficiaries SET last_visit = CURDATE() WHERE id = ?")->execute([$row['id']]);
            } else {
                $pdo->prepare("INSERT INTO beneficiaries (cnic, last_visit) VALUES (?, CURDATE())")->execute([$cnic]);
            }

            $pdo->commit();
            $msg = "Payout Successful! Cash Given: Rs. " . number_format($final_payout);
            
            // Refresh logic to clear form
            $next = null; 

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Transaction Failed: " . $e->getMessage();
        }
    }
}

// 4. GET NEXT TOKEN (Queue Logic)
// Fetch the oldest 'waiting' token for BISP
$next = $pdo->query("SELECT * FROM queue_tokens WHERE status='waiting' AND service_type='bisp' ORDER BY id ASC LIMIT 1")->fetch();

// If no queue, allow manual entry? Yes, form handles empty token_id.
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel p-4 border-top border-5 border-success bg-white shadow-sm">
            <h3 class="fw-bold mb-3 text-center text-dark">
                <i class="fas fa-hand-holding-usd text-success me-2"></i> Cash Payout
            </h3>
            
            <?php if($msg): ?>
                <div class="alert alert-success fw-bold text-center shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-danger fw-bold text-center shadow-sm">
                    <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="card bg-light mb-4 text-center border-0 shadow-inner">
                <div class="card-body py-4">
                    <small class="text-muted fw-bold text-uppercase ls-1">Current Token</small>
                    <?php if($next): ?>
                        <h1 class="display-3 fw-bold text-primary mb-0 mt-2"><?php echo $next['token_number']; ?></h1>
                        <div class="text-muted fw-bold fs-5 mt-1">
                            <i class="fas fa-id-card me-1"></i> <?php echo $next['cnic']; ?>
                        </div>
                    <?php else: ?>
                        <h2 class="text-muted py-3">Queue is Empty</h2>
                        <small class="text-muted">You can process a manual payout below.</small>
                    <?php endif; ?>
                </div>
            </div>

            <form method="POST" autocomplete="off">
                <input type="hidden" name="process_payout" value="1">
                <input type="hidden" name="token_id" value="<?php echo $next['id'] ?? ''; ?>">
                
                <?php if(!$next): ?>
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Beneficiary CNIC</label>
                    <input type="text" name="cnic" class="form-control fw-bold" placeholder="00000-0000000-0" required>
                </div>
                <?php else: ?>
                <input type="hidden" name="cnic" value="<?php echo $next['cnic']; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Amount Withdrawn</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold">Rs.</span>
                        <input type="number" name="amount" id="amt" class="form-control form-control-lg fw-bold" placeholder="e.g. 10500" required oninput="calc()">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold small text-muted">Deduction / Fee</label>
                    <div class="input-group">
                        <span class="input-group-text fw-bold text-danger">-</span>
                        <input type="number" name="deduction" id="fee" class="form-control fw-bold text-danger" value="0" oninput="calc()">
                    </div>
                </div>

                <div class="alert alert-warning text-center fw-bold fs-4 shadow-sm border-warning">
                    Handover: Rs. <span id="final">0</span>
                </div>

                <button class="btn btn-success w-100 btn-lg rounded-pill fw-bold shadow hover-effect">
                    <i class="fas fa-check me-2"></i> CONFIRM PAYOUT
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function calc() {
    let amt = parseFloat(document.getElementById('amt').value) || 0;
    let fee = parseFloat(document.getElementById('fee').value) || 0;
    let final = amt - fee;
    document.getElementById('final').innerText = final.toLocaleString();
}
</script>

<?php include 'includes/footer.php'; ?>