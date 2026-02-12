<?php
require 'includes/config.php';
requireAuth();

$id = $_GET['id'];
$txn = $pdo->prepare("SELECT t.*, b.name, b.cnic FROM transactions t JOIN beneficiaries b ON t.beneficiary_id = b.id WHERE t.id = ?");
$txn->execute([$id]);
$row = $txn->fetch();

if(!$row) die("Transaction not found");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $id; ?></title>
    <style>
        body { width: 72mm; margin: 0 auto; font-family: 'Courier New', monospace; font-size: 12px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .kv-row { display: flex; justify-content: space-between; }
    </style>
</head>
<body onload="window.print();">
    
    <div class="center bold" style="font-size: 16px;">ARHAM PRINTERS</div>
    <div class="center">Jalalpur Jattan</div>
    <div class="center">0300-6238233</div>
    
    <div class="divider"></div>
    
    <div class="kv-row"><span>Receipt #:</span> <span><?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></span></div>
    <div class="kv-row"><span>Date:</span> <span><?php echo date('d-M-y h:i A'); ?></span></div>
    
    <div class="divider"></div>
    
    <div class="bold">Beneficiary Details:</div>
    <div class="kv-row"><span>Name:</span> <span><?php echo substr($row['name'], 0, 15); ?></span></div>
    <div class="kv-row"><span>CNIC:</span> <span><?php echo $row['cnic']; ?></span></div>
    
    <div class="divider"></div>
    
    <div class="kv-row bold" style="font-size: 14px;">
        <span>AMOUNT PAID:</span>
        <span>Rs. <?php echo number_format($row['amount']); ?></span>
    </div>
    
    <div class="kv-row"><span>Bank TRX:</span> <span><?php echo $row['konnect_trx_id']; ?></span></div>
    <div class="kv-row"><span>Agent:</span> <span><?php echo $_SESSION['username'] ?? 'Staff'; ?></span></div>
    
    <div class="divider"></div>
    <div class="center" style="font-size: 10px;">Computer Generated Slip<br>Not valid for legal claims</div>
    <br>
</body>
</html>