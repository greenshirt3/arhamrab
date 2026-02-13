<?php require 'includes/header.php'; 
// SECURITY: ADMIN ONLY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

// HANDLE PAYOUT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cnic = $_POST['cnic'];
    $amount = str_replace(',', '', $_POST['amount']);
    $trx_id = $_POST['konnect_trx_id'];
    $agent_id = $_SESSION['user_id'];
    
    // 1. Get/Create Beneficiary
    $stmt = $pdo->prepare("SELECT id FROM beneficiaries WHERE cnic = ?");
    $stmt->execute([$cnic]);
    $ben_id = $stmt->fetchColumn();

    if (!$ben_id) {
        $pdo->prepare("INSERT INTO beneficiaries (cnic, name, phone) VALUES (?, ?, ?)")->execute([$cnic, $_POST['name'], $_POST['phone']]);
        $ben_id = $pdo->lastInsertId();
    }

    // 2. Record Transaction
    $sql = "INSERT INTO transactions (beneficiary_id, agent_id, amount, income, konnect_trx_id, status) VALUES (?, ?, ?, ?, ?, 'success')";
    $pdo->prepare($sql)->execute([$ben_id, $agent_id, $amount, 20, $trx_id]); // 20 is fixed commission
    $txn_id = $pdo->lastInsertId();

    // 3. Close Token if linked
    if (!empty($_POST['token_num'])) {
        $pdo->prepare("UPDATE queue_tokens SET status='served', served_at=NOW() WHERE token_number = ? AND DATE(issued_at)=CURDATE()")->execute([$_POST['token_num']]);
    }

    echo "<script>window.open('print_receipt.php?id=$txn_id', '_blank'); window.location.href='payout.php';</script>";
    exit();
}

// Auto-fill from URL
$pre_cnic = $_GET['cnic'] ?? '';
?>

<link rel="stylesheet" href="css/modern.css">

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="glass-panel p-5">
            <h3 class="fw-bold mb-4 text-center"><i class="fas fa-hand-holding-usd text-success"></i> Cash Disbursement</h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">CNIC Number</label>
                        <input type="text" id="cnic" name="cnic" class="form-control form-control-lg fw-bold" placeholder="35202-xxxxxxx-x" value="<?php echo $pre_cnic; ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Token # (Optional)</label>
                        <input type="text" id="token" name="token_num" class="form-control form-control-lg text-center" placeholder="e.g. 05-102">
                    </div>

                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Beneficiary Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Phone</label>
                        <input type="text" id="phone" name="phone" class="form-control">
                    </div>

                    <div class="col-12"><hr class="my-2"></div>

                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Amount (PKR)</label>
                        <input type="number" name="amount" class="form-control form-control-lg fw-bold text-success" value="10500" required>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Bank Trx ID (Konnect)</label>
                        <input type="text" name="konnect_trx_id" class="form-control form-control-lg" placeholder="Last 4-6 digits" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 btn-lg rounded-pill mt-4 fw-bold shadow hover-effect">
                    <i class="fas fa-check-circle me-2"></i> Confirm & Print Receipt
                </button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function fill(d) {
        if(d.cnic) $("#cnic").val(d.cnic);
        if(d.name) $("#name").val(d.name);
        if(d.phone) $("#phone").val(d.phone);
    }
    $("#cnic").autocomplete({ source: "api_search.php?type=cnic", select: function(e, ui) { fill(ui.item.data); } });
    $("#token").autocomplete({ source: "api_search.php?type=token", select: function(e, ui) { fill(ui.item.data); } });
});
</script>

<?php include 'includes/footer.php'; ?>