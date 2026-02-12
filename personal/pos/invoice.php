<?php
// invoice.php - Public Invoice Viewer
$id = $_GET['id'] ?? '';
$invoices = json_decode(file_exists('data_invoices.json') ? file_get_contents('data_invoices.json') : '[]', true);
$inv = null;

// Find the invoice
foreach ($invoices as $i) {
    if ($i['id'] == $id) { $inv = $i; break; }
}

if (!$inv) die("Invoice not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $inv['id']; ?> - Arham Printers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { background: #525659; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 20px; }
        .toolbar { background: white; padding: 15px; border-radius: 50px; margin-bottom: 20px; display: flex; gap: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .paper { 
            background: white; width: 210mm; min-height: 297mm; padding: 15mm; position: relative; 
            box-shadow: 0 0 50px rgba(0,0,0,0.5); color: #333;
        }
        /* Professional Invoice Design */
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #0f172a; padding-bottom: 20px; margin-bottom: 40px; }
        .brand h1 { margin: 0; font-weight: 800; color: #0f172a; font-size: 32px; letter-spacing: -1px; }
        .brand p { margin: 0; color: #64748b; font-size: 14px; }
        .meta { text-align: right; }
        .meta-title { font-size: 42px; font-weight: 900; color: #e2e8f0; line-height: 0.8; text-transform: uppercase; }
        .meta-data { margin-top: 10px; font-size: 14px; }
        
        .bill-grid { display: flex; gap: 50px; margin-bottom: 40px; }
        .bill-box h5 { font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 1px; margin-bottom: 8px; }
        .bill-box div { font-weight: 600; font-size: 15px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; background: #f8fafc; color: #475569; padding: 12px 10px; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155; }
        tr:last-child td { border-bottom: none; }
        .text-end { text-align: right; }

        .summary-box { width: 280px; margin-left: auto; background: #f8fafc; padding: 20px; border-radius: 8px; }
        .sum-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .sum-row.total { border-top: 2px solid #cbd5e1; padding-top: 10px; margin-top: 10px; font-weight: 800; font-size: 16px; color: #0f172a; }

        .stamp { 
            position: absolute; top: 35%; right: 100px; font-size: 80px; font-weight: 900; 
            text-transform: uppercase; opacity: 0.08; transform: rotate(-30deg); border: 8px solid; padding: 10px 40px; border-radius: 20px;
        }
        .paid { color: #10b981; border-color: #10b981; }
        .unpaid { color: #ef4444; border-color: #ef4444; }

        .footer { position: absolute; bottom: 15mm; left: 15mm; right: 15mm; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px solid #e2e8f0; padding-top: 15px; }

        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none; }
            .paper { box-shadow: none; margin: 0; width: 100%; height: 100%; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold">Print Invoice</button>
        <button onclick="downloadPDF()" class="btn btn-danger rounded-pill px-4 fw-bold">Download PDF</button>
        <a href="https://wa.me/<?php echo $inv['customer']['phone']; ?>" class="btn btn-success rounded-pill px-4 fw-bold">Contact Us</a>
    </div>

    <div class="paper" id="invoiceContent">
        <div class="stamp <?php echo $inv['balance'] <= 0 ? 'paid' : 'unpaid'; ?>">
            <?php echo $inv['balance'] <= 0 ? 'PAID' : 'DUE'; ?>
        </div>

        <div class="header">
            <div class="brand">
                <h1>ARHAM PRINTERS</h1>
                <p>Printing & Packaging Solutions</p>
                <p>Jalalpur Jattan, Gujrat</p>
                <p style="margin-top:5px; font-weight:600">0300-XXXXXXX | info@arhamprinters.pk</p>
            </div>
            <div class="meta">
                <div class="meta-title">INVOICE</div>
                <div class="meta-data">
                    <div><strong>Invoice #:</strong> <?php echo $inv['id']; ?></div>
                    <div><strong>Date:</strong> <?php echo $inv['date']; ?></div>
                    <?php if($inv['due']): ?>
                    <div style="color:#ef4444"><strong>Due Date:</strong> <?php echo $inv['due']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bill-grid">
            <div class="bill-box">
                <h5>Bill To</h5>
                <div><?php echo $inv['customer']['name']; ?></div>
                <div style="font-weight:400"><?php echo $inv['customer']['phone']; ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:50%">Description / Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Rate</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($inv['items'] as $item): ?>
                <tr>
                    <td>
                        <strong><?php echo $item['name']; ?></strong>
                    </td>
                    <td class="text-end"><?php echo $item['qty']; ?></td>
                    <td class="text-end"><?php echo number_format($item['rate']); ?></td>
                    <td class="text-end fw-bold"><?php echo number_format($item['total']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="summary-box">
            <div class="sum-row"><span>Subtotal</span> <span><?php echo number_format($inv['sub']); ?></span></div>
            <?php if($inv['taxTotal'] > 0): ?>
            <div class="sum-row"><span>Tax</span> <span><?php echo number_format($inv['taxTotal']); ?></span></div>
            <?php endif; ?>
            <?php if($inv['discount'] > 0): ?>
            <div class="sum-row text-success"><span>Discount</span> <span>-<?php echo number_format($inv['discount']); ?></span></div>
            <?php endif; ?>
            <div class="sum-row total"><span>Total</span> <span><?php echo number_format($inv['total']); ?></span></div>
            <div class="sum-row" style="margin-top:10px"><span>Paid</span> <span><?php echo number_format($inv['paid']); ?></span></div>
            <div class="sum-row text-danger fw-bold"><span>Balance Due</span> <span><?php echo number_format($inv['balance']); ?></span></div>
        </div>

        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <h6 style="font-size:12px; font-weight:700; text-transform:uppercase; color:#94a3b8">Terms & Conditions</h6>
            <p style="font-size:12px; color:#64748b; margin:0">
                <?php echo $inv['notes'] ? $inv['notes'] : "Thank you for your business. Please make checks payable to Arham Printers."; ?>
            </p>
        </div>

        <div class="footer">
            System by Arham Printers IT Dept
        </div>
    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('invoiceContent');
            const opt = {
                margin: 0,
                filename: 'Invoice_<?php echo $inv['id']; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>