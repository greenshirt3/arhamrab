<?php 
include 'header.php'; 
$id = isset($_GET['report_id']) ? strtoupper($_GET['report_id']) : '';
$report = isset($data['lab_reports'][$id]) ? $data['lab_reports'][$id] : null;
?>

<div class="container py-5">
    <?php if($report): ?>
        <div class="card border-0 shadow-lg" id="printableArea">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between border-bottom pb-4 mb-4">
                    <div>
                        <h2 class="text-primary fw-bold"><?php echo $info['name']; ?></h2>
                        <p class="text-muted mb-0">Pathology Department</p>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0">CASE: <?php echo $id; ?></h4>
                        <span class="badge <?php echo $report['status']=='Finalized'?'bg-success':'bg-warning'; ?>">
                            <?php echo $report['status']; ?>
                        </span>
                    </div>
                </div>

                <div class="row mb-5 bg-light p-3 rounded">
                    <div class="col-md-3"><strong>Patient Name:</strong><br><?php echo $report['patient_name']; ?></div>
                    <div class="col-md-3"><strong>Age/Sex:</strong><br><?php echo $report['age']; ?> / Male</div>
                    <div class="col-md-3"><strong>Test Name:</strong><br><?php echo $report['test_name']; ?></div>
                    <div class="col-md-3"><strong>Date:</strong><br><?php echo $report['date']; ?></div>
                </div>

                <?php if($report['status'] == 'Finalized'): ?>
                <table class="table table-striped table-hover mb-5">
                    <thead class="table-dark">
                        <tr>
                            <th>Investigation</th>
                            <th>Result</th>
                            <th>Units</th>
                            <th>Reference Range</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report['results'] as $res): ?>
                        <tr>
                            <td class="fw-bold"><?php echo $res['param']; ?></td>
                            <td class="text-primary fw-bold"><?php echo $res['value']; ?></td>
                            <td><?php echo $res['unit']; ?></td>
                            <td><?php echo $res['range']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center p-5">
                        <h4><i class="fa fa-clock me-2"></i> Report is being processed.</h4>
                        <p>Please check back after 4 hours.</p>
                    </div>
                <?php endif; ?>

                <div class="row mt-5 pt-5">
                    <div class="col-6">
                        <p class="small text-muted">Generated Electronically via Web Portal.</p>
                    </div>
                    <div class="col-6 text-end">
                        <div class="border-top border-dark d-inline-block pt-2 px-5">
                            <strong>Chief Pathologist</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <button onclick="window.print()" class="btn btn-primary"><i class="fa fa-print me-2"></i>Print Report</button>
            <a href="lab_portal.php" class="btn btn-outline-dark">Check Another</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center shadow">
            <h3>Record Not Found</h3>
            <p>No report found for ID: <strong><?php echo $id; ?></strong>. Please check your receipt.</p>
            <a href="lab_portal.php" class="btn btn-dark mt-2">Try Again</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>