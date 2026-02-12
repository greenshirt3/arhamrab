<?php 
require 'includes/header.php'; 

// ACCESS CONTROL
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (!has_perm('admin') && !has_perm('bisp')) {
    die("<div class='alert alert-danger m-5 text-center'>⛔ You do not have permission to access BISP Portal.</div>");
}

$msg = "";
$error = "";

// --- ACTION 1: ISSUE TOKEN ---
if (isset($_POST['issue_token'])) {
    $cnic = trim($_POST['cnic']);
    if (strlen($cnic) < 13) {
        $error = "Invalid CNIC";
    } else {
        try {
            // Check if already in queue today
            $stmt = $pdo->prepare("SELECT id FROM queue_tokens WHERE cnic = ? AND DATE(issued_at) = CURDATE() AND status='waiting'");
            $stmt->execute([$cnic]);
            if ($stmt->fetch()) {
                $error = "This CNIC is already in the waiting line!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO queue_tokens (cnic, status, issued_at) VALUES (?, 'waiting', NOW())");
                $stmt->execute([$cnic]);
                $token_id = $pdo->lastInsertId();
                $msg = "Token #$token_id Issued Successfully!";
            }
        } catch (Exception $e) { $error = $e->getMessage(); }
    }
}

// --- ACTION 2: PROCESS PAYOUT ---
if (isset($_POST['process_payout'])) {
    $token_id = $_POST['token_id'];
    $amount = (float)$_POST['amount'];
    $trx_id = $_POST['trx_id'];

    if ($amount <= 0 || empty($trx_id)) {
        $error = "Invalid Amount or Transaction ID";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Mark Token Served
            $pdo->prepare("UPDATE queue_tokens SET status='served', served_at=NOW() WHERE id=?")->execute([$token_id]);

            // 2. Add to Ledger (Income from Commission?) 
            // Note: Usually BISP payouts don't deduct shop cash, they add to it (if agent receives cash from bank) 
            // OR they deduct from BVS Balance. Assuming BVS Deduction here:
            
            // Deduct from BVS
            $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name='HBL Konnect BVS'")->execute([$amount]);

            // Add Cash to Shop Drawer (Agent gets cash from BVS to give to customer)
            $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name='Shop Cash Drawer'")->execute([$amount]);

            $pdo->commit();
            $msg = "Payout Successful! Cash Added to Drawer.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Payout Failed: " . $e->getMessage();
        }
    }
}

// FETCH QUEUE
$queue = $pdo->query("SELECT * FROM queue_tokens WHERE status='waiting' ORDER BY id ASC")->fetchAll();
?>

<div class="row">
    <div class="col-md-4">
        <div class="glass-panel p-4 mb-3">
            <h5 class="fw-bold"><i class="fas fa-ticket-alt me-2"></i> Issue Token</h5>
            <?php if($msg) echo "<div class='alert alert-success small'>$msg</div>"; ?>
            <?php if($error) echo "<div class='alert alert-danger small'>$error</div>"; ?>
            
            <form method="POST">
                <input type="hidden" name="issue_token" value="1">
                <input type="text" name="cnic" class="form-control mb-3 fw-bold" placeholder="Enter CNIC (No Dashes)" required maxlength="13">
                <button class="btn btn-primary w-100 fw-bold">PRINT TOKEN</button>
            </form>
        </div>

        <div class="glass-panel p-0 overflow-hidden">
            <div class="p-3 bg-dark text-white fw-bold">Waiting Queue (<?php echo count($queue); ?>)</div>
            <div style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover mb-0 small">
                    <?php foreach($queue as $q): ?>
                    <tr>
                        <td class="ps-3 fw-bold">#<?php echo $q['id']; ?></td>
                        <td><?php echo $q['cnic']; ?></td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-success py-0" onclick="selectToken(<?php echo $q['id']; ?>, '<?php echo $q['cnic']; ?>')">Select</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="glass-panel p-4 h-100 bg-white">
            <h4 class="fw-bold border-bottom pb-3 mb-4">Process Payout</h4>
            
            <form method="POST" id="payoutForm">
                <input type="hidden" name="process_payout" value="1">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">Token ID</label>
                        <input type="text" name="token_id" id="sel_token" class="form-control bg-light" readonly required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">Beneficiary CNIC</label>
                        <input type="text" id="sel_cnic" class="form-control bg-light" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">Amount (Rs)</label>
                        <input type="number" name="amount" class="form-control form-control-lg fw-bold text-success" placeholder="10500" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">Transaction ID (TID)</label>
                        <input type="text" name="trx_id" class="form-control form-control-lg fw-bold" placeholder="From Device" required>
                    </div>
                </div>

                <button class="btn btn-success btn-lg w-100 fw-bold mt-3">CONFIRM PAYOUT</button>
            </form>
            
            <div class="alert alert-info mt-4 small">
                <i class="fas fa-info-circle"></i> <b>Note:</b> Confirming payout will deduct balance from <b>HBL BVS</b> and add it to <b>Shop Cash Drawer</b> (assuming cash withdrawal).
            </div>
        </div>
    </div>
</div>

<script>
function selectToken(id, cnic) {
    document.getElementById('sel_token').value = id;
    document.getElementById('sel_cnic').value = cnic;
}
</script>

<?php include 'includes/footer.php'; ?>