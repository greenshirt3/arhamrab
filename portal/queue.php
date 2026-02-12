<?php
require 'includes/header.php';

// ACCESS
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// GENERATE TOKEN
$msg = "";
$print_token = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cnic = $_POST['cnic'];
    $type = $_POST['type'];
    
    // Get next token number for today
    $stmt = $pdo->prepare("SELECT MAX(token_number) FROM queue_tokens WHERE DATE(issued_at) = CURDATE()");
    $stmt->execute();
    $next = ($stmt->fetchColumn() ?: 0) + 1;
    
    // Insert
    $stmt = $pdo->prepare("INSERT INTO queue_tokens (token_number, cnic, service_type) VALUES (?, ?, ?)");
    $stmt->execute([$next, $cnic, $type]);
    
    // Save/Update Beneficiary
    $chk = $pdo->prepare("SELECT id FROM beneficiaries WHERE cnic = ?");
    $chk->execute([$cnic]);
    if (!$chk->fetch()) {
        $pdo->prepare("INSERT INTO beneficiaries (cnic, last_visit) VALUES (?, CURDATE())")->execute([$cnic]);
    }

    $print_token = $next;
    $msg = "Token #$next Issued!";
}

// Current Queue
$waiting = $pdo->query("SELECT * FROM queue_tokens WHERE status='waiting' ORDER BY id ASC")->fetchAll();
?>

<div class="row">
    <div class="col-md-5">
        <div class="glass-panel p-4 border-start border-5 border-primary">
            <h4 class="fw-bold mb-3"><i class="fas fa-ticket-alt"></i> Issue Token</h4>
            
            <?php if($print_token): ?>
            <div class="alert alert-success text-center py-4 mb-4">
                <h6 class="text-uppercase text-muted">Token Number</h6>
                <h1 class="display-1 fw-bold"><?php echo $print_token; ?></h1>
                <button onclick="window.print()" class="btn btn-sm btn-outline-dark no-print">Print Slip</button>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="fw-bold">CNIC / Mobile</label>
                    <input type="text" name="cnic" class="form-control form-control-lg fw-bold" placeholder="Enter ID" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="fw-bold">Service</label>
                    <select name="type" class="form-select form-select-lg">
                        <option value="bisp">BISP Cashout</option>
                        <option value="bills">Bill Payment</option>
                        <option value="other">Other Inquiry</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100 btn-lg fw-bold rounded-pill">GENERATE TOKEN</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="glass-panel p-0 overflow-hidden">
            <div class="p-3 bg-dark text-white d-flex justify-content-between">
                <h5 class="m-0">Waiting Queue</h5>
                <span class="badge bg-warning text-dark"><?php echo count($waiting); ?> Pending</span>
            </div>
            <table class="table table-striped mb-0 align-middle">
                <thead><tr><th>Token</th><th>CNIC</th><th>Service</th><th>Time</th></tr></thead>
                <tbody>
                    <?php foreach($waiting as $w): ?>
                    <tr>
                        <td><span class="badge bg-primary fs-5"><?php echo $w['token_number']; ?></span></td>
                        <td><?php echo $w['cnic']; ?></td>
                        <td><span class="badge bg-secondary text-uppercase"><?php echo $w['service_type']; ?></span></td>
                        <td class="small text-muted"><?php echo date('h:i A', strtotime($w['issued_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($waiting)): ?><tr><td colspan="4" class="text-center py-4">No one is waiting.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>