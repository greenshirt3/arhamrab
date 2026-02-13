<?php 
require 'includes/header.php'; 

// HANDLE SAVE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['bill_type'];
    $num  = preg_replace('/[^0-9]/', '', $_POST['consumer_number']); 
    $name = $_POST['consumer_name'];
    $amt  = $_POST['amount'];
    
    // Save Memory
    $mem = $pdo->prepare("INSERT INTO saved_consumers (consumer_number, consumer_name, bill_type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE consumer_name = ?, bill_type = ?");
    $mem->execute([$num, $name, $type, $name, $type]);

    // Add to Queue
    $check = $pdo->prepare("SELECT id FROM bill_queue WHERE consumer_number = ? AND DATE(created_at) = CURDATE()");
    $check->execute([$num]);
    
    if(!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO bill_queue (bill_type, consumer_number, consumer_name, amount) VALUES (?, ?, ?, ?)");
        $stmt->execute([$type, $num, $name, $amt]);
        // VISUAL SUCCESS
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessOverlay();
            });
        </script>";
    } else {
        echo "<div class='alert alert-warning text-center fw-bold'>⚠️ Bill already scanned today!</div>";
    }
}
?>

<link rel="stylesheet" href="css/modern.css">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src='https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js'></script>

<style>
    /* HUD SCANNER DESIGN */
    .scanner-hud {
        background: #000;
        border-radius: 12px;
        border: 2px solid #333;
        position: relative;
        overflow: hidden;
        min-height: 320px;
        box-shadow: inset 0 0 50px rgba(0,0,0,0.8);
    }
    .hud-line {
        position: absolute; width: 100%; height: 2px; background: #00E5FF;
        top: 50%; left: 0; box-shadow: 0 0 15px #00E5FF;
        z-index: 10;
    }
    .hud-corner {
        position: absolute; width: 30px; height: 30px; border: 4px solid var(--brand-cyan);
        z-index: 5;
    }
    .tl { top: 10px; left: 10px; border-right: 0; border-bottom: 0; }
    .tr { top: 10px; right: 10px; border-left: 0; border-bottom: 0; }
    .bl { bottom: 10px; left: 10px; border-right: 0; border-top: 0; }
    .br { bottom: 10px; right: 10px; border-left: 0; border-top: 0; }

    /* Mode Buttons */
    .mode-btn { 
        background: #1a1a1a; border: 1px solid #333; color: #888; 
        transition: 0.2s; font-size: 12px; font-weight: bold; letter-spacing: 1px;
    }
    .mode-btn:hover { background: #333; color: #fff; }
    .mode-btn.active { 
        background: var(--brand-cyan); color: #000; border-color: var(--brand-cyan); 
        box-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
    }

    /* Success Animation */
    #success-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 255, 136, 0.9);
        z-index: 9999; display: none;
        align-items: center; justify-content: center;
        flex-direction: column;
    }
    
    /* DROPDOWN STYLING - BIG & SCROLLABLE */
    .ui-autocomplete {
        max-height: 300px; /* Allow long lists */
        overflow-y: auto; overflow-x: hidden;
        background: #fff; border-radius: 12px; 
        box-shadow: 0 15px 40px rgba(0,0,0,0.3); border: 1px solid #ddd;
        padding: 0; font-family: 'Segoe UI', sans-serif;
        z-index: 1055 !important;
    }
    .ui-menu-item .ui-menu-item-wrapper {
        padding: 12px 15px; 
        border-bottom: 1px solid #f0f0f0; 
        font-weight: 600; font-size: 16px; color: #333;
    }
    .ui-menu-item:last-child .ui-menu-item-wrapper { border-bottom: none; }
    .ui-menu-item .ui-menu-item-wrapper:hover,
    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background: var(--brand-cyan); color: #000; 
        border: none; margin: 0;
    }
</style>

<div id="success-overlay">
    <i class="fas fa-check-circle fa-5x text-white mb-3 animate__animated animate__zoomIn"></i>
    <h1 class="text-white fw-bold animate__animated animate__fadeInUp">SAVED</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="glass-panel p-0 overflow-hidden border-0">
            <div class="bg-dark p-3 d-flex justify-content-between align-items-center">
                <h5 class="text-white m-0 fw-bold"><i class="fas fa-satellite-dish me-2 text-primary"></i> ENTRY TERMINAL</h5>
                <span class="badge bg-secondary">STAFF MODE</span>
            </div>

            <div class="p-4">
                <div class="btn-group w-100 mb-4 shadow-sm" role="group">
                    <button onclick="setMode('qr')" class="btn mode-btn active" id="btn-qr">QR CODE</button>
                    <button onclick="setMode('bar')" class="btn mode-btn" id="btn-bar">BARCODE</button>
                    <button onclick="setMode('text')" class="btn mode-btn" id="btn-text">AI TEXT</button>
                </div>

                <div id="scanner-container" style="display:none;">
                    <div class="scanner-hud mb-3">
                        <div class="hud-corner tl"></div><div class="hud-corner tr"></div>
                        <div class="hud-corner bl"></div><div class="hud-corner br"></div>
                        <div class="hud-line"></div>
                        
                        <div id="reader"></div>
                        <video id="videoElement" style="display:none; width:100%; height:100%; object-fit:cover;" autoplay playsinline></video>
                    </div>

                    <button id="capture-btn" class="btn btn-warning w-100 fw-bold py-3 mb-2 shadow" style="display:none;" onclick="captureText()">
                        <i class="fas fa-camera"></i> CAPTURE IMAGE
                    </button>
                    <button onclick="stopAll()" class="btn btn-outline-danger w-100 btn-sm">CANCEL SCAN</button>
                </div>

                <div id="start-area" class="mb-4">
                    <button onclick="startSelectedMode()" class="btn btn-dark w-100 py-4 rounded-3 shadow border-2 border-secondary hover-effect">
                        <i class="fas fa-power-off fa-2x mb-2 d-block text-primary"></i>
                        <span class="fw-bold ls-1">ACTIVATE SCANNER</span>
                    </button>
                </div>

                <form method="POST" id="billForm">
                    <div class="form-floating mb-2">
                        <input type="text" name="consumer_number" id="cnum" class="form-control fw-bold fs-4 text-primary bg-light" placeholder="Ref ID" required autocomplete="off">
                        <label>CONSUMER ID (Click for List)</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" name="consumer_name" id="cname" class="form-control fw-bold bg-light" placeholder="Name" required>
                        <label>NAME</label>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <select name="bill_type" id="btype" class="form-select fw-bold bg-light">
                                    <option value="Electricity">⚡ Elec</option>
                                    <option value="Gas">🔥 Gas</option>
                                    <option value="Water">💧 Water</option>
                                    <option value="Internet">🌐 Net</option>
                                </select>
                                <label>TYPE</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="number" name="amount" id="camount" class="form-control fw-bold text-success bg-light" placeholder="0">
                                <label>AMOUNT</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-pill shadow">
                        CONFIRM ENTRY <i class="fas fa-check ms-2"></i>
                    </button>
                </form>
                
                <canvas id="processCanvas" style="display:none;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
let currentMode = 'qr';
let html5QrCode = null;
let streamRef = null;

$(document).ready(function() {
    // --- SMART AUTOCOMPLETE ---
    $("#cnum").autocomplete({
        source: function(request, response) {
            $.getJSON("api_search.php", {
                type: 'consumer_suggest',
                term: request.term // Send text, or empty string
            }, response);
        },
        minLength: 0, // IMPORTANT: Allows triggering with 0 characters
        select: function(event, ui) {
            $('#cname').val(ui.item.data.name).css('background-color', '#d1e7dd');
            $('#btype').val(ui.item.data.type);
            $('#camount').focus();
        }
    }).focus(function() {
        // Trigger search immediately when box is clicked
        $(this).autocomplete("search", $(this).val());
    });
});

function showSuccessOverlay() {
    $('#success-overlay').css('display', 'flex');
    $('#success-overlay').fadeIn(100).delay(800).fadeOut(200, function() {
        window.location.href = 'bills.php';
    });
}

function setMode(mode) {
    stopAll();
    currentMode = mode;
    $('.mode-btn').removeClass('active');
    $('#btn-' + mode).addClass('active');
}

function startSelectedMode() {
    $('#start-area').hide();
    $('#scanner-container').slideDown();
    if(currentMode === 'text') { startCameraForText(); } else { startCodeScanner(); }
}

function stopAll() {
    if(html5QrCode) { html5QrCode.stop().then(() => { html5QrCode.clear(); }); html5QrCode = null; }
    if(streamRef) { streamRef.getTracks().forEach(track => track.stop()); streamRef = null; }
    $('#videoElement').hide();
    $('#reader').show();
    $('#capture-btn').hide();
    $('#scanner-container').hide();
    $('#start-area').show();
}

function startCodeScanner() {
    html5QrCode = new Html5Qrcode("reader");
    let formats = (currentMode === 'qr') ? [Html5QrcodeSupportedFormats.QR_CODE] : [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39, Html5QrcodeSupportedFormats.EAN_13];
    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 150 }, formatsToSupport: formats }, (decodedText) => {
        let clean = decodedText.replace(/[^0-9]/g, '');
        if(clean.length > 5) {
            fillData(clean);
            stopAll();
        }
    });
}

function startCameraForText() {
    $('#reader').hide(); $('#videoElement').show(); $('#capture-btn').show();
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(stream => {
        document.getElementById('videoElement').srcObject = stream;
        streamRef = stream;
    });
}

function captureText() {
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('processCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth; canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    
    // Binarization for better OCR
    let imgData = ctx.getImageData(0,0, canvas.width, canvas.height);
    let d = imgData.data;
    for (let i = 0; i < d.length; i += 4) {
        let avg = (d[i] + d[i+1] + d[i+2]) / 3;
        let color = avg > 80 ? 255 : 0; 
        d[i] = d[i+1] = d[i+2] = color;
    }
    ctx.putImageData(imgData, 0, 0);
    
    Tesseract.recognize(canvas, 'eng').then(({ data: { text } }) => {
        const numMatch = text.match(/\d{10,20}/);
        if (numMatch) { fillData(numMatch[0]); stopAll(); } 
        else { alert("Text unclear. Try again."); }
    });
}

function fillData(num) {
    $('#cnum').val(num);
    if(num.length === 14) $('#btype').val('Electricity');
    if(num.length === 11) $('#btype').val('Gas');
    
    // Trigger autocomplete logic manually to fill name if known
    $.getJSON("api_search.php?type=consumer&term=" + num, function(data) {
        if(data.status === 'found') {
            $('#cname').val(data.name).css('background-color', '#d1e7dd'); 
            $('#btype').val(data.type);
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>