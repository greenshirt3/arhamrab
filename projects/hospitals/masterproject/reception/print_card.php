<?php
require_once '../auth_check.php';
$id = $_GET['id'] ?? '';
$patients = getJSON(FILE_PATIENTS);
$p = findEntry($patients, 'id', $id);

if (!$p) die("Patient not found.");

// QR Data: Just the ID is enough, scanner will look up details via API
$qr_url = getQRCode($p['id']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Print Card - <?php echo $p['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #555; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .id-card {
            width: 350px; height: 210px; /* Credit Card Ratio */
            background: linear-gradient(135deg, #ffffff 0%, #f0f2f5 100%);
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            font-family: 'Arial', sans-serif;
            border: 1px solid #ccc;
        }
        .header { background: #007bff; height: 50px; width: 100%; position: absolute; top: 0; left: 0; }
        .logo-text { color: white; font-weight: bold; position: absolute; top: 12px; left: 15px; font-size: 16px; }
        .qr-box { position: absolute; right: 15px; top: 60px; width: 90px; height: 90px; }
        .info { position: absolute; left: 20px; top: 70px; }
        .label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 2px; }
        .value { font-size: 14px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .footer-strip { position: absolute; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #888; padding: 5px; border-top: 1px solid #eee; background: white; }
        
        @media print {
            body { background: white; }
            .id-card { box-shadow: none; border: 1px solid #000; page-break-inside: avoid; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="id-card">
        <div class="header"></div>
        <div class="logo-text">CITY INTERNATIONAL HOSPITAL</div>
        
        <div class="info">
            <div class="label">Patient Name</div>
            <div class="value"><?php echo $p['name']; ?></div>
            
            <div class="label">Patient ID</div>
            <div class="value" style="font-family: monospace; font-size: 16px;"><?php echo $p['id']; ?></div>
            
            <div class="label">Emergency Contact</div>
            <div class="value"><?php echo $p['phone']; ?></div>
        </div>

        <img src="<?php echo $qr_url; ?>" class="qr-box" alt="QR">
        
        <div class="footer-strip">
            Keep this card safe. Scan at reception for instant appointment.
        </div>
    </div>

    <div class="no-print" style="position: fixed; bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary fw-bold">Print Card</button>
        <a href="dashboard.php" class="btn btn-light">Back</a>
    </div>

</body>
</html>