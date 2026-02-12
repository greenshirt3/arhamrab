<?php
// 1. ENABLE ERROR REPORTING (To help you debug JSON issues)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
$subdomain = $parts[0];

$json_file = 'customers.json';

// 2. ROBUST JSON LOADING
if (!file_exists($json_file)) { 
    die("Error: customers.json missing."); 
}

$json_content = file_get_contents($json_file);
$customers = json_decode($json_content, true);

// Check if JSON is valid
if ($customers === null) {
    die("JSON Syntax Error: " . json_last_error_msg() . "<br>Please check your comma placement in customers.json");
}

if (isset($customers[$subdomain])) {
    $c = $customers[$subdomain];
} else {
    // Default Fallback
    $c = [
        'type' => 'business', 
        'company' => 'Arham Printers', 
        'name' => 'Digital Card', 
        'color' => '#0d6efd', 
        'phone' => '923006238233', 
        'whatsapp' => '923006238233', 
        'address' => 'Jalalpur Jattan', 
        'logo' => '../img/logo2.webp'
    ];
}

$type = $c['type'] ?? 'business';

function getFullUrl($path) {
    if (strpos($path, 'http') === 0) return $path;
    return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$path";
}

// =========================================================
//  LAYOUT 1: WEDDING INVITATION
// =========================================================
if ($type == 'wedding') {
    $gold = $c['color'] ?? '#d4af37';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invitation - <?php echo $c['groom_bride_name']; ?></title>
    <link href="https://arhamprinters.pk/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --gold: <?php echo $gold; ?>; --cream: #fffbf0; }
        body {
            background-color: #f4f4f4;
            background-image: url('https://www.transparenttextures.com/patterns/black-orchid.png'); 
            font-family: 'Lato', sans-serif;
            color: #444;
            min-height: 100vh;
            padding: 20px 0;
        }
        .wedding-card {
            max-width: 900px; margin: 0 auto; background: white;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15); position: relative;
            overflow: hidden; border: 15px solid white;
            outline: 2px solid var(--gold); outline-offset: -10px;
        }
        .corner-decoration {
            position: absolute; width: 100px; height: 100px; 
            background-image: url('https://www.transparenttextures.com/patterns/flower-trail.png');
            opacity: 0.3; pointer-events: none; z-index: 0;
        }
        .top-left { top: 0; left: 0; transform: rotate(0deg); }
        .top-right { top: 0; right: 0; transform: rotate(90deg); }
        .bottom-left { bottom: 0; left: 0; transform: rotate(-90deg); }
        .bottom-right { bottom: 0; right: 0; transform: rotate(180deg); }

        .header-section {
            text-align: center; padding: 80px 20px 50px;
            background: radial-gradient(circle, var(--cream) 0%, #fff 80%);
            border-bottom: 1px solid #eee; position: relative; z-index: 1;
        }
        .bismillah {
            font-family: 'Great Vibes', cursive; font-size: 2.5rem; color: var(--gold); margin-bottom: 25px;
        }
        .host-names {
            font-family: 'Cinzel', serif; font-weight: 700; font-size: 1.1rem;
            text-transform: uppercase; letter-spacing: 2px; color: #333;
            margin-bottom: 15px; border-top: 1px solid var(--gold); border-bottom: 1px solid var(--gold);
            display: inline-block; padding: 10px 30px;
        }
        .invitation-text { font-style: italic; color: #666; font-size: 1rem; margin: 20px 0; }
        .couple-name {
            font-family: 'Great Vibes', cursive; font-size: 4rem; color: var(--gold);
            line-height: 1.1; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); margin: 10px 0;
        }
        .details-container { padding: 40px 20px; position: relative; z-index: 1; }
        .side-panel {
            background: var(--cream); padding: 30px 20px; text-align: center;
            border-radius: 4px; border: 1px solid rgba(212, 175, 55, 0.3); height: 100%;
        }
        .panel-title {
            font-family: 'Cinzel', serif; color: var(--gold); font-weight: bold; font-size: 1.2rem;
            border-bottom: 2px solid white; padding-bottom: 10px; margin-bottom: 20px;
        }
        .name-list p { margin: 8px 0; font-weight: 600; font-size: 0.95rem; color: #555; font-family: 'Cinzel', serif; }
        
        .event-card {
            background: white; border: 1px solid var(--gold); padding: 25px; margin-bottom: 30px;
            text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative;
        }
        .event-card::before {
            content: '\f06c'; font-family: 'Font Awesome 5 Free'; font-weight: 900;
            position: absolute; top: -15px; left: 50%; transform: translateX(-50%);
            background: white; padding: 0 10px; color: var(--gold); font-size: 1.2rem;
        }
        .event-title { font-family: 'Great Vibes', cursive; font-size: 2.5rem; color: var(--gold); margin-bottom: 5px; }
        .event-date { font-weight: bold; font-size: 1.1rem; color: #333; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }
        .time-row {
            display: flex; justify-content: space-between; border-bottom: 1px dashed #ddd;
            padding: 8px 0; width: 80%; margin: 0 auto; font-size: 0.95rem;
        }
        .venue-display { margin-top: 20px; padding: 15px; background: #fafafa; border-radius: 8px; }
        .venue-text { font-weight: bold; color: #444; display: block; margin-bottom: 5px; }
        .venue-address { font-size: 0.85rem; color: #777; display: block; margin-bottom: 10px; }
        
        .contact-footer { background: #2c3e50; color: white; text-align: center; padding: 40px 20px; position: relative; z-index: 1; }
        .contact-info-text { font-size: 1.1rem; margin-bottom: 20px; font-family: 'Cinzel', serif; }
        .contact-number-display { font-size: 1.5rem; font-weight: bold; color: var(--gold); display: block; margin-top: 5px; }
        
        .footer-icons { display: flex; justify-content: center; gap: 20px; margin-top: 20px; }
        .icon-btn {
            width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.1);
            color: white; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;
            text-decoration: none; border: 1px solid var(--gold); transition: 0.3s;
        }
        .icon-btn:hover { background: var(--gold); color: white; transform: scale(1.1); }
        .branding { margin-top: 30px; font-size: 0.7rem; color: #888; letter-spacing: 1px; }
        .branding a { color: #aaa; text-decoration: none; }

        @media(max-width: 768px) {
            .wedding-card { border-width: 0; outline: none; }
            .couple-name { font-size: 3rem; }
            .details-container > .row { flex-direction: column; }
            .mobile-order-1 { order: 1; }
            .mobile-order-2 { order: 2; }
            .mobile-order-3 { order: 3; }
        }
    </style>
</head>
<body>

<div class="wedding-card">
    <div class="corner-decoration top-left"></div>
    <div class="corner-decoration top-right"></div>
    <div class="corner-decoration bottom-left"></div>
    <div class="corner-decoration bottom-right"></div>

    <div class="header-section">
        <div class="bismillah">﷽</div>
        <div class="host-names"><?php echo $c['host_parents']; ?></div>
        <div class="invitation-text">
            Request the honor of your presence at the wedding ceremony of their beloved <?php echo $c['relation']; ?>
        </div>
        <div class="couple-name"><?php echo $c['groom_bride_name']; ?></div>
    </div>

    <div class="details-container">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-md-3 mobile-order-2">
                    <div class="side-panel">
                        <div class="panel-title">R.S.V.P</div>
                        <div class="name-list">
                            <?php foreach($c['rsvp'] as $name) { echo "<p>$name</p>"; } ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mobile-order-1">
                    <?php foreach($c['events'] as $event): ?>
                    <div class="event-card">
                        <div class="event-title"><?php echo $event['name']; ?></div>
                        <div class="event-date">
                            <i class="far fa-calendar-alt me-2"></i> <?php echo $event['date']; ?>
                        </div>
                        <div class="time-table">
                            <?php foreach($event['times'] as $time): ?>
                            <div class="time-row">
                                <span><?php echo $time['label']; ?></span>
                                <strong><?php echo $time['time']; ?></strong>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="venue-display">
                            <i class="fas fa-map-marker-alt text-danger mb-1"></i>
                            <span class="venue-text"><?php echo $event['venue']; ?></span>
                            <span class="venue-address">
                                <?php echo isset($event['address']) ? $event['address'] : 'Click button below for map location'; ?>
                            </span>
                            <?php if(!empty($event['map_link'])): ?>
                            <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill mt-2" style="font-size: 0.8rem;">
                                View Map Location
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-md-3 mobile-order-3">
                    <div class="side-panel">
                        <div class="panel-title">Well Wishers</div>
                        <div class="name-list">
                            <?php foreach($c['well_wishers'] as $name) { echo "<p>$name</p>"; } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-footer">
        <div class="contact-info-text">
            For more details & location queries:
            <span class="contact-number-display"><?php echo $c['contact_phone']; ?></span>
        </div>
        <div class="footer-icons">
            <a href="tel:<?php echo $c['contact_phone']; ?>" class="icon-btn"><i class="fas fa-phone-alt"></i></a>
            <a href="https://wa.me/<?php echo $c['contact_whatsapp']; ?>" class="icon-btn"><i class="fab fa-whatsapp"></i></a>
        </div>
        <div class="branding">
            Digital Invite Powered by <a href="https://arhamprinters.pk" target="_blank">ARHAM PRINTERS 0300-6238233</a>
        </div>
    </div>
</div>
</body>
</html>

<?php 

} elseif ($type == 'store') {
    $theme = $c['theme']['primary'] ?? '#2f3542';
    $currency = $c['currency'] ?? 'Rs.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $c['company']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --app-color: <?php echo $theme; ?>; --bg-gray: #f4f6f8; }
        body { background: var(--bg-gray); font-family: 'Segoe UI', sans-serif; padding-bottom: 100px; -webkit-tap-highlight-color: transparent; }
        
        /* 1. App Header */
        .app-header {
            background: white; padding: 12px 15px; position: sticky; top: 0; z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;
        }
        .brand-area { display: flex; align-items: center; gap: 10px; }
        .app-logo { height: 38px; width: 38px; border-radius: 8px; object-fit: contain; border: 1px solid #eee; }
        .app-title { font-weight: 800; font-size: 1.1rem; color: #2d3436; margin: 0; letter-spacing: -0.5px; }
        .header-icons .btn-icon { color: #2d3436; font-size: 1.2rem; margin-left: 15px; position: relative; }

        /* 2. Hero Banner */
        .hero-banner {
            margin: 15px; border-radius: 15px; overflow: hidden; position: relative; height: 160px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .hero-banner img { width: 100%; height: 100%; object-fit: cover; }
        .hero-overlay {
            position: absolute; bottom: 0; left: 0; right: 0; padding: 20px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); color: white;
        }

        /* 3. Category Pills (Horizontal Scroll) */
        .cat-scroll {
            display: flex; overflow-x: auto; gap: 10px; padding: 0 15px 15px; scrollbar-width: none;
        }
        .cat-scroll::-webkit-scrollbar { display: none; }
        .cat-pill {
            background: white; padding: 8px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600;
            color: #636e72; white-space: nowrap; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .cat-pill.active { background: var(--app-color); color: white; border-color: var(--app-color); }

        /* 4. Product Grid (2 Columns Mobile) */
        .prod-grid { padding: 0 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        @media(min-width: 768px) { .prod-grid { grid-template-columns: repeat(4, 1fr); } }

        .prod-card {
            background: white; border-radius: 12px; overflow: hidden; position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: transform 0.2s; display: flex; flex-direction: column;
        }
        .prod-card:active { transform: scale(0.98); }
        
        .prod-img-wrap {
            width: 100%; aspect-ratio: 1/1; padding: 15px; background: white;
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        .prod-img { max-width: 100%; max-height: 100%; object-fit: contain; mix-blend-mode: multiply; }
        
        .prod-info { padding: 12px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .prod-cat { font-size: 0.65rem; text-transform: uppercase; color: #b2bec3; font-weight: 700; letter-spacing: 0.5px; }
        .prod-name { font-size: 0.9rem; font-weight: 700; color: #2d3436; margin: 3px 0 5px; line-height: 1.3;
                      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .prod-meta { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
        .prod-price { font-weight: 800; color: var(--app-color); font-size: 1rem; }
        
        .btn-add-mini {
            width: 32px; height: 32px; border-radius: 50%; background: #f1f2f6; color: var(--app-color);
            display: flex; align-items: center; justify-content: center; border: none; transition: 0.2s;
        }
        .btn-add-mini:active, .btn-add-mini.added { background: var(--app-color); color: white; }

        /* 5. Sticky Bottom Cart Bar (App Style) */
        .cart-bar {
            position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 999;
            transform: translateY(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .cart-bar.visible { transform: translateY(0); }
        
        .cart-info h6 { margin: 0; font-weight: 800; color: #2d3436; }
        .cart-info small { color: #636e72; font-size: 0.8rem; }
        
        .btn-checkout {
            background: #25D366; color: white; padding: 10px 25px; border-radius: 50px;
            font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }

        /* 6. Footer Branding */
        .store-footer { text-align: center; margin-top: 40px; padding-bottom: 20px; color: #b2bec3; font-size: 0.75rem; }
        .store-footer a { color: #636e72; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <header class="app-header">
        <div class="brand-area">
            <img src="<?php echo $c['logo']; ?>" class="app-logo" onerror="this.src='../img/logo2.webp'">
            <h1 class="app-title"><?php echo $c['company']; ?></h1>
        </div>
        <div class="header-icons">
            <a href="tel:<?php echo $c['phone']; ?>" class="btn-icon"><i class="fas fa-phone-alt"></i></a>
        </div>
    </header>

    <?php if(!empty($c['cover'])): ?>
    <div class="hero-banner">
        <img src="<?php echo $c['cover']; ?>">
        <div class="hero-overlay">
            <h4 class="m-0 fw-bold">New Arrivals</h4>
            <small>Best Prices in City</small>
        </div>
    </div>
    <?php endif; ?>

    <div class="cat-scroll">
        <div class="cat-pill active">All Items</div>
        <div class="cat-pill">Chargers</div>
        <div class="cat-pill">Cables</div>
        <div class="cat-pill">Audio</div>
        <div class="cat-pill">Cases</div>
    </div>

    <div class="prod-grid">
        <?php if(!empty($c['products'])): foreach($c['products'] as $i => $p): ?>
        <div class="prod-card">
            <div class="prod-img-wrap">
                <img src="<?php echo $p['image']; ?>" class="prod-img" onerror="this.src='https://via.placeholder.com/150'">
            </div>
            <div class="prod-info">
                <div>
                    <div class="prod-cat"><?php echo $p['category']; ?></div>
                    <div class="prod-name"><?php echo $p['name']; ?></div>
                </div>
                <div class="prod-meta">
                    <div class="prod-price"><?php echo $currency . $p['price']; ?></div>
                    <button class="btn-add-mini" id="btn-<?php echo $i; ?>" onclick="addToCart(<?php echo $i; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>)">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <div class="store-footer">
        Powered by <a href="https://arhamprinters.pk">Arham Printers</a><br>
        Professional Digital Solutions
    </div>

    <div class="cart-bar" id="cartBar">
        <div class="cart-info">
            <h6 id="cartTotal">Rs. 0</h6>
            <small><span id="cartCount">0</span> items selected</small>
        </div>
        <a href="#" onclick="checkout()" class="btn-checkout">
            WhatsApp Order <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <script>
        let cart = {}; // Using Object for unique items
        const waNumber = "<?php echo $c['whatsapp']; ?>";
        const currency = "<?php echo $currency; ?>";

        function addToCart(id, name, price) {
            // Toggle Logic
            if(cart[id]) {
                delete cart[id];
                document.getElementById(`btn-${id}`).classList.remove('added');
                document.getElementById(`btn-${id}`).innerHTML = '<i class="fas fa-plus"></i>';
            } else {
                cart[id] = { name, price };
                document.getElementById(`btn-${id}`).classList.add('added');
                document.getElementById(`btn-${id}`).innerHTML = '<i class="fas fa-check"></i>';
                
                // Haptic feedback for mobile
                if (navigator.vibrate) navigator.vibrate(50);
            }
            updateCartUI();
        }

        function updateCartUI() {
            const bar = document.getElementById('cartBar');
            const items = Object.values(cart);
            const total = items.reduce((sum, item) => sum + item.price, 0);
            
            document.getElementById('cartCount').innerText = items.length;
            document.getElementById('cartTotal').innerText = currency + ' ' + total;

            if (items.length > 0) {
                bar.classList.add('visible');
                // Adjust body padding so footer isn't hidden
                document.body.style.paddingBottom = "100px";
            } else {
                bar.classList.remove('visible');
                document.body.style.paddingBottom = "40px";
            }
        }

        function checkout() {
            const items = Object.values(cart);
            if(items.length === 0) return;

            let total = 0;
            let msg = "*NEW ORDER REQUEST* \n";
            msg += "store: <?php echo $c['company']; ?>\n";
            msg += "---------------------\n";
            
            items.forEach((item, index) => {
                msg += `• ${item.name} (${currency}${item.price})\n`;
                total += item.price;
            });

            msg += `---------------------\n*Total Bill: ${currency}${total}*\n\nI want to order these items.`;
            
            const url = `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`;
            window.open(url, '_blank');
        }
    </script>

</body>
</html>

<?php
// =========================================================
//  LAYOUT 2: DOCTOR / MEDICAL PROFILE
// =========================================================
} elseif ($type == 'doctor') {
    $theme = $c['theme'] ?? ['primary' => '#0fb9b1', 'text' => '#333', 'background' => '#f1f2f6'];
    
    // Logic for Cover Image with Fallback Gradient
    $header_style = "background: linear-gradient(135deg, {$theme['primary']}, #2bcbba);";
    if (!empty($c['cover'])) {
        $cover_url = getFullUrl($c['cover']);
        // Gradient Overlay + Image
        $header_style = "background: linear-gradient(to bottom, rgba(15, 185, 177, 0.85), rgba(0,0,0,0.5)), url('$cover_url'); background-size: cover; background-position: center;";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $c['name']; ?> - Doctor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --med-blue: <?php echo $theme['primary']; ?>; }
        body { background-color: <?php echo $theme['background']; ?>; font-family: 'Segoe UI', sans-serif; }
        
        .doc-container {
            max-width: 480px; margin: 0 auto; background: white; min-height: 100vh;
            position: relative; box-shadow: 0 0 20px rgba(0,0,0,0.05); padding-bottom: 80px;
        }
        
        /* Updated Header with Cover Support */
        .doc-header {
            padding: 60px 20px 80px; 
            border-bottom-left-radius: 30px; 
            border-bottom-right-radius: 30px;
            color: white; 
            text-align: center;
            position: relative;
        }
        
        .doc-header h5 {
            font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.3); letter-spacing: 0.5px;
        }

        .doc-pic-wrap {
            width: 130px; height: 130px; margin: -65px auto 15px; position: relative; z-index: 2;
        }
        .doc-pic {
            width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
            border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .verified-badge {
            position: absolute; bottom: 5px; right: 5px; background: #3867d6; color: white;
            width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; border: 2px solid white; font-size: 14px;
        }

        /* Info */
        .doc-name { font-weight: 800; font-size: 1.5rem; color: #2d3436; margin-bottom: 2px; }
        .doc-deg { color: var(--med-blue); font-weight: 600; font-size: 0.95rem; margin-bottom: 5px; }
        .doc-hospital { color: #636e72; font-size: 0.9rem; }

        /* About Section */
        .doc-about {
            text-align: center; font-size: 0.95rem; color: #555; line-height: 1.6;
            margin: 20px 20px 10px 20px; padding: 15px; background: #f8f9fa; border-radius: 12px;
            border-left: 4px solid var(--med-blue);
        }

        /* Action Grid */
        .action-grid { display: flex; gap: 10px; justify-content: center; margin: 20px 15px; }
        .act-btn {
            flex: 1; padding: 12px; border-radius: 12px; text-decoration: none; color: white;
            text-align: center; font-size: 0.9rem; font-weight: 600; transition: 0.2s;
            display: flex; flex-direction: column; align-items: center; gap: 5px;
        }
        .act-btn i { font-size: 1.2rem; }
        .btn-call { background: #20bf6b; }
        .btn-wa { background: #25D366; }
        .btn-loc { background: #4b7bec; }
        .act-btn:hover { transform: translateY(-3px); color: white; opacity: 0.9; }

        /* Timings Card */
        .time-card {
            background: #eafef6; border: 1px solid #cbf7e6; margin: 15px 20px; padding: 15px;
            border-radius: 12px; text-align: center; color: #218c74;
        }
        .time-label { font-weight: 700; display: block; margin-bottom: 3px; }

        /* Services List */
        .svc-list { padding: 0 20px; }
        .svc-item {
            display: flex; align-items: center; padding: 12px 0; border-bottom: 1px dashed #eee;
        }
        .svc-icon {
            width: 35px; height: 35px; background: rgba(15, 185, 177, 0.1); color: var(--med-blue);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;
        }
        .svc-text { font-weight: 500; color: #555; }

        /* Footer Bar */
        .book-bar {
            position: fixed; bottom: 0; left: 0; right: 0; background: white;
            padding: 15px 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
            display: flex; justify-content: center; z-index: 99; max-width: 480px; margin: 0 auto;
        }
        .book-btn {
            background: var(--med-blue); color: white; width: 100%; padding: 12px;
            border-radius: 50px; text-align: center; text-decoration: none; font-weight: bold;
            box-shadow: 0 5px 15px rgba(15, 185, 177, 0.4);
        }
    </style>
</head>
<body>

<div class="doc-container">
    <div class="doc-header" style="<?php echo $header_style; ?>">
        <h5 class="m-0"><i class="fas fa-hospital-alt me-2"></i> <?php echo $c['company']; ?></h5>
    </div>

    <div class="doc-pic-wrap">
        <img src="<?php echo $c['profile_pic']; ?>" class="doc-pic" onerror="this.src='../img/logo2.webp'">
        <div class="verified-badge"><i class="fas fa-check"></i></div>
    </div>

    <div class="text-center px-3">
        <h1 class="doc-name"><?php echo $c['name']; ?></h1>
        <div class="doc-deg"><?php echo $c['designation']; ?></div>
        <div class="doc-hospital"><i class="fas fa-map-marker-alt me-1"></i> <?php echo $c['address']; ?></div>
    </div>

    <div class="action-grid">
        <a href="tel:<?php echo $c['phone']; ?>" class="act-btn btn-call">
            <i class="fas fa-phone-alt"></i> Call
        </a>
        <a href="https://wa.me/<?php echo $c['whatsapp']; ?>?text=I+want+an+appointment" class="act-btn btn-wa">
            <i class="fab fa-whatsapp"></i> Chat
        </a>
        <a href="<?php echo $c['map_link']; ?>" target="_blank" class="act-btn btn-loc">
            <i class="fas fa-location-arrow"></i> Map
        </a>
    </div>

    <?php if(!empty($c['about'])): ?>
    <div class="doc-about">
        <?php echo $c['about']; ?>
    </div>
    <?php endif; ?>

    <?php if(!empty($c['timings'])): ?>
    <div class="time-card">
        <span class="time-label"><i class="far fa-clock me-1"></i> Clinic Timings</span>
        <?php echo $c['timings']; ?>
    </div>
    <?php endif; ?>

    <div class="svc-list mt-4">
        <h6 class="fw-bold mb-3 ps-2 border-start border-4 border-info">  Treatments & Services</h6>
        <?php if(!empty($c['services'])): foreach($c['services'] as $svc): ?>
        <div class="svc-item">
            <div class="svc-icon"><i class="fas fa-stethoscope"></i></div>
            <div class="svc-text"><?php echo $svc; ?></div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <div class="text-center mt-5 mb-5 pb-5">
        <small class="text-muted">Digital Profile by <a href="https://arhamprinters.pk">Arham Printers</a></small>
    </div>

    <div class="book-bar">
        <a href="https://wa.me/<?php echo $c['whatsapp']; ?>?text=I+need+an+appointment" class="book-btn">
            <i class="far fa-calendar-check me-2"></i> Book Appointment
        </a>
    </div>
</div>

</body>
</html>

<?php 
// =========================================================
//  LAYOUT 3: BUSINESS CARD (DEFAULT)
// =========================================================
} else { 
    $theme = $c['color'] ?? '#182C61';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $c['company']; ?></title>
    <meta property="og:title" content="<?php echo $c['company']; ?>">
    <meta property="og:image" content="<?php echo getFullUrl($c['logo']); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary: <?php echo $theme; ?>; }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; margin: 0; display: flex; justify-content: center; min-height: 100vh; }
        .mobile-container { width: 100%; max-width: 420px; background: white; min-height: 100vh; display: flex; flex-direction: column; box-shadow: 0 0 30px rgba(0,0,0,0.1); position: relative; }
        .top-section { background: white; text-align: center; padding: 30px 20px 60px 20px; position: relative; z-index: 1; }
        .brand-logo-small { position: absolute; top: 20px; right: 20px; width: 50px; height: 50px; object-fit: contain; }
        .profile-img-container { width: 140px; height: 140px; margin: 20px auto; border-radius: 50%; padding: 5px; background: white; border: 1px solid #eee; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .profile-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .user-name { font-size: 1.8rem; font-weight: 800; color: #333; margin-bottom: 0; line-height: 1.2; }
        .user-title { font-size: 1rem; color: #777; margin-bottom: 10px; font-weight: 500; }
        .user-contact { font-size: 1.1rem; color: #333; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .curve-separator { position: relative; margin-top: -50px; z-index: 2; }
        .curve-svg { display: block; width: 100%; height: auto; color: var(--primary); }
        .icon-dock { position: absolute; top: 20%; left: 0; right: 0; display: flex; justify-content: center; gap: 15px; z-index: 10; }
        .dock-btn { width: 50px; height: 50px; border-radius: 50%; background: white; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; box-shadow: 0 5px 15px rgba(0,0,0,0.2); text-decoration: none; transition: transform 0.2s; border: 2px solid white; }
        .dock-btn:hover { transform: translateY(-5px); color: var(--primary); }
        .icon-phone { color: #2ecc71; } .icon-map { color: #3498db; } .icon-wa { color: #25D366; } .icon-mail { color: #e74c3c; } .icon-web { color: #34495e; }
        .bottom-section { background-color: var(--primary); color: white; flex-grow: 1; padding: 60px 25px 30px 25px; text-align: center; }
        .company-name { font-size: 1.4rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .company-address { font-size: 0.9rem; opacity: 0.9; margin-bottom: 30px; line-height: 1.5; }
        .share-input-box { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; padding: 8px 8px 8px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; cursor: pointer; }
        .share-text { color: white; font-size: 0.9rem; }
        .share-btn-icon { background: white; color: var(--primary); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .bottom-actions { display: flex; justify-content: center; gap: 15px; margin-bottom: 30px; }
        .action-pill { background: white; color: var(--primary); padding: 10px 20px; border-radius: 50px; font-size: 0.85rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .action-pill:hover { background: #f8f9fa; transform: scale(1.05); }
        .services-area { margin-bottom: 30px; }
        .svc-tag { display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; margin: 3px; }
        .social-footer { display: flex; justify-content: center; gap: 15px; margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        .social-icon { color: white; font-size: 1.2rem; opacity: 0.7; transition: 0.2s; }
        .social-icon:hover { opacity: 1; }
        .powered-by { font-size: 0.7rem; color: rgba(255,255,255,0.5); margin-top: 20px; }
        .powered-by a { color: rgba(255,255,255,0.8); text-decoration: none; }
    </style>
</head>
<body>

<div class="mobile-container">
    <div class="top-section">
        <?php if(!empty($c['logo'])): ?>
            <img src="<?php echo $c['logo']; ?>" class="brand-logo-small">
        <?php endif; ?>
        <div class="profile-img-container">
            <?php $pic = !empty($c['profile_pic']) ? $c['profile_pic'] : $c['logo']; ?>
            <img src="<?php echo $pic; ?>" class="profile-img" onerror="this.src='../img/logo2.webp'">
        </div>
        <h1 class="user-name"><?php echo $c['name']; ?></h1>
        <div class="user-title"><?php echo $c['designation']; ?></div>
        <div class="user-contact">
            <i class="fas fa-mobile-alt text-success"></i> <?php echo $c['phone']; ?>
        </div>
        <?php if(!empty($c['email'])): ?>
        <div class="user-contact" style="font-size: 0.9rem; margin-top: 5px;">
            <i class="fas fa-envelope text-danger"></i> <?php echo $c['email']; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="curve-separator">
        <svg class="curve-svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="currentColor" fill-opacity="1" d="M0,224L80,213.3C160,203,320,181,480,181.3C640,181,800,203,960,213.3C1120,224,1280,224,1360,224L1440,224L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
        </svg>
        <div class="icon-dock">
            <a href="tel:<?php echo $c['phone']; ?>" class="dock-btn icon-phone"><i class="fas fa-phone"></i></a>
            <?php if(!empty($c['map_link'])): ?>
            <a href="<?php echo $c['map_link']; ?>" target="_blank" class="dock-btn icon-map"><i class="fas fa-map-marker-alt"></i></a>
            <?php endif; ?>
            <a href="https://wa.me/<?php echo $c['whatsapp']; ?>" class="dock-btn icon-wa"><i class="fab fa-whatsapp"></i></a>
            <?php if(!empty($c['email'])): ?>
            <a href="mailto:<?php echo $c['email']; ?>" class="dock-btn icon-mail"><i class="fas fa-envelope"></i></a>
            <?php endif; ?>
            <?php if(!empty($c['website'])): ?>
            <a href="<?php echo $c['website']; ?>" target="_blank" class="dock-btn icon-web"><i class="fas fa-globe"></i></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="bottom-section">
        <div class="company-name"><?php echo $c['company']; ?></div>
        <div class="company-address"><?php echo $c['address']; ?></div>
        <div class="share-input-box" onclick="shareCard()">
            <span class="share-text"><?php echo $c['phone']; ?></span>
            <div class="share-btn-icon"><i class="fas fa-share-alt"></i></div>
        </div>
        <div class="bottom-actions">
            <a href="#" onclick="downloadVCard()" class="action-pill">
                <i class="fas fa-save me-1"></i> Save
            </a>
            <a href="#" onclick="shareCard()" class="action-pill">
                <i class="fas fa-share me-1"></i> Share
            </a>
            <a href="https://arhamprinters.pk" class="action-pill">
                <i class="fas fa-magic me-1"></i> Create
            </a>
        </div>
        <?php if(!empty($c['services'])): ?>
        <div class="services-area">
            <?php foreach($c['services'] as $svc): ?>
                <span class="svc-tag"><?php echo $svc; ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="social-footer">
            <?php if(isset($c['social'])): ?>
                <?php foreach($c['social'] as $platform => $link): ?>
                    <a href="<?php echo $link; ?>" class="social-icon"><i class="fab fa-<?php echo $platform; ?>"></i></a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="powered-by">
            Powered by <a href="https://arhamprinters.pk">Arham Printers 0300-6238233</a>
        </div>
    </div>
</div>

<script>
    function downloadVCard() {
        const vcard = `BEGIN:VCARD
VERSION:3.0
FN:<?php echo $c['name']; ?>

ORG:<?php echo $c['company']; ?>

TITLE:<?php echo $c['designation']; ?>

TEL;TYPE=CELL:<?php echo $c['phone']; ?>

TEL;TYPE=WORK,VOICE:<?php echo $c['whatsapp']; ?>

EMAIL:<?php echo $c['email']; ?>

ADR;TYPE=WORK:;;<?php echo $c['address']; ?>;;;
END:VCARD`;

        const blob = new Blob([vcard], { type: 'text/vcard' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = "<?php echo str_replace(' ', '_', $c['name']); ?>.vcf";
        link.href = url;
        link.click();
    }

    function shareCard() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo $c['company']; ?>',
                url: window.location.href
            });
        } else {
            alert("URL copied to clipboard!");
            navigator.clipboard.writeText(window.location.href);
        }
    }
</script>

</body>
</html>
<?php } ?>