<?php
require 'includes/config.php';
requireAuth();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM queue_tokens WHERE id = ?");
$stmt->execute([$id]);
$token = $stmt->fetch();

if(!$token) die("Token not found");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Token #<?php echo $token['token_number']; ?></title>
    <style>
        body { width: 58mm; margin: 0; font-family: 'Arial', sans-serif; text-align: center; }
        .header { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-top: 5px; }
        .sub-header { font-size: 10px; margin-bottom: 5px; }
        .token-box { border: 2px solid #000; padding: 5px; margin: 5px 0; border-radius: 5px; }
        .token-num { font-size: 32px; font-weight: 900; }
        .label { font-size: 10px; text-transform: uppercase; }
        .details { font-size: 10px; text-align: left; margin-top: 5px; }
        .footer { font-size: 9px; margin-top: 10px; font-style: italic; }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 500);">

    <div class="header">Arham Printers</div>
    <div class="sub-header">BISP Disbursement Center</div>
    
    <div class="token-box">
        <div class="label">Your Token Number</div>
        <div class="token-num"><?php echo $token['token_number']; ?></div>
    </div>

    <div class="details">
        <strong>CNIC:</strong> <?php echo $token['cnic']; ?><br>
        <strong>Date:</strong> <?php echo date('d-M-Y h:i A', strtotime($token['issued_at'])); ?>
    </div>

    <div class="footer">
        Please wait for your turn.<br>
        Powered by ArhamSoft
    </div>

</body>
</html>