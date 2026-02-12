<?php
require_once __DIR__ . '/../auth_check.php';
requireRole(['admin', 'doctor']);

$visit_id = $_GET['visit_id'] ?? '';
$visit = findEntry(getJSON(FILE_VISITS), 'id', $visit_id);
if (!$visit) die("Invalid Visit ID");
$patient = findEntry(getJSON(FILE_PATIENTS), 'id', $visit['patient_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Save Rx
    $prescriptions = getJSON(DIR_DATA . 'prescriptions.json');
    $rx_data = [
        'id' => generateID('RX'),
        'visit_id' => $visit_id,
        'patient_id' => $patient['id'],
        'doctor_name' => $_SESSION['name'],
        'date' => date('Y-m-d H:i:s'),
        'vitals' => $_POST['vitals'],
        'diagnosis' => $_POST['diagnosis'],
        'medicines' => json_decode($_POST['meds_json'], true),
        'lab_requests' => $_POST['lab_tests'] ?? []
    ];
    saveJSON(DIR_DATA . 'prescriptions.json', array_merge($prescriptions, [$rx_data]));

    // 2. Add to Lab Queue
    if (!empty($rx_data['lab_requests'])) {
        $lab_queue = getJSON(FILE_LAB);
        foreach($rx_data['lab_requests'] as $test) {
            $lab_queue[] = ['id' => generateID('LAB'), 'visit_id' => $visit_id, 'patient_name' => $patient['name'], 'test_name' => $test, 'status' => 'pending', 'timestamp' => date('Y-m-d H:i:s')];
        }
        saveJSON(FILE_LAB, $lab_queue);
    }

    // 3. Close Visit
    $visits = getJSON(FILE_VISITS);
    foreach ($visits as &$v) { if ($v['id'] == $visit_id) $v['status'] = 'completed'; }
    saveJSON(FILE_VISITS, $visits);

    header("Location: dashboard.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Examine Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-light pb-5">
    <div class="bg-dark text-white p-3 d-flex justify-content-between sticky-top shadow">
        <div>Examining: <strong class="fs-5 ms-2"><?php echo $patient['name']; ?></strong></div>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Cancel</a>
    </div>

    <form method="POST" id="examForm">
        <div class="container-fluid mt-4">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="glass-panel p-4 bg-white mb-3">
                        <h6 class="fw-bold text-primary">Vitals</h6>
                        <input type="text" name="vitals[bp]" class="form-control mb-2" placeholder="BP (120/80)">
                        <input type="text" name="vitals[temp]" class="form-control mb-2" placeholder="Temp (98.6)">
                        <input type="text" name="vitals[weight]" class="form-control" placeholder="Weight (kg)">
                    </div>
                    <div class="glass-panel p-4 bg-white">
                        <h6 class="fw-bold text-primary">Lab Request</h6>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="CBC"><label>CBC</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="LFT"><label>LFT</label></div>
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="lab_tests[]" value="X-RAY"><label>X-Ray</label></div>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="glass-panel p-4 bg-white h-100">
                        <h5 class="fw-bold">Diagnosis & Rx</h5>
                        <textarea name="diagnosis" class="form-control mb-4" rows="3" placeholder="Clinical findings..." required></textarea>

                        <table class="table table-bordered" id="medTable">
                            <thead><tr><th>Medicine (Auto-Search)</th><th width="150">Dose</th><th width="150">Days</th><th></th></tr></thead>
                            <tbody></tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMedRow()">+ Add Medicine</button>
                        <input type="hidden" name="meds_json" id="meds_json">

                        <div class="mt-5 text-end border-top pt-3">
                            <button type="button" onclick="finalize()" class="btn btn-success btn-lg fw-bold">Finalize Checkup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="../assets/smart-search.js"></script>
    <script>
        function addMedRow() {
            const tbody = document.querySelector('#medTable tbody');
            const row = `
                <tr>
                    <td>
                        <input type="text" class="form-control med-name smart-search" data-type="medicine" placeholder="Type to search..." autocomplete="off">
                    </td>
                    <td><input type="text" class="form-control med-dose" placeholder="1+0+1"></td>
                    <td><input type="number" class="form-control med-dur" placeholder="5"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
            
            // RE-INIT SMART SEARCH so the new input works!
            if(window.initSmartSearch) window.initSmartSearch();
        }

        function finalize() {
            const meds = [];
            document.querySelectorAll('#medTable tbody tr').forEach(tr => {
                const name = tr.querySelector('.med-name').value;
                const dose = tr.querySelector('.med-dose').value;
                const dur = tr.querySelector('.med-dur').value;
                if(name) meds.push({ name, dose, dur });
            });
            document.getElementById('meds_json').value = JSON.stringify(meds);
            document.getElementById('examForm').submit();
        }
        
        // Init first row
        addMedRow();
    </script>
</body>
</html>