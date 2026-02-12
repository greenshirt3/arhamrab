<?php
require 'includes/config.php';

// Fetch Current Serving
$today = date('Y-m-d');
$current = $pdo->query("SELECT * FROM queue_tokens WHERE status='served' AND DATE(served_at) = '$today' ORDER BY served_at DESC LIMIT 1")->fetch();

// Fetch Next 4 Waiting
$waiting = $pdo->query("SELECT * FROM queue_tokens WHERE status='waiting' ORDER BY id ASC LIMIT 4")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #000; 
            color: #fff; 
            height: 100vh; 
            overflow: hidden; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .container-fluid { height: 100%; padding: 0; }
        
        /* LEFT SIDE: NOW SERVING (Green) */
        .serving-section { 
            background: linear-gradient(135deg, #004d26 0%, #006400 100%);
            height: 100vh; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            border-right: 8px solid #FFC107;
            text-align: center;
        }
        
        .label { 
            font-size: 3vw; 
            text-transform: uppercase; 
            letter-spacing: 5px; 
            opacity: 0.8; 
            margin-bottom: 20px; 
        }
        
        .token-main { 
            font-size: 18vw; 
            font-weight: 900; 
            line-height: 1; 
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: pulse 2s infinite;
        }
        
        .name-main { 
            font-size: 4vw; 
            margin-top: 20px; 
            color: #FFC107; 
            font-weight: bold;
        }

        /* RIGHT SIDE: WAITING LIST (Dark) */
        .waiting-section { 
            background: #111; 
            height: 100vh; 
            padding: 40px; 
        }

        .waiting-title { 
            font-size: 3vw; 
            color: #fff; 
            border-bottom: 2px solid #333; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
            font-weight: bold;
            display: flex; align-items: center; gap: 20px;
        }

        .list-group-item { 
            background: #1e1e1e; 
            color: #fff; 
            border: 1px solid #333; 
            font-size: 3vw; 
            font-weight: bold; 
            padding: 25px; 
            margin-bottom: 20px; 
            border-radius: 20px !important; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .badge-custom { 
            background: #333; 
            color: #aaa; 
            font-size: 2vw; 
            padding: 10px 20px; 
            border-radius: 15px; 
        }

    </style>
</head>
<body>

<div class="row g-0">
    <div class="col-8 serving-section">
        <div class="label"><i class="fas fa-bell me-3"></i> Now Serving</div>
        <div class="token-main animate__animated animate__zoomIn">
            <?php echo $current ? $current['token_number'] : '--'; ?>
        </div>
        <div class="name-main">
            <?php echo $current ? $current['name'] : 'Counter Closed'; ?>
        </div>
    </div>

    <div class="col-4 waiting-section">
        <div class="waiting-title">
            <i class="fas fa-users text-primary"></i> Next in Line
        </div>
        
        <?php if(count($waiting) > 0): ?>
            <ul class="list-group">
                <?php foreach($waiting as $w): ?>
                <li class="list-group-item animate__animated animate__fadeInRight">
                    <span class="text-warning"><?php echo $w['token_number']; ?></span>
                    <span class="badge-custom">WAITING</span>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="text-center text-muted mt-5">
                <i class="fas fa-coffee fa-4x mb-3 opacity-25"></i>
                <h3>Queue Empty</h3>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>