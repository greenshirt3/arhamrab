<?php
// 1. Get Subdomain
$host = $_SERVER['HTTP_HOST'];
$parts = explode('.', $host);
$subdomain = $parts[0];

// 2. Load Customer Data
$json_file = '../customers.json'; 
if (!file_exists($json_file)) { die("Error: customers.json missing."); }
$customers = json_decode(file_get_contents($json_file), true);

// 3. Check if User Exists
if (isset($customers[$subdomain])) {
    $c = $customers[$subdomain];
} else {
    // Fallback Demo
    $c = ['company' => 'Chand Burger', 'color' => '#e67e22', 'phone' => '923000000000', 'whatsapp' => '923000000000', 'logo' => '../../img/logo2.webp', 'menu' => []];
}

// 4. Asset Fixer
function getFullUrl($path) {
    if (strpos($path, 'http') === 0) return $path;
    $cleanPath = str_replace('../', '', $path); 
    return "https://arhamprinters.pk/" . $cleanPath;
}

$theme = $c['color'] ?? '#e67e22';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $c['company']; ?> | Order Now</title>
    
    <link href="https://arhamprinters.pk/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Poppins:wght@300;600&display=swap" rel="stylesheet">
     <script type="text/javascript">
<?php include __DIR__ . '/../../seo.php'; ?>

    
    <style>
        :root { --primary: <?php echo $theme; ?>; --dark: #1a1a1a; --darker: #0d0d0d; }
        
        body {
            background-color: var(--darker);
            color: white;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding-bottom: 80px; 
        }
        
        .food-container {
            max-width: 480px;
            margin: 0 auto;
            background: var(--dark);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* HERO HEADER */
        .hero-header {
            height: 260px;
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: 25px;
            border-bottom-right-radius: 25px;
        }
        .hero-img {
            width: 100%; height: 100%; object-fit: cover;
            filter: brightness(0.6);
            transition: transform 3s;
        }
        .hero-header:hover .hero-img { transform: scale(1.1); }
        
        .shop-info-overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 80px 20px 20px;
            background: linear-gradient(to top, rgba(0,0,0,0.95), transparent);
            text-align: center;
        }
        .shop-logo {
            width: 90px; height: 90px; border-radius: 50%;
            border: 3px solid var(--primary);
            position: absolute; top: -50px; left: 50%; transform: translateX(-50%);
            background: white; object-fit: cover;
            box-shadow: 0 5px 20px rgba(0,0,0,0.6);
        }
        .shop-name {
            font-family: 'Oswald', sans-serif;
            font-size: 2.2rem; font-weight: 700;
            margin-top: 35px; margin-bottom: 5px;
            text-transform: uppercase; letter-spacing: 1px;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.8);
            line-height: 1.1;
            color: #ffffff;
        }
        .status-badge {
            background: #27ae60; color: white;
            font-weight: bold; font-size: 0.75rem;
            padding: 4px 12px; border-radius: 20px;
            text-transform: uppercase; letter-spacing: 1px;
        }

        /* PROMO BAR */
        .promo-bar {
            background: var(--primary);
            color: black;
            padding: 10px 0;
            font-size: 0.9rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-transform: uppercase;
        }
        .marquee-content { display: inline-block; animation: scroll 12s linear infinite; padding-left: 100%; }
        @keyframes scroll { 0% { transform: translate(0, 0); } 100% { transform: translate(-100%, 0); } }

        /* ACTION GRID */
        .action-grid {
            display: flex; gap: 10px; padding: 20px 15px;
            justify-content: center;
        }
        .action-btn {
            flex: 1;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 15px 10px;
            border-radius: 12px;
            text-align: center;
            color: white; text-decoration: none;
            font-size: 0.8rem;
            transition: 0.2s;
        }
        .action-btn i { display: block; font-size: 1.4rem; margin-bottom: 8px; color: var(--primary); }
        .action-btn:active { transform: scale(0.95); background: rgba(255,255,255,0.15); }

        /* SERVICES */
        .services-tags { padding: 0 20px 10px; text-align: center; }
        .tag { background: #333; padding: 5px 15px; border-radius: 30px; font-size: 0.75rem; color: #ccc; margin: 3px; display: inline-block; border: 1px solid #444; }

        /* MENU SECTION */
        .menu-section { padding: 10px 15px; flex-grow: 1; }
        .section-title {
            font-family: 'Oswald', sans-serif;
            font-size: 1.6rem; color: white;
            margin-bottom: 20px;
            display: flex; align-items: center;
        }
        .section-title::before {
            content: ''; width: 5px; height: 25px; background: var(--primary);
            margin-right: 10px; display: inline-block; border-radius: 2px;
        }

        .menu-item {
            display: flex; align-items: center;
            background: #252525;
            margin-bottom: 15px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: transform 0.2s;
            border: 1px solid #333;
        }
        .menu-item:active { transform: scale(0.98); }
        
        .item-img {
            width: 110px; height: 110px;
            object-fit: cover;
        }
        .item-details {
            flex: 1; padding: 15px;
        }
        .item-name {
            font-weight: 600; font-size: 1rem; margin-bottom: 5px; display: block; color: #fff;
        }
        .item-price {
            color: var(--primary); font-weight: 700; font-size: 1.2rem;
        }
        .btn-add {
            background: white; color: black;
            border: none; padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.8rem; font-weight: bold;
            float: right; margin-top: 5px;
            text-decoration: none;
        }
        
        /* BRANDING FOOTER */
        .branding-footer {
            text-align: center;
            padding: 30px 20px 20px;
            margin-top: auto;
            border-top: 1px solid #333;
            background: #151515;
        }
        .branding-text {
            font-size: 0.75rem; color: #666; letter-spacing: 0.5px;
        }
        .branding-text a {
            color: var(--primary); text-decoration: none; font-weight: bold;
        }

        /* STICKY NAV */
        .sticky-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #000;
            border-top: 1px solid #333;
            display: flex; justify-content: space-around;
            padding: 12px 0; z-index: 100;
            max-width: 480px; margin: 0 auto;
        }
        .nav-item {
            text-align: center; color: #666;
            text-decoration: none; font-size: 0.7rem;
            width: 60px;
        }
        .nav-item i { display: block; font-size: 1.3rem; margin-bottom: 4px; }
        .nav-item.active { color: white; }
        
        .main-cta {
            position: relative; top: -35px;
            width: 60px; height: 60px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: black; font-size: 1.5rem;
            border: 5px solid #000;
            box-shadow: 0 0 20px rgba(255,255,255,0.1);
        }

    </style>
</head>
<body>

<div class="food-container">
    
    <div class="hero-header">
        <img src="<?php echo $c['cover']; ?>" class="hero-img" onerror="this.src='https://images.unsplash.com/photo-1550547660-d9450f859349?q=80&w=1000'">
        <div class="shop-info-overlay">
            <img src="<?php echo getFullUrl($c['logo']); ?>" class="shop-logo" onerror="this.src='../../img/chandlogo.webp'">
            <h1 class="shop-name"><?php echo $c['company']; ?></h1>
            <span class="status-badge">● Open For Orders</span>
        </div>
    </div>

    <?php if(!empty($c['promos'])): ?>
    <div class="promo-bar">
        <div class="marquee-content">
            <?php foreach($c['promos'] as $promo) { echo "<i class='fas fa-hamburger'></i> " . $promo . " &nbsp;&nbsp;&nbsp;&nbsp; "; } ?>
            <?php foreach($c['promos'] as $promo) { echo "<i class='fas fa-mug-hot'></i> " . $promo . " &nbsp;&nbsp;&nbsp;&nbsp; "; } ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="action-grid">
        <a href="tel:<?php echo $c['phone']; ?>" class="action-btn">
            <i class="fas fa-phone-alt"></i> Call
        </a>
        <a href="https://wa.me/<?php echo $c['whatsapp']; ?>" class="action-btn">
            <i class="fab fa-whatsapp"></i> Chat
        </a>
        <a href="<?php echo $c['map_link']; ?>" target="_blank" class="action-btn">
            <i class="fas fa-map-marker-alt"></i> Map
        </a>
        <a href="#" onclick="share()" class="action-btn">
            <i class="fas fa-share-alt"></i> Share
        </a>
    </div>

    <div class="services-tags">
        <?php foreach($c['services'] as $svc) { echo "<span class='tag'>$svc</span>"; } ?>
    </div>

    <div class="menu-section">
        <div class="section-title">OUR MENU</div>
        
        <?php if(!empty($c['menu'])): ?>
            <?php foreach($c['menu'] as $item): ?>
            <div class="menu-item">
                <img src="<?php echo $item['img']; ?>" class="item-img" onerror="this.src='https://arhamprinters.pk/img/products/placeholder.webp'">
                <div class="item-details">
                    <span class="item-name"><?php echo $item['name']; ?></span>
                    <span class="item-price">Rs. <?php echo $item['price']; ?></span>
                    <a href="https://wa.me/<?php echo $c['whatsapp']; ?>?text=I want to order: <?php echo $item['name']; ?> - Rs.<?php echo $item['price']; ?>" class="btn-add">
                        ORDER
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">Menu uploading soon...</p>
        <?php endif; ?>
    </div>

    <div class="branding-footer">
        <div class="branding-text">
            Powered by <a href="https://arhamprinters.pk" target="_blank">ARHAM PRINTERS 0300-6238233</a>
        </div>
    </div>
    
    <div style="height: 20px;"></div>

</div>

<div class="sticky-nav">
    <a href="#" class="nav-item active"><i class="fas fa-home"></i> Home</a>
    <a href="https://wa.me/<?php echo $c['whatsapp']; ?>" class="main-cta"><i class="fas fa-utensils"></i></a>
    <a href="<?php echo $c['map_link']; ?>" class="nav-item"><i class="fas fa-location-arrow"></i> Locate</a>
</div>

<script>
    function share() {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo $c['company']; ?>',
                text: 'Order the best food in town!',
                url: window.location.href
            });
        } else {
            alert("Link copied!");
        }
    }
</script>

</body>
</html>