<?php
require_once '../auth_check.php';
// No role check here so patients can potentially view it if we expanded logic, 
// but for now, it's backend use.

$id = $_GET['id'] ?? '';
$queue = getJSON(FILE_LAB);
$report = findEntry($queue, 'id', $id);

if (!$report || $report['status'] != 'completed') die("Report not ready or invalid ID.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report: <?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #555; padding: 20px; }
        .a4-page {
            background: white; width: 210mm; min-height: 297mm; margin: auto; padding: 15mm;
            position: relative; box-shadow: 0 0 20px rgba(0,0,0,0.5);
            font-family: 'Times New Roman', serif;
        }
        .header-strip { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .patient-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 30px; border: 1px solid #ddd; }
        .table-results th { background: #000; color: white !important; }
        .footer-strip { position: absolute; bottom: 15mm; left: 15mm; right: 15mm; border-top: 1px solid #ccc; padding-top: 10px; font-size: 12px; text-align: center; color: #666; }
        .signature { text-align: right; margin-top: 50px; }
        .stamp { border: 2px dashed #0d6efd; color: #0d6efd; display: inline-block; padding: 5px 20px; transform: rotate(-5deg); font-weight: bold; font-size: 1.2rem; }

        @media print {
            body { background: white; padding: 0; }
            .a4-page { box-shadow: none; margin: 0; width: 100%; height: auto; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="a4-page">
        <div class="header-strip">
            <div>
                <h1 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 2px;">City Hospital</h1>
                <p class="mb-0 text-muted">Department of Pathology & Laboratory Medicine</p>
                <small><?php echo HOSPITAL_ADDRESS; ?> | <?php echo HOSPITAL_PHONE; ?></small>
            </div>
            <div class="text-end">
                <div class="stamp">FINALIZED</div>
            </div>
        </div>

        <div class="patient-info">
            <div class="row">
                <div class="col-6 mb-2"><strong>Patient Name:</strong> <?php echo $report['patient_name']; ?></div>
                <div class="col-6 mb-2"><strong>Lab ID:</strong> <?php echo $report['id']; ?></div>
                <div class="col-6"><strong>Test Requested:</strong> <?php echo $report['test_name']; ?></div>
                <div class="col-6"><strong>Report Date:</strong> <?php echo date('d-M-Y H:i', strtotime($report['completed_at'])); ?></div>
            </div>
        </div>

        <h4 class="text-uppercase border-bottom d-inline-block pb-1 mb-4">Investigation Report</h4>
        
        <table class="table table-striped table-results">
            <thead>
                <tr>
                    <th>Investigation</th>
                    <th>Result</th>
                    <th>Units</th>
                    <th>Reference Interval</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($report['results'])): ?>
                    <?php foreach($report['results'] as $row): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $row['param']; ?></td>
                        <td><?php echo $row['value']; ?></td>
                        <td><?php echo $row['unit']; ?></td>
                        <td><?php echo $row['range']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-4 p-3 bg-light border rounded">
            <strong>Pathologist's Comments:</strong>
            <p class="mb-0 fst-italic"><?php echo $report['comments']; ?></p>
        </div>

        <div class="signature">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Signature_sample.svg/1200px-Signature_sample.svg.png" width="150" style="opacity: 0.6;">
            <p class="fw-bold mb-0 border-top d-inline-block pt-2 px-5">Chief Pathologist</p>
            <p class="small text-muted">Dr. Sarah Khan, MBBS, FCPS</p>
        </div>

        <div class="footer-strip">
            <p>This is a computer-generated report and has been electronically verified. No manual signature is required.</p>
            <p>Printed on: <?php echo date('Y-m-d H:i:s'); ?> | Page 1 of 1</p>
        </div>
    </div>

    <div class="no-print text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg fw-bold">Print Report</button>
        <a href="dashboard.php" class="btn btn-secondary btn-lg">Back to Queue</a>
    </div>

</body>
</html>