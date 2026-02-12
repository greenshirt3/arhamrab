<?php
$data = json_decode(urldecode($_GET['data']), true);
require_once '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt #<?php echo $data['id']; ?></title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 80mm; margin: 0 auto; background: #fff; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 10px; }
        .logo { font-size: 16px; font-weight: bold; }
        .meta { font-size: 10px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px solid #000; }
        td { padding: 5px 0; }
        .total-row { border-top: 1px dashed #000; font-weight: bold; font-size: 14px; margin-top: 10px; padding-top: 10px; text-align: right; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
        
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 5px; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="header">
        <div class="logo">CITY PHARMACY</div>
        <div><?php echo HOSPITAL_ADDRESS; ?></div>
        <div>TRN: 1234-5678-90</div>
    </div>

    <div class="meta">
        <div><strong>Rcpt #:</strong> <?php echo $data['id']; ?></div>
        <div><strong>Date:</strong> <?php echo date('Y-m-d H:i'); ?></div>
        <div><strong>Customer:</strong> <?php echo $data['customer']; ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th style="text-align:right">Amt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['items'] as $item): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['qty']; ?></td>
                <td style="text-align:right"><?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-row">
        TOTAL: <?php echo CURRENCY . ' ' . number_format($data['total'], 2); ?>
    </div>

    <div class="footer">
        <p>Get Well Soon!</p>
        <p>No Return without Receipt</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=INV-<?php echo $data['id']; ?>" width="60">
    </div>

    <div class="no-print" style="margin-top:20px; text-align:center;">
        <a href="pos.php" style="background:#000; color:#fff; padding:10px; text-decoration:none;">Back to POS</a>
    </div>

</body>
</html>