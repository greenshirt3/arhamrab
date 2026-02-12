<?php
require 'includes/config.php';

// --- 1. DATA ENGINE ---
$today = date('Y-m-d');
$now_time = date('H:i');

// Default Settings
$s = ['status_color' => 'success', 'status_text' => 'Welcome', 'announcement' => 'System Online', 'shop_open_time' => '09:00', 'shop_close_time' => '17:00', 'enable_auto_hours' => '0', 'service_speed_mins' => 5];

try {
    $rows = $pdo->query("SELECT * FROM system_settings")->fetchAll();
    foreach($rows as $r) $s[$r['setting_key']] = $r['setting_value'];
} catch (Exception $e) { }

// --- 2. LOGIC CORE ---
$system_status = "open"; 
$block_reason = "";

// Time & Admin Logic
if ($s['status_color'] == 'danger') {
    $system_status = "closed";
    $block_reason = "سسٹم بند ہے / System Closed";
}
if ($s['enable_auto_hours'] == '1') {
    if ($now_time < $s['shop_open_time'] || $now_time > $s['shop_close_time']) {
        $system_status = "closed";
        $block_reason = "وقت ختم ہو گیا ہے";
    }
}

// Stats
$current_token = "--";
$total_served = 0;
$people_waiting = 0;

if($system_status == 'open') {
    $current_token = $pdo->query("SELECT token_number FROM queue_tokens WHERE status='served' AND DATE(served_at) = '$today' ORDER BY served_at DESC LIMIT 1")->fetchColumn() ?: "--";
    $total_served = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='served' AND DATE(served_at) = '$today'")->fetchColumn();
    $people_waiting = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='waiting'")->fetchColumn();
}

// Visual Logic
$status_badge = "🟢 ONLINE";
$status_class = "glow-green";
$hero_gradient = "linear-gradient(135deg, #004d26 0%, #002914 100%)"; // Emerald

if ($system_status == 'closed') {
    $status_badge = "🔴 CLOSED";
    $status_class = "glow-red";
    $hero_gradient = "linear-gradient(135deg, #4d0000 0%, #290000 100%)"; // Red
}
if ($s['status_color'] == 'warning' && $system_status == 'open') {
    $status_badge = "⚠️ RUSH HOURS";
    $status_class = "glow-yellow";
    $hero_gradient = "linear-gradient(135deg, #4d3900 0%, #291f00 100%)"; // Gold
}

// --- 3. FORM HANDLER ---
$booking_msg = ""; $track_msg = ""; $booking_card = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // BOOKING
    if (isset($_POST['action']) && $_POST['action'] == 'book') {
        if ($system_status != 'open') {
            $booking_msg = "<div class='alert alert-danger shadow-sm border-0 urdu-font fs-5'>❌ معذرت، $block_reason</div>";
        } else {
            $cnic = preg_replace('/[^0-9]/', '', $_POST['cnic']);
            $cnic_display = substr($cnic, 0, 5) . '-' . substr($cnic, 5, 7) . '-' . substr($cnic, 12, 1);
            
            $check = $pdo->prepare("SELECT token_number FROM queue_tokens WHERE REPLACE(cnic, '-', '') = ? AND DATE(issued_at) = '$today'");
            $check->execute([$cnic]);
            $existing = $check->fetch();

            if ($existing) {
                $booking_msg = "<div class='alert alert-warning shadow-sm border-0 text-center'><span class='urdu-font fs-4'>آپ کا ٹوکن پہلے سے موجود ہے</span><br><b class='fs-1 text-dark'>{$existing['token_number']}</b></div>";
            } else {
                $cnt = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE DATE(issued_at) = CURDATE()")->fetchColumn() + 1;
                $t_num = date('d') . '-' . str_pad($cnt, 3, '0', STR_PAD_LEFT);
                $stmt = $pdo->prepare("INSERT INTO queue_tokens (token_number, cnic, name, issued_by, is_online) VALUES (?, ?, 'Online User', 999, 1)");
                $stmt->execute([$t_num, $cnic_display]);
                
                $speed = $s['service_speed_mins'] ?? 5;
                $wait = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='waiting'")->fetchColumn() * $speed;
                $time = date('h:i A', strtotime("+$wait minutes"));
                
                $booking_card = ['number'=>$t_num, 'time'=>$time, 'wait'=>$wait];
                $booking_msg = "success";
            }
        }
    }
    // TRACKING
    if (isset($_POST['action']) && $_POST['action'] == 'track') {
        $t_num = $_POST['token_num'];
        $row = $pdo->query("SELECT * FROM queue_tokens WHERE token_number = '$t_num' AND DATE(issued_at) = '$today'")->fetch();
        if (!$row) $track_msg = "<div class='alert alert-danger shadow-sm border-0 text-center urdu-font fs-5'>یہ ٹوکن نمبر غلط ہے۔</div>";
        elseif ($row['status'] == 'served') $track_msg = "<div class='alert alert-success shadow-sm border-0 text-center urdu-font fs-4'><b>مبارک ہو!</b><br>آپ کی باری آ چکی ہے۔</div>";
        else {
            $ahead = $pdo->query("SELECT COUNT(*) FROM queue_tokens WHERE status='waiting' AND id < {$row['id']}")->fetchColumn();
            $track_msg = "<div class='text-center p-4 glass-card'><h1 class='text-primary display-2 fw-bold mb-0'>$ahead</h1><p class='text-muted urdu-font fs-4 fw-bold'>لوگ آپ سے پہلے ہیں</p></div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Arham BISP Portal</title>
    
    <link rel="manifest" href="manifest.json">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2910/2910768.png" type="image/png">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        /* === 1. CORE DESIGN SYSTEM === */
        @font-face {
            font-family: 'Jameel Noori Nastaleeq';
            src: url('Jameel Noori Nastaleeq.ttf') format('truetype');
            font-display: swap;
        }
        :root {
            --emerald: #004d26;
            --emerald-light: #007038;
            --gold: #FFD700;
            --dark: #0a0a0a;
            --glass: rgba(255, 255, 255, 0.9);
            --shadow-3d: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        body { 
            background-color: #f4f6f8; 
            font-family: 'Poppins', sans-serif; 
            padding-bottom: 80px; 
            overflow-x: hidden; 
        }
        .urdu-font { font-family: 'Jameel Noori Nastaleeq', serif; line-height: 1.8; }
        .eng-font { font-family: 'Poppins', sans-serif; direction: ltr; display: inline-block; }

        /* === 2. HERO SECTION (3D) === */
        .hero {
            position: relative;
            background: <?php echo $hero_gradient; ?>;
            color: white;
            padding: 20px 20px 80px;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            box-shadow: 0 20px 50px rgba(0,77,38,0.3);
            text-align: center;
            overflow: hidden;
            z-index: 1;
        }
        
        /* Floating Particles Background */
        .particles {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.4;
            animation: float 60s linear infinite;
            z-index: -1;
        }
        @keyframes float { from { transform: translateY(0); } to { transform: translateY(-50%); } }

        /* Navbar */
        .app-nav {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px;
        }
        .brand-pill {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,0.2);
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 14px;
        }
        .admin-btn {
            color: white; opacity: 0.7; font-size: 20px; transition: 0.3s;
        }
        .admin-btn:hover { opacity: 1; color: var(--gold); transform: rotate(20deg); }

        /* Status Ring */
        .status-ring {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            background: rgba(0,0,0,0.4);
            border: 2px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 2px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .glow-green { border-color: #0f0; color: #0f0; box-shadow: 0 0 15px rgba(0,255,0,0.3); animation: pulseG 2s infinite; }
        .glow-red { border-color: #f00; color: #f00; box-shadow: 0 0 15px rgba(255,0,0,0.3); }
        .glow-yellow { border-color: #ff0; color: #ff0; box-shadow: 0 0 15px rgba(255,255,0,0.3); animation: pulseY 1s infinite; }
        
        @keyframes pulseG { 0% { box-shadow: 0 0 0 0 rgba(0,255,0,0.7); } 70% { box-shadow: 0 0 0 10px rgba(0,255,0,0); } 100% { box-shadow: 0 0 0 0 rgba(0,255,0,0); } }

        /* 3D Token Counter */
        .token-box {
            background: rgba(255,255,255,0.1);
            border-top: 1px solid rgba(255,255,255,0.5);
            border-left: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            padding: 20px 40px;
            display: inline-block;
            box-shadow: 20px 20px 50px rgba(0,0,0,0.2);
            backdrop-filter: blur(15px);
            margin: 10px 0;
            transform: perspective(1000px) rotateX(10deg);
            transition: 0.3s;
        }
        .token-box:hover { transform: perspective(1000px) rotateX(0deg) scale(1.05); }
        
        .token-val { font-size: 70px; font-weight: 800; line-height: 1; color: white; text-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .token-lbl { font-size: 18px; color: rgba(255,255,255,0.8); }

        /* === 3. 3D GLASS CARDS === */
        .card-grid { margin-top: -60px; padding: 0 15px; position: relative; z-index: 10; }
        
        .glass-card {
            background: white;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,1);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            opacity: 0; transition: 0.5s;
        }
        .glass-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px rgba(0,0,0,0.15); }
        .glass-card:hover::before { opacity: 1; }
        
        .icon-3d {
            width: 60px; height: 60px; margin: 0 auto 15px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            box-shadow:  5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;
            color: var(--emerald);
        }
        
        /* Stats Section */
        .stats-strip {
            display: flex; justify-content: space-around;
            background: white; border-radius: 15px;
            padding: 20px; margin-top: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .stat-item h3 { margin: 0; font-size: 24px; font-weight: 800; color: var(--emerald); }
        .stat-item p { margin: 0; font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; }

        /* Tickers & Footer */
        .top-ticker { background: #000; color: var(--gold); font-size: 14px; padding: 8px 0; }
        .bottom-ticker {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #fff; border-top: 1px solid #eee;
            padding: 10px 0; z-index: 100;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
        }
        .ticker-text { font-size: 16px; color: var(--emerald); font-weight: bold; }

        /* Modal Polish */
        .modal-content { border-radius: 25px; border: none; overflow: hidden; }
        .modal-header { border-bottom: none; padding: 20px 20px 0; }
        .modal-body { padding: 30px; }
        .form-control-lg { border-radius: 15px; background: #f8f9fa; border: none; padding: 15px; font-weight: bold; font-size: 24px; letter-spacing: 2px; }
        .form-control-lg:focus { background: #fff; box-shadow: 0 0 0 4px rgba(0,230,118,0.2); }
    </style>
</head>
<body>

    <div class="top-ticker urdu-font" dir="rtl">
        <marquee scrollamount="6">
            🔔 <b>تازہ ترین:</b> <?php echo htmlspecialchars($s['announcement']); ?> &nbsp;&nbsp; • &nbsp;&nbsp; 🕒 دکان کے اوقات: <?php echo date('h:i A', strtotime($s['shop_open_time'])); ?> سے <?php echo date('h:i A', strtotime($s['shop_close_time'])); ?> تک
        </marquee>
    </div>

    <div class="hero">
        <div class="particles"></div>
        
        <div class="app-nav">
            <a href="https://portal.arhamprinters.pk/login.php" class="admin-btn"><i class="fas fa-lock"></i></a>
            <div class="brand-pill eng-font text-uppercase">ARHAM <span class="text-warning">PRINTERS</span></div>
            <div class="admin-btn" onclick="location.reload()"><i class="fas fa-sync-alt"></i></div>
        </div>

        <div class="status-ring <?php echo $status_class; ?> eng-font">
            <span class="fas fa-circle me-2" style="font-size:10px;"></span> <?php echo $status_badge; ?>
        </div>

        <div class="animate__animated animate__zoomIn">
            <?php if($system_status == 'open'): ?>
                <div class="token-box">
                    <div class="token-lbl urdu-font">ابھی ٹوکن نمبر چل رہا ہے</div>
                    <div class="token-val eng-font"><?php echo $current_token; ?></div>
                </div>
            <?php else: ?>
                <div class="py-4">
                    <i class="fas fa-moon fa-4x opacity-50 mb-3 text-warning"></i>
                    <h2 class="urdu-font fw-bold">سسٹم بند ہے</h2>
                    <p class="small opacity-75 eng-font">See you tomorrow!</p>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="urdu-font mt-3 fw-bold animate__animated animate__fadeInUp"><?php echo htmlspecialchars($s['status_text']); ?></h2>
        
        <?php if($system_status == 'open'): ?>
        <button onclick="playVoice()" class="btn btn-outline-light rounded-pill px-4 mt-2 fw-bold border-2">
            <i class="fas fa-volume-up me-2"></i> Voice Call
        </button>
        <?php endif; ?>
    </div>

    <div class="container cards-grid card-grid">
        <div class="row g-3 justify-content-center">
            
            <div class="col-md-4 col-12">
                <div class="glass-card animate__animated animate__fadeInUp animate__delay-1s"
                     <?php echo ($system_status == 'open') ? 'data-bs-toggle="modal" data-bs-target="#bookModal"' : 'onclick="alert(\'دکان بند ہے\')"'; ?>>
                    <div class="icon-3d text-success"><i class="fas fa-ticket-alt"></i></div>
                    <h3 class="urdu-font fs-2 fw-bold text-dark mb-0">ٹوکن حاصل کریں</h3>
                    <p class="text-muted small eng-font mb-0">Get Token Online</p>
                </div>
            </div>

            <div class="col-md-4 col-6">
                <div class="glass-card animate__animated animate__fadeInUp animate__delay-1s" data-bs-toggle="modal" data-bs-target="#trackModal">
                    <div class="icon-3d text-primary"><i class="fas fa-search-location"></i></div>
                    <h4 class="urdu-font fw-bold text-dark mb-0">نمبر چیک کریں</h4>
                    <p class="text-muted small eng-font mb-0">Track Status</p>
                </div>
            </div>

            <div class="col-md-4 col-6">
                <a href="https://wa.me/923006238233" class="text-decoration-none">
                    <div class="glass-card animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="icon-3d text-success"><i class="fab fa-whatsapp"></i></div>
                        <h4 class="urdu-font fw-bold text-dark mb-0">رابطہ کریں</h4>
                        <p class="text-muted small eng-font mb-0">Help & Support</p>
                    </div>
                </a>
            </div>

        </div>

        <div class="stats-strip animate__animated animate__fadeInUp animate__delay-2s">
            <div class="stat-item text-center border-end w-50">
                <h3 class="eng-font"><?php echo $total_served; ?></h3>
                <p>Served Today</p>
            </div>
            <div class="stat-item text-center w-50">
                <h3 class="eng-font"><?php echo $people_waiting; ?></h3>
                <p>Waiting Now</p>
            </div>
        </div>
        
        <div class="text-center mt-4 mb-5 pb-5">
            <small class="text-muted eng-font">SECURE BISP DISBURSEMENT CENTER • JALALPUR JATTAN</small>
        </div>
    </div>

    <div class="bottom-ticker urdu-font" dir="rtl">
        <marquee scrollamount="7" class="ticker-text">
            ارہم پرنٹرز جلالپور جتاں: BISP کی رقم بائیو میٹرک کے ذریعے حاصل کریں • شادی کارڈ • فلیکس پرنٹنگ • بل بکس • ویب سائٹ اور سافٹ ویئر بنوانے کے لیے رابطہ کریں: 0300-6238233
        </marquee>
    </div>

    <div class="modal fade" id="bookModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-success text-white justify-content-center py-3">
                    <h5 class="fw-bold m-0 urdu-font fs-2">ٹوکن بکنگ</h5>
                </div>
                <div class="modal-body text-center bg-white">
                    <?php if($booking_msg == 'success' && $booking_card): ?>
                        <div class="py-2">
                            <i class="fas fa-check-circle text-success fa-5x mb-3 animate__animated animate__bounceIn"></i>
                            <h2 class="display-3 fw-bold eng-font text-dark mb-0"><?php echo $booking_card['number']; ?></h2>
                            <p class="text-muted urdu-font fs-3 mb-4">یہ آپ کا ٹوکن نمبر ہے</p>
                            
                            <div class="bg-light rounded-3 p-3 mb-3 border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="urdu-font text-muted">متوقع وقت:</span>
                                    <span class="fw-bold text-dark eng-font"><?php echo $booking_card['time']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="urdu-font text-muted">انتظار:</span>
                                    <span class="fw-bold text-dark eng-font"><?php echo $booking_card['wait']; ?> mins</span>
                                </div>
                            </div>
                            <button class="btn btn-dark w-100 rounded-pill py-3 urdu-font fs-4 shadow" onclick="window.location.href='index.php'">شکریہ</button>
                        </div>
                    <?php else: ?>
                        <?php echo $booking_msg; ?>
                        <form method="POST" dir="ltr" class="mt-3">
                            <input type="hidden" name="action" value="book">
                            <div class="form-floating mb-4">
                                <input type="tel" name="cnic" class="form-control form-control-lg text-center text-success" placeholder="CNIC" required maxlength="15">
                                <label class="text-center w-100">CNIC Number (بغیر ڈیش)</label>
                            </div>
                            <button class="btn btn-success w-100 rounded-pill py-3 fw-bold fs-3 shadow urdu-font hover-effect">
                                ٹوکن حاصل کریں <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="trackModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning text-dark justify-content-center py-3">
                    <h5 class="fw-bold m-0 urdu-font fs-2">اسٹیٹس چیک کریں</h5>
                </div>
                <div class="modal-body p-4 bg-white">
                    <?php if($track_msg) echo $track_msg; ?>
                    <form method="POST" class="mt-3" dir="ltr">
                        <input type="hidden" name="action" value="track">
                        <div class="form-floating mb-3">
                            <input type="text" name="token_num" class="form-control form-control-lg text-center" placeholder="Token" required>
                            <label class="text-center w-100">Token Number (e.g. 05-001)</label>
                        </div>
                        <button class="btn btn-dark w-100 rounded-pill py-3 fw-bold fs-4 shadow urdu-font">چیک کریں</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function playVoice() {
        if ('speechSynthesis' in window) {
            var text = "خوش آمدید۔ ابھی ٹوکن نمبر <?php echo $current_token; ?> چل رہا ہے۔";
            var msg = new SpeechSynthesisUtterance(text);
            msg.lang = 'ur-PK'; msg.rate = 0.9;
            window.speechSynthesis.speak(msg);
        } else { alert("Audio not supported"); }
    }
    <?php if($booking_msg): ?> new bootstrap.Modal(document.getElementById('bookModal')).show(); <?php endif; ?>
    <?php if($track_msg): ?> new bootstrap.Modal(document.getElementById('trackModal')).show(); <?php endif; ?>
    </script>
</body>
</html>