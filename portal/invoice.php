<?php
// FILE: invoice.php (Enhanced Version)
require 'includes/config.php';

// 1. SECURITY & VALIDATION
if (!isset($_GET['id'])) {
    die("<div class='alert alert-danger text-center p-5'>❌ Invalid Invoice ID</div>");
}

$invoice_id = preg_replace('/[^0-9A-Z]/', '', $_GET['id']); // Clean input

// 2. FETCH SHOP SETTINGS
$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM system_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Use defaults if settings table doesn't exist
}

$shop_name = $settings['shop_name'] ?? "ARHAM PRINTERS";
$shop_address = $settings['shop_address'] ?? "Jalalpur Jattan";
$shop_phone = $settings['shop_phone'] ?? "0300-1234567";
$shop_email = $settings['shop_email'] ?? "info@arhamprinters.com";
$footer_msg = $settings['invoice_footer'] ?? "Thank you for your business!";

// 3. FETCH INVOICE ITEMS
$stmt = $pdo->prepare("SELECT * FROM finance_ledger WHERE invoice_no = ?");
$stmt->execute([$invoice_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    die("<div class='alert alert-warning text-center p-5'>
            <i class='fas fa-exclamation-triangle fa-2x mb-3'></i><br>
            Invoice #$invoice_id not found in system.
        </div>");
}

// 4. CALCULATE TOTALS & CONSTRUCT DATA
$total_amount = 0;
$tax_rate = 0; // You can add tax logic later
$subtotal = 0;
$item_count = 0;
$invoice_date = '';

foreach ($items as $item) {
    $total_amount += $item['amount'];
    $item_count++;
    if (empty($invoice_date)) {
        $invoice_date = $item['trans_date'];
    }
}

$subtotal = $total_amount;
$tax_amount = ($subtotal * $tax_rate) / 100;
$grand_total = $subtotal + $tax_amount;

// 5. GET CUSTOMER DETAILS (if available)
$customer_name = "Walk-in Customer";
$customer_phone = "";
$customer_address = "";

// Check if there's a customer ID in the ledger
foreach ($items as $item) {
    if (!empty($item['description'])) {
        // Try to extract customer name from description
        if (strpos($item['description'], 'Customer:') !== false) {
            $customer_name = trim(str_replace('Customer:', '', $item['description']));
            $customer_name = substr($customer_name, 0, strpos($customer_name, '(') ?: strlen($customer_name));
        }
    }
}

// 6. WHATSAPP MESSAGE CONSTRUCTION
$wa_msg = "*INVOICE RECEIPT - $shop_name*%0a";
$wa_msg .= "Invoice #: $invoice_id%0a";
$wa_msg .= "Date: " . date('d-M-Y h:i A', strtotime($invoice_date)) . "%0a";
$wa_msg .= "Customer: $customer_name%0a";
$wa_msg .= "------------------------%0a";

foreach ($items as $index => $item) {
    $wa_msg .= ($index + 1) . ". " . substr($item['description'], 0, 40) . "%0a";
    $wa_msg .= "   Rs. " . number_format($item['amount']) . "%0a";
}

$wa_msg .= "------------------------%0a";
$wa_msg .= "Subtotal: Rs. " . number_format($subtotal) . "%0a";

if ($tax_amount > 0) {
    $wa_msg .= "Tax ($tax_rate%): Rs. " . number_format($tax_amount) . "%0a";
}

$wa_msg .= "*GRAND TOTAL: Rs. " . number_format($grand_total) . "*%0a";
$wa_msg .= "------------------------%0a";
$wa_msg .= $footer_msg . "%0a";
$wa_msg .= "Shop: $shop_name%0a";
$wa_msg .= "Phone: $shop_phone";

// 7. GENERATE UNIQUE FILENAME FOR PRINT
$print_filename = "Invoice_" . $invoice_id . "_" . date('Ymd_His');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice_id; ?> - <?php echo $shop_name; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 12px;
            --shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Header Controls */
        .header-controls {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-controls h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-controls h1 i {
            color: var(--secondary-color);
        }
        
        .controls-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .invoice-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
            text-align: right;
        }
        
        .invoice-id {
            font-size: 22px;
            font-weight: 700;
            color: var(--accent-color);
            letter-spacing: 1px;
        }
        
        .invoice-date {
            font-size: 14px;
            color: #6c757d;
        }
        
        /* Main Invoice Container */
        .invoice-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 25px;
            position: relative;
        }
        
        /* Invoice Header */
        .invoice-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .shop-info h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }
        
        .shop-info p {
            opacity: 0.9;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .shop-info i {
            margin-right: 8px;
            width: 20px;
        }
        
        .invoice-logo {
            background: white;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .invoice-logo .logo-icon {
            font-size: 40px;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }
        
        .invoice-logo .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        /* Invoice Body */
        .invoice-body {
            padding: 30px;
        }
        
        /* Customer & Shop Details */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #eee;
        }
        
        .detail-box {
            background: var(--light-color);
            padding: 20px;
            border-radius: 10px;
        }
        
        .detail-box h3 {
            font-size: 18px;
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        
        .detail-value {
            color: #212529;
            flex: 1;
        }
        
        /* Items Table */
        .items-table-container {
            margin-bottom: 40px;
            overflow-x: auto;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .items-table thead {
            background: var(--primary-color);
            color: white;
        }
        
        .items-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .items-table td {
            padding: 15px;
            vertical-align: top;
        }
        
        .item-description {
            max-width: 300px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Totals Section */
        .totals-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .total-label {
            color: #495057;
        }
        
        .total-value {
            font-weight: 600;
        }
        
        .grand-total {
            font-size: 24px;
            font-weight: 700;
            color: var(--accent-color);
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            margin-top: 15px;
        }
        
        /* Footer */
        .invoice-footer {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .footer-message {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .payment-method {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        
        .payment-method span {
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            min-width: 160px;
        }
        
        .btn-primary {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-success {
            background: var(--success-color);
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }
        
        .btn-dark {
            background: var(--dark-color);
            color: white;
        }
        
        .btn-dark:hover {
            background: #23272b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 58, 64, 0.3);
        }
        
        /* WhatsApp Input */
        .whatsapp-input {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .whatsapp-input label {
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .whatsapp-input input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            min-width: 250px;
        }
        
        .whatsapp-input input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-paid {
            background: rgba(46, 204, 113, 0.15);
            color: var(--success-color);
            border: 1px solid rgba(46, 204, 113, 0.3);
        }
        
        .status-pending {
            background: rgba(243, 156, 18, 0.15);
            color: var(--warning-color);
            border: 1px solid rgba(243, 156, 18, 0.3);
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
            
            .header-controls,
            .action-buttons,
            .whatsapp-input,
            .no-print {
                display: none !important;
            }
            
            .invoice-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .invoice-header {
                padding: 20px;
            }
            
            .shop-info h2 {
                font-size: 20px;
            }
            
            .invoice-body {
                padding: 20px;
            }
            
            .details-grid {
                gap: 20px;
                margin-bottom: 20px;
            }
            
            .items-table th,
            .items-table td {
                padding: 8px;
            }
            
            .btn {
                display: none;
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-controls {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .controls-right {
                justify-content: center;
                margin-top: 15px;
            }
            
            .invoice-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn {
                width: 100%;
            }
            
            .whatsapp-input {
                flex-direction: column;
                align-items: stretch;
            }
            
            .whatsapp-input input {
                min-width: auto;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="container animate-fade-in">
        <!-- Header with Controls -->
        <div class="header-controls">
            <h1>
                <i class="fas fa-file-invoice"></i>
                Invoice Receipt
            </h1>
            <div class="controls-right">
                <div class="invoice-info">
                    <div class="invoice-id">#<?php echo $invoice_id; ?></div>
                    <div class="invoice-date">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo date('d F Y, h:i A', strtotime($invoice_date)); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- WhatsApp Input Section -->
        <div class="whatsapp-input no-print">
            <label for="wa_number">
                <i class="fab fa-whatsapp text-success"></i>
                Send to WhatsApp:
            </label>
            <input type="text" 
                   id="wa_number" 
                   placeholder="Enter mobile number (e.g., 923001234567)" 
                   value="<?php echo !empty($customer_phone) ? $customer_phone : ''; ?>">
            <a href="#" id="wa_link" class="btn btn-success">
                <i class="fab fa-whatsapp"></i>
                Send Receipt
            </a>
        </div>
        
        <!-- Main Invoice Container -->
        <div class="invoice-container">
            <!-- Header -->
            <div class="invoice-header">
                <div class="shop-info">
                    <h2><?php echo htmlspecialchars($shop_name); ?></h2>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shop_address); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($shop_phone); ?></p>
                    <?php if(!empty($shop_email)): ?>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($shop_email); ?></p>
                    <?php endif; ?>
                </div>
                <div class="invoice-logo">
                    <div class="logo-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="logo-text">INVOICE</div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="invoice-body">
                <!-- Customer & Invoice Details -->
                <div class="details-grid">
                    <div class="detail-box">
                        <h3><i class="fas fa-user-circle"></i> Customer Details</h3>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value fw-bold"><?php echo htmlspecialchars($customer_name); ?></span>
                        </div>
                        <?php if(!empty($customer_phone)): ?>
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($customer_phone); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($customer_address)): ?>
                        <div class="detail-row">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($customer_address); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="detail-box">
                        <h3><i class="fas fa-info-circle"></i> Invoice Details</h3>
                        <div class="detail-row">
                            <span class="detail-label">Invoice #:</span>
                            <span class="detail-value fw-bold text-primary"><?php echo $invoice_id; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value"><?php echo date('d M Y, h:i A', strtotime($invoice_date)); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="status-badge status-paid">PAID</span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Items:</span>
                            <span class="detail-value"><?php echo $item_count; ?> item(s)</span>
                        </div>
                    </div>
                </div>
                
                <!-- Items Table -->
                <div class="items-table-container">
                    <h3 style="margin-bottom: 15px; color: var(--primary-color);">
                        <i class="fas fa-list-alt"></i> Items Purchased
                    </h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 60%;">Description</th>
                                <th style="width: 15%;" class="text-center">Category</th>
                                <th style="width: 20%;" class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <td class="text-center"><?php echo $index + 1; ?></td>
                                <td class="item-description">
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['description']); ?></div>
                                    <?php if(!empty($item['payment_method'])): ?>
                                    <small class="text-muted">Method: <?php echo $item['payment_method']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        <?php echo strtoupper($item['category']); ?>
                                    </span>
                                </td>
                                <td class="text-right fw-bold">
                                    Rs. <?php echo number_format($item['amount'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Totals -->
                <div class="totals-section">
                    <div class="total-row">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-value">Rs. <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <?php if ($tax_amount > 0): ?>
                    <div class="total-row">
                        <span class="total-label">Tax (<?php echo $tax_rate; ?>%):</span>
                        <span class="total-value">Rs. <?php echo number_format($tax_amount, 2); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="total-row grand-total">
                        <span class="total-label">GRAND TOTAL:</span>
                        <span class="total-value">Rs. <?php echo number_format($grand_total, 2); ?></span>
                    </div>
                </div>
                
                <!-- Footer Message -->
                <div class="invoice-footer">
                    <div class="footer-message">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <?php echo htmlspecialchars($footer_msg); ?>
                    </div>
                    
                    <div class="payment-method">
                        <span><i class="fas fa-money-bill-wave"></i> Cash</span>
                        <span><i class="fas fa-credit-card"></i> Card</span>
                        <span><i class="fas fa-mobile-alt"></i> Mobile Payment</span>
                        <span><i class="fas fa-university"></i> Bank Transfer</span>
                    </div>
                    
                    <div style="font-size: 12px; color: #6c757d;">
                        <p>This is a computer-generated invoice. No signature required.</p>
                        <p>For any queries, contact: <?php echo $shop_phone; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i>
                Print Invoice
            </button>
            
            <button onclick="downloadAsPDF()" class="btn btn-warning">
                <i class="fas fa-download"></i>
                Download PDF
            </button>
            
            <a href="dashboard.php" class="btn btn-dark">
                <i class="fas fa-home"></i>
                Back to Dashboard
            </a>
            
            <button onclick="window.close()" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Close Window
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
                
                // Clean and format number
                number = number.replace(/\D/g, ''); // Remove non-digits
                
                if (number.length === 0) {
                    waLink.href = '#';
                    waLink.style.opacity = '0.5';
                    waLink.style.cursor = 'not-allowed';
                    waLink.onclick = function(e) { 
                        e.preventDefault(); 
                        alert('Please enter a phone number');
                        waInput.focus();
                    };
                    return;
                }
                
                // Ensure it starts with country code
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
                waLink.style.cursor = 'pointer';
                waLink.onclick = null;
                waLink.target = '_blank';
            }
            
            // Initialize
            updateWhatsAppLink();
            
            // Update on input change
            waInput.addEventListener('input', updateWhatsAppLink);
            waInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (waLink.href !== '#') {
                        window.open(waLink.href, '_blank');
                    }
                }
            });
            
            // Auto-print option (comment out if not needed)
            // setTimeout(() => {
            //     if (confirm('Do you want to print this invoice?')) {
            //         window.print();
            //     }
            // }, 1000);
        });
        
        // Download as PDF (using browser's print to PDF)
        function downloadAsPDF() {
            window.print();
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Ctrl+W to close
            if (e.ctrlKey && e.key === 'w') {
                e.preventDefault();
                window.close();
            }
            
            // Escape to go back
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
        
        // Copy invoice number to clipboard
        function copyInvoiceNumber() {
            const invoiceNumber = '<?php echo $invoice_id; ?>';
            navigator.clipboard.writeText(invoiceNumber).then(() => {
                alert('Invoice number copied to clipboard: ' + invoiceNumber);
            });
        }
        
        // Share invoice via Web Share API if available
        function shareInvoice() {
            if (navigator.share) {
                navigator.share({
                    title: 'Invoice #<?php echo $invoice_id; ?>',
                    text: 'Check out this invoice from <?php echo $shop_name; ?>',
                    url: window.location.href,
                })
                .catch(console.error);
            } else {
                alert('Web Share API not supported in your browser');
            }
        }
    </script>
</body>
</html>
<?php
// Log invoice view for analytics (optional)
try {
    $log_stmt = $pdo->prepare("INSERT INTO invoice_views (invoice_no, viewed_at, ip_address) VALUES (?, NOW(), ?)");
    $log_stmt->execute([$invoice_id, $_SERVER['REMOTE_ADDR']]);
} catch (Exception $e) {
    // Silently fail - table might not exist
}
?>