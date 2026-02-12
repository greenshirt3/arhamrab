<?php
require_once '../auth_check.php';
requireRole(['admin', 'lab']);

$id = $_GET['id'] ?? '';
$queue = getJSON(FILE_LAB);
$test_index = null;
$test_data = null;

// Find the test in queue
foreach ($queue as $index => $item) {
    if ($item['id'] === $id) {
        $test_index = $index;
        $test_data = $item;
        break;
    }
}

if (!$test_data) die("Test Request Not Found");

// HANDLE SUBMISSION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode results from the dynamic table
    $results = json_decode($_POST['results_json'], true);
    
    // Update the record
    $queue[$test_index]['status'] = 'completed';
    $queue[$test_index]['technician'] = $_SESSION['name'];
    $queue[$test_index]['completed_at'] = date('Y-m-d H:i:s');
    $queue[$test_index]['results'] = $results;
    $queue[$test_index]['comments'] = $_POST['comments'];

    saveJSON(FILE_LAB, $queue);
    
    // Redirect to Print
    header("Location: print_report.php?id=" . $id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Perform Test | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light py-5">
    <div class="container col-lg-8">
        <div class="glass-panel p-5 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold mb-0">Enter Lab Results</h3>
                    <p class="text-muted mb-0">Test: <strong class="text-primary"><?php echo $test_data['test_name']; ?></strong></p>
                </div>
                <div class="text-end">
                    <span class="badge bg-secondary"><?php echo $test_data['id']; ?></span><br>
                    <small>Patient: <?php echo $test_data['patient_name']; ?></small>
                </div>
            </div>

            <form method="POST" id="labForm">
                
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle me-2"></i> Add parameters below. Example: For CBC, add rows for Hemoglobin, WBC, etc.
                </div>

                <table class="table table-bordered align-middle" id="resultTable">
                    <thead class="table-light">
                        <tr>
                            <th>Parameter Name</th>
                            <th>Observed Value</th>
                            <th>Unit</th>
                            <th>Reference Range</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>

                <button type="button" class="btn btn-outline-primary btn-sm mb-4" onclick="addRow()">
                    <i class="fas fa-plus"></i> Add Parameter
                </button>

                <div class="mb-4">
                    <label class="form-label fw-bold">Pathologist Comments</label>
                    <textarea name="comments" class="form-control" rows="3">Results correlate clinically.</textarea>
                </div>

                <input type="hidden" name="results_json" id="results_json">

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-light">Cancel</a>
                    <button type="button" onclick="finalizeReport()" class="btn btn-success fw-bold px-4">
                        <i class="fas fa-check-circle me-2"></i> Finalize & Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pre-fill some defaults based on test name (Simulation)
        const testName = "<?php echo $test_data['test_name']; ?>";
        
        window.onload = function() {
            if(testName.includes('CBC')) {
                addRow('Hemoglobin', '', 'g/dL', '13.0 - 17.0');
                addRow('WBC Count', '', '/cmm', '4,000 - 11,000');
                addRow('Platelets', '', '/cmm', '150,000 - 450,000');
            } else if(testName.includes('LFT')) {
                addRow('Bilirubin Total', '', 'mg/dL', '0.1 - 1.2');
                addRow('ALT (SGPT)', '', 'U/L', '< 40');
            } else {
                addRow();
            }
        };

        function addRow(param='', val='', unit='', range='') {
            const tbody = document.querySelector('#resultTable tbody');
            const row = `
                <tr>
                    <td><input type="text" class="form-control" value="${param}" placeholder="Parameter"></td>
                    <td><input type="text" class="form-control fw-bold" value="${val}" placeholder="Result"></td>
                    <td><input type="text" class="form-control" value="${unit}" placeholder="Unit"></td>
                    <td><input type="text" class="form-control" value="${range}" placeholder="Range"></td>
                    <td><button type="button" class="btn btn-light text-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        }

        function finalizeReport() {
            const data = [];
            document.querySelectorAll('#resultTable tbody tr').forEach(tr => {
                const inputs = tr.querySelectorAll('input');
                if(inputs[0].value) {
                    data.push({
                        param: inputs[0].value,
                        value: inputs[1].value,
                        unit: inputs[2].value,
                        range: inputs[3].value
                    });
                }
            });
            document.getElementById('results_json').value = JSON.stringify(data);
            document.getElementById('labForm').submit();
        }
    </script>
</body>
</html>