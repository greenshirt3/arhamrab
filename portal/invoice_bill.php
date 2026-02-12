<?php
// FILE: invoice_bill.php (Enhanced Version)
require 'includes/config.php';

// 1. SECURITY & VALIDATION
if (!isset($_GET['id'])) {
    die("<div class='alert alert-danger text-center p-5'>❌ Invalid Bill ID</div>");
}

$bill_id = (int)$_GET['id'];

// 2. FETCH BILL DETAILS
try {
    $stmt = $pdo->prepare("SELECT * FROM bill_queue WHERE id = ?");
    $stmt->execute([$bill_id]);
    $bill = $stmt->fetch();
    
    if (!$bill) {
        die("<div class='alert alert-warning text-center p-5'>
                <i class='fas fa-exclamation-triangle fa-2x mb-3'></i><br>
                Bill #$bill_id not found in system.
            </div>");
    }
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// 3. FETCH SHOP SETTINGS
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM system_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Use defaults
}

$shop_name = $settings['shop_name'] ?? "ARHAM PRINTERS";
$shop_address = $settings['shop_address'] ?? "Jalalpur Jattan";
$shop_phone = $settings['shop_phone'] ?? "0300-1234567";
$footer_msg = $settings['invoice_footer'] ?? "Thank you for using our bill payment service!";

// 4. FORMAT DATA
$bill_date = date('d F Y, h:i A', strtotime($bill['created_at']));
$payment_date = ($bill['status'] == 'paid' && !empty($bill['paid_at'])) 
    ? date('d F Y, h:i A', strtotime($bill['paid_at'])) 
    : $bill_date;

// 5. FETCH TRANSACTION DETAILS IF AVAILABLE
$transaction_info = null;
if (!empty($bill['transaction_id'])) {
    try {
        $trans_stmt = $pdo->prepare("SELECT * FROM finance_ledger WHERE description LIKE ? ORDER BY id DESC LIMIT 1");
        $trans_stmt->execute(["%" . $bill['consumer_number'] . "%"]);
        $transaction_info = $trans_stmt->fetch();
    } catch (Exception $e) {
        // Silently fail
    }
}

// 6. WHATSAPP MESSAGE
$wa_msg = "*BILL PAYMENT RECEIPT - $shop_name*%0a";
$wa_msg .= "Receipt #: BILL-" . $bill['id'] . "%0a";
$wa_msg .= "Bill Type: " . $bill['bill_type'] . "%0a";
$wa_msg .= "Reference #: " . $bill['consumer_number'] . "%0a";
$wa_msg .= "Customer: " . ($bill['consumer_name'] ?: 'Walk-in Customer') . "%0a";
$wa_msg .= "Date: " . $payment_date . "%0a";
$wa_msg .= "------------------------%0a";
$wa_msg .= "Amount: Rs. " . number_format($bill['amount']) . "%0a";
$wa_msg .= "Status: " . strtoupper($bill['status']) . "%0a";

if (!empty($bill['transaction_id'])) {
    $wa_msg .= "TID: " . $bill['transaction_id'] . "%0a";
}

$wa_msg .= "Payment Mode: " . strtoupper($bill['payment_status']) . "%0a";
$wa_msg .= "------------------------%0a";
$wa_msg .= $footer_msg . "%0a";
$wa_msg .= "Shop: $shop_name%0a";
$wa_msg .= "Phone: $shop_phone";

// 7. PREPARE CUSTOMER MOBILE FOR WHATSAPP
$customer_mobile = '';
if (!empty($bill['mobile_no'])) {
    $customer_mobile = preg_replace('/[^0-9]/', '', $bill['mobile_no']);
    if (strlen($customer_mobile) === 11 && substr($customer_mobile, 0, 2) === '03') {
        $customer_mobile = '92' . substr($customer_mobile, 1);
    }
}

// 8. CHECK IF BILL HAS BEEN PROCESSED IN LEDGER
$is_processed = false;
if ($bill['status'] == 'paid') {
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM finance_ledger WHERE description LIKE ? AND category = 'Utility Bill'");
    $check_stmt->execute(["%" . $bill['consumer_number'] . "%"]);
    $is_processed = $check_stmt->fetchColumn() > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Receipt #<?php echo $bill_id; ?> - <?php echo $shop_name; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #283593;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --info-color: #2196f3;
            --light-color: #f5f5f5;
            --dark-color: #212121;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 850px;
            margin: 0 auto;
        }
        
        /* Header */
        .receipt-header {
            background: white;
            border-radius: 15px 15px 0 0;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .shop-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .shop-icon {
            background: var(--primary-color);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }
        
        .shop-details h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .shop-details p {
            color: #666;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .receipt-meta {
            text-align: right;
        }
        
        .receipt-id {
            font-size: 32px;
            font-weight: 900;
            color: var(--danger-color);
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .receipt-type {
            background: var(--info-color);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Main Content */
        .receipt-body {
            background: white;
            padding: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        
        /* Status Banner */
        .status-banner {
            padding: 20px 30px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-left h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .status-left p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .status-badge {
            background: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 800;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .status-paid {
            color: var(--success-color);
        }
        
        .status-pending {
            color: var(--warning-color);
        }
        
        /* Receipt Content */
        .receipt-content {
            padding: 30px;
        }
        
        /* Bill Info Grid */
        .bill-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--info-color);
        }
        
        .info-card h3 {
            font-size: 16px;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 140px;
        }
        
        .info-value {
            color: #222;
            font-weight: 500;
            flex: 1;
        }
        
        .highlight {
            color: var(--danger-color);
            font-weight: 700;
            font-size: 16px;
        }
        
        /* Amount Display */
        .amount-display {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            border: 3px dashed #4caf50;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            position: relative;
            overflow: hidden;
        }
        
        .amount-display::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
        }
        
        .amount-label {
            font-size: 18px;
            color: #388e3c;
            margin-bottom: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .amount-value {
            font-size: 48px;
            font-weight: 900;
            color: #1b5e20;
            margin: 10px 0;
        }
        
        .amount-words {
            font-size: 16px;
            color: #666;
            font-style: italic;
            margin-top: 10px;
        }
        
        /* Transaction Details */
        .transaction-card {
            background: #fff8e1;
            border: 2px solid #ffd54f;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .transaction-card h3 {
            color: #ff8f00;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Footer */
        .receipt-footer {
            background: #f5f5f5;
            padding: 25px 30px;
            border-top: 2px solid #eee;
            text-align: center;
        }
        
        .footer-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .security-code {
            background: white;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 14px;
            letter-spacing: 2px;
            color: #333;
            margin: 15px auto;
            max-width: 300px;
            border: 1px dashed #ccc;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 16px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            text-align: center;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3);
        }
        
        .btn-success {
            background: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background: #388e3c;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background: #f57c00;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 152, 0, 0.3);
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(244, 67, 54, 0.3);
        }
        
        .btn-dark {
            background: var(--dark-color);
            color: white;
        }
        
        .btn-dark:hover {
            background: #424242;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(33, 33, 33, 0.3);
        }
        
        /* WhatsApp Input */
        .whatsapp-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .whatsapp-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .whatsapp-icon {
            background: #25D366;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .whatsapp-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .whatsapp-input-group input {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .whatsapp-input-group input:focus {
            outline: none;
            border-color: #25D366;
            box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.1);
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(0,0,0,0.05);
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 12px;
            }
            
            .container {
                max-width: 100%;
                margin: 0;
            }
            
            .whatsapp-section,
            .action-buttons,
            .no-print {
                display: none !important;
            }
            
            .receipt-body {
                box-shadow: none;
                border: 1px solid #ccc;
            }
            
            .receipt-header {
                padding: 15px;
            }
            
            .shop-details h1 {
                font-size: 20px;
            }
            
            .receipt-id {
                font-size: 24px;
            }
            
            .receipt-content {
                padding: 20px;
            }
            
            .amount-value {
                font-size: 36px;
            }
            
            .watermark {
                opacity: 0.1;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .receipt-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .shop-brand {
                flex-direction: column;
                text-align: center;
            }
            
            .receipt-meta {
                text-align: center;
            }
            
            .bill-info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .status-banner {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .whatsapp-input-group {
                flex-direction: column;
            }
            
            .whatsapp-input-group input {
                width: 100%;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .amount-value {
                font-size: 36px;
            }
            
            .watermark {
                font-size: 60px;
            }
        }
        
        /* Animation */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        /* Number to Words Helper */
        .number-words {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container animate-slide-in">
        <!-- WhatsApp Input Section -->
        <div class="whatsapp-section no-print">
            <div class="whatsapp-header">
                <div class="whatsapp-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h3 style="margin: 0; color: #075e54;">Send Receipt via WhatsApp</h3>
                    <p style="color: #666; margin: 5px 0 0 0;">Enter customer's mobile number</p>
                </div>
            </div>
            <div class="whatsapp-input-group">
                <input type="text" 
                       id="wa_number" 
                       placeholder="e.g., 03001234567 or 923001234567" 
                       value="<?php echo htmlspecialchars($customer_mobile); ?>">
                <a href="#" id="wa_link" class="btn btn-success" style="min-width: 200px;">
                    <i class="fab fa-whatsapp"></i>
                    Send Receipt
                </a>
            </div>
        </div>
        
        <!-- Main Receipt -->
        <div class="receipt-body">
            <!-- Watermark -->
            <div class="watermark">
                <?php echo strtoupper($bill['status']); ?>
            </div>
            
            <!-- Header -->
            <div class="receipt-header">
                <div class="shop-brand">
                    <div class="shop-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="shop-details">
                        <h1><?php echo htmlspecialchars($shop_name); ?></h1>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shop_address); ?></p>
                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($shop_phone); ?></p>
                    </div>
                </div>
                <div class="receipt-meta">
                    <div class="receipt-id">BILL-<?php echo $bill_id; ?></div>
                    <div class="receipt-type">Bill Payment Receipt</div>
                </div>
            </div>
            
            <!-- Status Banner -->
            <div class="status-banner">
                <div class="status-left">
                    <h2><?php echo strtoupper($bill['bill_type']); ?> Bill Payment</h2>
                    <p>Payment processed successfully</p>
                </div>
                <div class="status-badge <?php echo 'status-' . $bill['status']; ?>">
                    <?php echo strtoupper($bill['status']); ?>
                </div>
            </div>
            
            <!-- Receipt Content -->
            <div class="receipt-content">
                <!-- Bill Information -->
                <div class="bill-info-grid">
                    <div class="info-card">
                        <h3><i class="fas fa-user-tag"></i> Customer Details</h3>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value highlight"><?php echo htmlspecialchars($bill['consumer_name'] ?: 'Walk-in Customer'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Reference #:</span>
                            <span class="info-value"><?php echo htmlspecialchars($bill['consumer_number']); ?></span>
                        </div>
                        <?php if(!empty($bill['mobile_no'])): ?>
                        <div class="info-row">
                            <span class="info-label">Mobile:</span>
                            <span class="info-value"><?php echo htmlspecialchars($bill['mobile_no']); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="info-row">
                            <span class="info-label">Payment Mode:</span>
                            <span class="info-value">
                                <span style="color: <?php echo $bill['payment_status'] == 'cash' ? '#4caf50' : '#ff9800'; ?>; font-weight: 700;">
                                    <?php echo strtoupper($bill['payment_status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <h3><i class="fas fa-receipt"></i> Payment Details</h3>
                        <div class="info-row">
                            <span class="info-label">Bill Type:</span>
                            <span class="info-value"><?php echo htmlspecialchars($bill['bill_type']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value"><?php echo $bill_date; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Date:</span>
                            <span class="info-value"><?php echo $payment_date; ?></span>
                        </div>
                        <?php if(!empty($bill['transaction_id'])): ?>
                        <div class="info-row">
                            <span class="info-label">Transaction ID:</span>
                            <span class="info-value" style="font-family: monospace; color: #d32f2f;">
                                <?php echo htmlspecialchars($bill['transaction_id']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Amount Display -->
                <div class="amount-display">
                    <div class="amount-label">Total Amount Paid</div>
                    <div class="amount-value">Rs. <?php echo number_format($bill['amount'], 2); ?></div>
                    <div class="amount-words">
                        (<?php echo numberToWords($bill['amount']); ?>)
                    </div>
                </div>
                
                <!-- Transaction Details -->
                <?php if($transaction_info): ?>
                <div class="transaction-card">
                    <h3><i class="fas fa-exchange-alt"></i> Transaction Record</h3>
                    <div class="info-row">
                        <span class="info-label">Ledger Entry:</span>
                        <span class="info-value">#<?php echo $transaction_info['id']; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Description:</span>
                        <span class="info-value"><?php echo htmlspecialchars($transaction_info['description']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account:</span>
                        <span class="info-value"><?php echo htmlspecialchars($transaction_info['account_head']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <div class="receipt-footer">
                    <div class="footer-message">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        <?php echo htmlspecialchars($footer_msg); ?>
                    </div>
                    
                    <?php if($is_processed): ?>
                    <div style="color: #4caf50; margin: 10px 0;">
                        <i class="fas fa-check-circle me-2"></i>
                        This bill has been recorded in financial ledger
                    </div>
                    <?php endif; ?>
                    
                    <div class="security-code">
                        SECURITY CODE: <?php echo strtoupper(md5($bill_id . $bill['consumer_number'])); ?>
                    </div>
                    
                    <div style="font-size: 12px; color: #888; margin-top: 15px;">
                        <p>This is an official payment receipt. Please retain for your records.</p>
                        <p>For verification, call: <?php echo $shop_phone; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Print Receipt
            </button>
            
            <a href="night_mode.php" class="btn btn-dark">
                <i class="fas fa-moon"></i>
                Back to Queue
            </a>
            
            <a href="bills.php" class="btn btn-warning">
                <i class="fas fa-plus-circle"></i>
                New Bill
            </a>
            
            <button onclick="downloadReceipt()" class="btn btn-danger">
                <i class="fas fa-download"></i>
                Download PDF
            </button>
            
            <button onclick="shareReceipt()" class="btn btn-success">
                <i class="fas fa-share-alt"></i>
                Share
            </button>
        </div>
    </div>

    <script>
        // WhatsApp Integration
        document.addEventListener('DOMContentLoaded', function() {
            const waInput = document.getElementById('wa_number');
            const waLink = document.getElementById('wa_link');
            
            function updateWhatsAppLink() {
                let number = waInput.value.trim();
                
                // Clean number
                number = number.replace(/\D/g, '');
                
                if (number.length === 0) {
                    waLink.href = '#';
                    waLink.style.opacity = '0.6';
                    waLink.onclick = function(e) {
                        e.preventDefault();
                        alert('Please enter a phone number');
                        waInput.focus();
                    };
                    return;
                }
                
                // Format for Pakistan
                if (number.startsWith('0')) {
                    number = '92' + number.substring(1);
                }
                
                if (!number.startsWith('92')) {
                    number = '92' + number;
                }
                
                const message = `<?php echo str_replace(["%0a"], ["\\n"], addslashes($wa_msg)); ?>`;
                const encodedMessage = encodeURIComponent(message);
                
                waLink.href = `https://wa.me/${number}?text=${encodedMessage}`;
                waLink.style.opacity = '1';
                waLink.onclick = null;
                waLink.target = '_blank';
            }
            
            // Initialize
            updateWhatsAppLink();
            
            // Update on input
            waInput.addEventListener('input', updateWhatsAppLink);
            
            // Enter key to send
            waInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (waLink.href !== '#') {
                        window.open(waLink.href, '_blank');
                    }
                }
            });
            
            // Auto-focus on input
            waInput.focus();
            
            // Select all text
            waInput.select();
        });
        
        // Download as PDF
        function downloadReceipt() {
            window.print();
        }
        
        // Share functionality
        function shareReceipt() {
            if (navigator.share) {
                navigator.share({
                    title: 'Bill Receipt #<?php echo $bill_id; ?>',
                    text: 'Payment receipt for <?php echo $bill['bill_type']; ?> bill',
                    url: window.location.href,
                })
                .catch(console.error);
            } else {
                // Fallback: Copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Receipt URL copied to clipboard!');
                });
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Ctrl+S for save/share
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                downloadReceipt();
            }
            
            // Ctrl+W to close
            if (e.ctrlKey && e.key === 'w') {
                e.preventDefault();
                window.close();
            }
        });
        
        // Auto-print after 2 seconds (optional)
        // setTimeout(() => {
        //     if (confirm('Do you want to print this receipt?')) {
        //         window.print();
        //     }
        // }, 2000);
    </script>
</body>
</html>
<?php
// Helper function to convert number to words
function numberToWords($num) {
    $ones = array(
        0 => "", 1 => "One", 2 => "Two", 3 => "Three", 4 => "Four",
        5 => "Five", 6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine",
        10 => "Ten", 11 => "Eleven", 12 => "Twelve", 13 => "Thirteen",
        14 => "Fourteen", 15 => "Fifteen", 16 => "Sixteen",
        17 => "Seventeen", 18 => "Eighteen", 19 => "Nineteen"
    );
    
    $tens = array(
        2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty",
        6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
    );
    
    if ($num == 0) {
        return "Zero";
    }
    
    // Handle decimals
    $whole = floor($num);
    $decimal = round(($num - $whole) * 100);
    
    $words = convertToWords($whole);
    
    if ($decimal > 0) {
        $words .= " and " . convertToWords($decimal) . " Paisa";
    }
    
    $words .= " Rupees Only";
    
    return $words;
}

function convertToWords($num) {
    if ($num < 20) {
        global $ones;
        return $ones[$num];
    } elseif ($num < 100) {
        global $tens;
        return $tens[floor($num / 10)] . " " . convertToWords($num % 10);
    } elseif ($num < 1000) {
        return convertToWords(floor($num / 100)) . " Hundred " . convertToWords($num % 100);
    } elseif ($num < 100000) {
        return convertToWords(floor($num / 1000)) . " Thousand " . convertToWords($num % 1000);
    } elseif ($num < 10000000) {
        return convertToWords(floor($num / 100000)) . " Lakh " . convertToWords($num % 100000);
    } else {
        return convertToWords(floor($num / 10000000)) . " Crore " . convertToWords($num % 10000000);
    }
}
?>