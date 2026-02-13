<?php 
require 'includes/header.php'; 
require_once 'includes/intelligence.php';

$alert_html = "";

// HANDLE TOKEN ISSUANCE + FRAUD CHECK
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cnic = $_POST['cnic'];
    $name = $_POST['name'] ?? 'Guest';
    $print_mode = $_POST['print_mode']; // 'thermal' or 'queue'
    $phone = $_POST['phone'] ?? '';

    // 1. RUN AI FRAUD CHECK
    $risk = $AI->checkFraudRisk($cnic, $phone);

    if ($risk['score'] >= 50) {
        // BLOCK: High Risk
        $alert_html = "<div class='alert alert-danger shadow-lg p-4 mb-4 glass-panel border-danger'>
            <h3 class='fw-bold'><i class='fas fa-shield-alt'></i> SECURITY BLOCK</h3>
            <ul class='mb-0 mt-2'>";
        foreach($risk['alerts'] as $a) { $alert_html .= "<li>$a</li>"; }
        $alert_html .= "</ul></div>";
    } else {
        // SAFE: Process Token
        // Generate Token Number
        $today_count = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE DATE(issued_at) = CURDATE()")->fetchColumn() + 1;
        $token_num = date('d') . '-' . str_pad($today_count, 3, '0', STR_PAD_LEFT);
        
        // Save
        $is_printed = ($print_mode == 'thermal') ? 1 : 0;
        $stmt = $pdo->prepare("INSERT INTO queue_tokens (token_number, cnic, name, issued_by, is_printed) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$token_num, $cnic, $name, $_SESSION['user_id'], $is_printed]);
        $last_id = $pdo->lastInsertId();

        // Redirect based on mode
        if ($print_mode == 'thermal') {
            echo "<script>window.location.href='print_token.php?id=$last_id';</script>";
        } else {
            echo "<script>window.location.href='queue.php';</script>"; // Refresh for batch
        }
        exit();
    }
}
?>

<script src='https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js'></script>

<div class="row">
    <div class="col-lg-5 mb-4">
        
        <?php echo $alert_html; ?>

        <div class="card shadow-lg border-0 glass-panel">
            <div class="card-header bg-primary text-white text-center py-3" style="border-radius: 20px 20px 0 0;">
                <h4 class="mb-0"><i class="fas fa-id-card"></i> Smart Token Issuer</h4>
            </div>
            <div class="card-body bg-transparent">
                
                <div id="camera-container" class="mb-3 text-center" style="display:none;">
                    <div style="position: relative;">
                        <video id="video" width="100%" height="240" autoplay style="border-radius:10px; border:2px solid #333; background:#000; object-fit: cover;"></video>
                        <div style="position: absolute; top: 10%; left: 10%; width: 80%; height: 80%; border: 2px dashed rgba(255,255,255,0.7); border-radius: 10px; pointer-events: none;"></div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" id="capture-btn" class="btn btn-warning flex-grow-1 fw-bold"><i class="fas fa-camera"></i> Capture & Scan</button>
                        <button type="button" onclick="stopCamera()" class="btn btn-secondary"><i class="fas fa-times"></i></button>
                    </div>
                    <div id="ocr-status" class="text-primary fw-bold mt-2 small"></div>
                </div>

                <form id="tokenForm" method="post">
                    <div class="d-flex justify-content-between align-items-end mb-2">
                        <label class="fw-bold">CNIC Number</label>
                        <button type="button" class="btn btn-sm btn-dark shadow-sm" onclick="startCamera()">
                            <i class="fas fa-camera"></i> Scan ID
                        </button>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="cnic" name="cnic" class="form-control form-control-lg fw-bold fs-4 text-primary" placeholder="35202-xxxxxxx-x" required autocomplete="off">
                    </div>
                    
                    <label class="fw-bold">Beneficiary Name</label>
                    <input type="text" id="name" name="name" class="form-control mb-4" placeholder="Auto-fills if found" autocomplete="off">

                    <div class="row g-2">
                        <div class="col-6">
                            <button type="submit" name="print_mode" value="thermal" class="btn btn-dark w-100 py-3 shadow-sm hover-effect">
                                <i class="fas fa-print fa-lg mb-1"></i><br>Thermal Slip
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="print_mode" value="queue" class="btn btn-outline-primary w-100 py-3 shadow-sm hover-effect">
                                <i class="fas fa-layer-group fa-lg mb-1"></i><br>Add to Batch
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow border-0 glass-panel">
            <div class="card-header d-flex justify-content-between align-items-center bg-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="mb-0 fw-bold text-secondary">A4 Batch Queue</h5>
                <a href="print_batch.php" target="_blank" class="btn btn-success shadow-sm">
                    <i class="fas fa-print"></i> Print Sheet (20)
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Token</th>
                                <th>CNIC</th>
                                <th>Name</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="batch-list">
                            <?php
                            $batch = $pdo->query("SELECT * FROM queue_tokens WHERE is_printed=0 ORDER BY id ASC LIMIT 20")->fetchAll();
                            if(count($batch) > 0) {
                                foreach($batch as $b) {
                                    echo "<tr>
                                        <td class='ps-3'><span class='badge bg-warning text-dark fs-6'>{$b['token_number']}</span></td>
                                        <td class='fw-bold text-primary'>{$b['cnic']}</td>
                                        <td>{$b['name']}</td>
                                        <td class='text-end pe-3'>
                                            <a href='delete_token.php?id={$b['id']}' class='btn btn-sm btn-outline-danger' title='Remove'><i class='fas fa-trash'></i></a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center p-5 text-muted'><i class='fas fa-box-open fa-3x mb-3 opacity-25'></i><br>Batch queue is empty.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // --- 1. AUTOCOMPLETE ---
    $("#cnic").autocomplete({
        source: "api_search.php?type=cnic",
        minLength: 3,
        select: function(event, ui) {
            $("#cnic").val(ui.item.data.cnic);
            $("#name").val(ui.item.data.name);
            $("#name, #cnic").css("background-color", "#d1e7dd");
            setTimeout(function(){ $("#name, #cnic").css("background-color", ""); }, 800);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function(ul, item) {
        return $("<li>").append("<div class='p-2 border-bottom'><strong>" + item.data.cnic + "</strong><br><small class='text-muted'>" + item.data.name + "</small></div>").appendTo(ul);
    };

    // --- 2. FORMATTER ---
    $('#cnic').on('input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val.length > 5) val = val.substring(0, 5) + '-' + val.substring(5);
        if (val.length > 13) val = val.substring(0, 13) + '-' + val.substring(13, 14);
        $(this).val(val);
    });

    // --- 3. CAMERA LOGIC (Retained) ---
    const video = document.getElementById('video');
    const cnicInput = document.getElementById('cnic');
    let streamRef = null;

    window.startCamera = function() {
        $('#camera-container').slideDown();
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(stream => { video.srcObject = stream; streamRef = stream; })
            .catch(err => { alert("Camera Error: " + err.message); $('#camera-container').hide(); });
        }
    }

    window.stopCamera = function() {
        if(streamRef) { streamRef.getTracks().forEach(track => track.stop()); }
        $('#camera-container').slideUp();
    }

    document.getElementById('capture-btn').addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        document.getElementById('ocr-status').innerHTML = "<i class='fas fa-spinner fa-spin'></i> Processing...";
        
        Tesseract.recognize(canvas, 'eng').then(({ data: { text } }) => {
            const cnicMatch = text.match(/\d{5}-?\d{7}-?\d{1}/);
            if (cnicMatch) {
                let rawNumbers = cnicMatch[0].replace(/-/g, '');
                cnicInput.value = rawNumbers;
                $('#cnic').trigger('input');
                document.getElementById('ocr-status').innerHTML = "<span class='text-success fw-bold'>CNIC Found!</span>";
                
                $.getJSON("api_search.php?type=cnic&term=" + rawNumbers, function(data) {
                    if(data.length > 0) { $("#name").val(data[0].data.name); }
                });
                setTimeout(stopCamera, 1000);
            } else {
                document.getElementById('ocr-status').innerHTML = "<span class='text-danger'>Try again.</span>";
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>