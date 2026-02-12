<?php
include 'config.php';

// 1. Get Order Details
if (!isset($_GET['id'])) {
    die("Invalid Request");
}
$id = $_GET['id'];

// Secure Query
$stmt = $conn->prepare("SELECT orders.*, services.name_en, services.name_ur FROM orders JOIN services ON orders.service_id = services.id WHERE orders.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

// 2. Dynamic URL Generation (Auto-detects your domain)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST']; 
$track_url = "$protocol://$host/index.php?track=" . $order['tracking_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $order['tracking_id']; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #eee; 
            padding: 20px; 
        }
        .receipt { 
            width: 300px; 
            background: #fff; 
            margin: 0 auto; 
            padding: 20px; 
            text-align: center; 
            border: 1px dashed #333; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .logo { 
            font-weight: bold; 
            font-size: 20px; 
            margin-bottom: 5px; 
            text-transform: uppercase;
        }
        .info { 
            text-align: left; 
            margin-top: 15px; 
            font-size: 14px; 
            line-height: 1.4;
        }
        .total { 
            border-top: 2px dashed #000; 
            border-bottom: 2px dashed #000; 
            margin: 15px 0; 
            padding: 10px 0; 
            font-weight: bold; 
            font-size: 16px;
        }
        .footer {
            font-size: 12px; 
            margin-top: 15px;
        }
        #qrcode { 
            margin-top: 15px; 
            display: flex; 
            justify-content: center; 
        }
        .tech-partner {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dotted #ccc;
            font-size: 10px;
            color: #666;
        }
        .tech-partner strong {
            color: #000;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { border: none; box-shadow: none; width: 100%; margin: 0; }
            #print-btn { display: none; }
        }
        #print-btn {
            background: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            font-family: inherit;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="receipt">
    <div class="logo">MIRZA JI PROPERTY</div>
    <div>Jalalpur Jattan</div>
    <div>0300-7329510</div>
    <hr style="border-top: 1px dashed #000; margin: 10px 0;">
    
    <div class="info">
        <strong>Date:</strong> <?php echo date("d-m-Y h:i A", strtotime($order['created_at'])); ?><br>
        <strong>Track ID:</strong> <span style="font-size: 16px; font-weight: bold;"><?php echo $order['tracking_id']; ?></span><br>
        <strong>Customer:</strong> <?php echo $order['customer_name']; ?><br>
        <strong>Service:</strong> <?php echo $order['name_en']; ?><br>
    </div>

    <div class="total">
        Total Fee: Rs. <?php echo number_format($order['total_fee']); ?><br>
        Paid: Rs. <?php echo number_format($order['paid_amount']); ?><br>
        Balance: Rs. <?php echo number_format($order['total_fee'] - $order['paid_amount']); ?>
    </div>

    <div class="footer">
        Scan to Track Status:<br>
        <strong><?php echo $host; ?></strong>
    </div>

    <div id="qrcode"></div>

    <div class="tech-partner">
        Tech Partner: <strong>Arham Printers</strong><br>
        arhamprinters.pk
    </div>

    <br>
    <button id="print-btn" onclick="window.print()">Print Receipt</button>
</div>

<script type="text/javascript">
    // Generate QR Code dynamically
    new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo $track_url; ?>",
        width: 120,
        height: 120,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
    // Auto print popup after a short delay
    setTimeout(function() { window.print(); }, 800);
</script>

</body>
</html>