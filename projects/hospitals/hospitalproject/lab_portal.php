<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="fa fa-flask fa-3x mb-3"></i>
                    <h3>Online Lab Reports</h3>
                    <p class="mb-0">Enter your Case ID / MR Number to view results</p>
                </div>
                <div class="card-body p-5">
                    <form method="GET" action="lab_result.php">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Lab Case ID</label>
                            <input type="text" name="report_id" class="form-control form-control-lg text-uppercase" placeholder="e.g., LAB-101" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 btn-lg">View Report</button>
                    </form>
                </div>
                <div class="card-footer text-center text-muted small bg-light">
                    Protected by SSL. Results are confidential.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>