<?php 
require_once 'includes/header.php'; 

$search_id = $_GET['report_id'] ?? '';
$report = null;
$error = null;

if ($search_id) {
    // 1. Load Lab Data
    $lab_queue = getJSON(FILE_LAB);
    
    // 2. Find the Report
    $report = findEntry($lab_queue, 'id', strtoupper(trim($search_id)));
    
    if (!$report) {
        $error = "Record not found. Please check the ID on your receipt.";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 mb-5">
            <div class="glass-panel p-5" data-aos="fade-down">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-3 shadow">
                        <i class="fas fa-flask fa-2x"></i>
                    </div>
                    <h2 class="fw-bold">Lab Reports</h2>
                    <p class="text-muted">Enter the Lab ID found on your receipt.</p>
                </div>

                <form action="check_reports.php" method="GET">
                    <div class="mb-4">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="report_id" class="form-control border-start-0 text-uppercase fw-bold" 
                                   placeholder="e.g. LAB-25-101" value="<?php echo htmlspecialchars($search_id); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                        View Results <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-10">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center shadow-sm p-4 rounded-3" data-aos="shake">
                    <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                    <strong><?php echo $error; ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($report): ?>
                <div class="glass-panel p-0 overflow-hidden bg-white" data-aos="fade-up">
                    
                    <div class="p-4 bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">LAB REPORT</h4>
                            <small class="text-white-50">Generated: <?php echo date('d M Y'); ?></small>
                        </div>
                        <div class="text-end">
                            <span class="badge <?php echo $report['status'] == 'completed' ? 'bg-success' : 'bg-warning text-dark'; ?> fs-6 px-3 py-2">
                                <?php echo strtoupper($report['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <div class="row mb-5 border-bottom pb-4">
                            <div class="col-md-3">
                                <small class="text-muted text-uppercase">Patient Name</small>
                                <h5 class="fw-bold text-primary"><?php echo $report['patient_name']; ?></h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted text-uppercase">Report ID</small>
                                <h5 class="fw-bold"><?php echo $report['id']; ?></h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted text-uppercase">Test Requested</small>
                                <h5 class="fw-bold"><?php echo $report['test_name']; ?></h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted text-uppercase">Date</small>
                                <h5 class="fw-bold"><?php echo date('d M Y', strtotime($report['timestamp'])); ?></h5>
                            </div>
                        </div>

                        <?php if ($report['status'] === 'completed'): ?>
                            
                            <h5 class="fw-bold text-uppercase text-secondary mb-3">Test Results</h5>
                            <table class="table table-striped table-hover border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Result</th>
                                        <th>Unit</th>
                                        <th>Normal Range</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($report['results']) && is_array($report['results'])): ?>
                                        <?php foreach($report['results'] as $row): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $row['param']; ?></td>
                                            <td class="text-primary fw-bold"><?php echo $row['value']; ?></td>
                                            <td><?php echo $row['unit']; ?></td>
                                            <td class="text-muted small"><?php echo $row['range']; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center text-muted">No parameters recorded.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <div class="alert alert-light border mt-4">
                                <strong><i class="fas fa-user-md me-1"></i> Pathologist Comments:</strong>
                                <p class="mb-0 fst-italic text-muted"><?php echo $report['comments'] ?? 'No comments provided.'; ?></p>
                            </div>

                            <div class="text-center mt-5">
                                <button onclick="window.print()" class="btn btn-outline-dark px-4 rounded-pill">
                                    <i class="fas fa-print me-2"></i> Print Report
                                </button>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-hourglass-half fa-4x text-warning mb-3"></i>
                                <h3>Processing...</h3>
                                <p class="text-muted lead">This sample is currently being analyzed in our lab.</p>
                                <p>Please check back later.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>