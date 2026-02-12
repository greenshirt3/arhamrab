<?php
$order_id = $_GET['id'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | RiderGo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; }
        .receipt-card { background: white; padding: 40px 30px; border-radius: 24px; text-align: center; max-width: 400px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .icon-circle { width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px; animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        h1 { margin: 0; color: #111827; font-size: 1.8rem; font-weight: 800; }
        p { color: #6b7280; margin-top: 10px; line-height: 1.5; }
        .order-id-box { background: #f9fafb; border: 2px dashed #e5e7eb; padding: 15px; border-radius: 12px; font-weight: 800; color: #F37021; font-size: 1.2rem; margin: 25px 0; letter-spacing: 1px; }
        .btn-track { display: block; background: #1f2937; color: white; text-decoration: none; padding: 15px; border-radius: 12px; font-weight: 600; transition: 0.2s; }
        .btn-track:hover { background: #000; transform: translateY(-2px); }
        .wa-note { font-size: 0.85rem; color: #9ca3af; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        
        @keyframes popIn { 0% { transform: scale(0); } 100% { transform: scale(1); } }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="icon-circle"><i class="fas fa-check"></i></div>
        <h1>Order Placed!</h1>
        <p>Your order has been sent to the kitchen. We will notify you when the rider picks it up.</p>
        
        <div class="order-id-box">ORDER #<?php echo $order_id; ?></div>
        
        <a href="../index.php" class="btn-track">Return to Home</a>
        <div class="wa-note"><i class="fab fa-whatsapp"></i> Details sent to WhatsApp</div>
    </div>
</body>
</html>