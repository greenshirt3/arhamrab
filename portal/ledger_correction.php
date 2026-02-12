<?php 
require 'includes/header.php'; 
if ($_SESSION['role'] !== 'admin') die("Access Denied");

$id = $_GET['id'] ?? 0;
$entry = $pdo->query("SELECT * FROM finance_ledger WHERE id = $id")->fetch();
if (!$entry) die("Entry not found");

// HANDLE DELETE
if (isset($_POST['delete_entry'])) {
    // 1. Reverse the Balance Effect
    $rev_amt = $entry['amount'];
    $acct = $entry['account_head'];
    
    if (in_array($entry['type'], ['income', 'sale'])) {
        // Was income, so subtract to reverse
        $pdo->prepare("UPDATE accounts SET current_balance = current_balance - ? WHERE account_name = ?")->execute([$rev_amt, $acct]);
    } else {
        // Was expense, so add back to reverse
        $pdo->prepare("UPDATE accounts SET current_balance = current_balance + ? WHERE account_name = ?")->execute([$rev_amt, $acct]);
    }
    
    // 2. Delete Record
    $pdo->prepare("DELETE FROM finance_ledger WHERE id = ?")->execute([$id]);
    
    echo "<script>alert('Entry Deleted & Balance Reverted'); window.location.href='financial_reports.php';</script>";
}
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="glass-panel p-4 border-danger">
            <h4 class="text-danger fw-bold">Delete Transaction?</h4>
            <div class="alert alert-warning">
                <strong>Warning:</strong> Deleting this will automatically update the 
                <strong><?php echo $entry['account_head']; ?></strong> balance.
            </div>
            
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Date:</strong> <?php echo $entry['trans_date']; ?></li>
                <li class="list-group-item"><strong>Desc:</strong> <?php echo $entry['description']; ?></li>
                <li class="list-group-item"><strong>Amount:</strong> Rs. <?php echo number_format($entry['amount']); ?></li>
            </ul>
            
            <form method="POST">
                <input type="hidden" name="delete_entry" value="1">
                <div class="d-flex gap-2">
                    <a href="financial_reports.php" class="btn btn-light w-50">Cancel</a>
                    <button class="btn btn-danger w-50 fw-bold">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>