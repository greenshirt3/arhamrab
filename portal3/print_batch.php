<?php
require 'includes/config.php';
requireAuth();

// Fetch 20 unprinted tokens
$tokens = $pdo->query("SELECT * FROM queue_tokens WHERE is_printed=0 ORDER BY id ASC LIMIT 20")->fetchAll();

// Auto-mark as printed
if(count($tokens) > 0) {
    $ids = array_column($tokens, 'id');
    $in  = str_repeat('?,', count($ids) - 1) . '?';
    $pdo->prepare("UPDATE queue_tokens SET is_printed=1 WHERE id IN ($in)")->execute($ids);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Batch Print (A4)</title>
    <style>
        @page { size: A4; margin: 5mm; }
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background: #fff; }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 Columns */
            grid-template-rows: repeat(5, 1fr);    /* 5 Rows */
            gap: 2mm;
            height: 285mm; /* Fits A4 */
        }
        
        .card {
            border: 2px dashed #444;
            border-radius: 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5px;
        }
        
        .brand { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000; }
        .token { font-size: 32px; font-weight: 900; margin: 5px 0; font-family: 'Arial Black', sans-serif; }
        .cnic { font-size: 12px; font-family: monospace; font-weight: bold; letter-spacing: 1px; }
        .meta { font-size: 9px; color: #555; margin-top: 5px; }
    </style>
</head>
<body onload="window.print()">

    <?php if(count($tokens) == 0): ?>
        <div style="text-align:center; padding-top:50px;">
            <h2>Queue is Empty</h2>
            <p>No unprinted tokens found.</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach($tokens as $t): ?>
            <div class="card">
                <div class="brand">Arham BISP Center</div>
                <div class="token"><?php echo $t['token_number']; ?></div>
                <div class="cnic"><?php echo $t['cnic']; ?></div>
                <div class="meta"><?php echo date('d-M h:i A', strtotime($t['issued_at'])); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>