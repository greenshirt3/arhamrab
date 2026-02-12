<?php 
require 'includes/header.php'; 
if ($_SESSION['role'] !== 'admin') { die("ACCESS DENIED"); }

// HANDLE VOID
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $reason = $_POST['reason'];
    
    // Mark transaction as void instead of deleting (Audit Trail)
    $stmt = $pdo->prepare("UPDATE transactions SET status = 'void', void_reason = ?, void_by = ? WHERE id = ?");
    $stmt->execute([$reason, $_SESSION['user_id'], $id]);
    
    echo "<script>window.location.href='reports.php';</script>";
    exit();
}

$id = $_GET['id'] ?? 0;
$txn = $pdo->query("SELECT * FROM transactions WHERE id = $id")->fetch();
if(!$txn) die("Transaction not found");
?>

<link rel="stylesheet" href="css/modern.css">

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel p-5 border-danger" style="border: 1px solid #dc3545;">
            <div class="text-center text-danger mb-4">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h3 class="fw-bold">Void Transaction #<?php echo $id; ?></h3>
                <p class="text-white-50">This action will remove the amount from financial totals.</p>
            </div>

            <div class="bg-dark bg-opacity-50 p-3 rounded mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span>Amount:</span>
                    <span class="fw-bold text-white">Rs. <?php echo number_format($txn['amount']); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Bank ID:</span>
                    <span class="fw-bold text-white"><?php echo $txn['konnect_trx_id']; ?></span>
                </div>
            </div>

            <form method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                
                <div class="mb-4">
                    <label class="fw-bold text-danger">Reason for Voiding (Required)</label>
                    <textarea name="reason" class="form-control bg-dark text-white border-secondary" rows="3" required placeholder="e.g. Duplicate entry, Wrong amount..."></textarea>
                </div>

                <div class="d-flex gap-3">
                    <a href="reports.php" class="btn btn-outline-light w-50 rounded-pill">Cancel</a>
                    <button type="submit" class="btn btn-danger w-50 rounded-pill fw-bold">Confirm Void</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>