<?php
// 1. Load Data
$shops_file = 'data/shops.json';
$shops = file_exists($shops_file) ? json_decode(file_get_contents($shops_file), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiderGo | The Logistics of Jalalpur Jattan</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand: #F37021;
            --brand-dark: #d65a0f;
            --dark: #0f172a;
            --light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.8);
            --shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
        }
        
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; background: var(--light); color: var(--dark); overflow-x: hidden; }
        
        /* --- 1. NAVBAR (Glass Effect) --- */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 5%; position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05); transition: 0.3s;
        }
        .logo { font-size: 1.8rem; font-weight: 800; color: var(--dark); text-decoration: none; display: flex; align-items: center; gap: 8px; letter-spacing: -1px; }
        .logo span { color: var(--brand); }
        
        .nav-right { display: flex; gap: 15px; align-items: center; }
        .btn-track { font-weight: 600; color: var(--dark); text-decoration: none; font-size: 0.9rem; }
        
        /* MODERN DROPDOWN */
        .dropdown { position: relative; }
        .dropbtn {
            background: var(--dark); color: white; padding: 10px 24px; border-radius: 50px;
            font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px;
            transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .dropbtn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.3); }
        
        .dropdown-menu {
            display: none; position: absolute; right: 0; top: 120%; background: white;
            min-width: 240px; border-radius: 16px; padding: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid rgba(0,0,0,0.05);
            animation: slideUp 0.2s ease-out;
        }
        .dropdown-menu a {
            display: flex; align-items: center; gap: 12px; padding: 12px 15px;
            color: var(--dark); text-decoration: none; border-radius: 8px; font-weight: 500;
        }
        .dropdown-menu a:hover { background: #f1f5f9; color: var(--brand); }
        .dropdown:hover .dropdown-menu { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* --- 2. 3D HERO SECTION --- */
        .hero {
            padding: 140px 5% 80px; display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(135deg, #fff 0%, #fff7ed 100%); position: relative; overflow: hidden;
        }
        /* Abstract Background Shapes */
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6; z-index: 0; }
        .blob-1 { width: 400px; height: 400px; background: #ffdec9; top: -100px; right: -100px; }
        .blob-2 { width: 300px; height: 300px; background: #e2e8f0; bottom: -50px; left: -50px; }

        .hero-text { max-width: 600px; z-index: 1; position: relative; }
        .hero h1 { font-size: 4rem; line-height: 1.1; font-weight: 800; letter-spacing: -2px; margin-bottom: 20px; }
        .hero p { font-size: 1.25rem; color: #64748b; margin-bottom: 40px; line-height: 1.6; }
        
        .hero-visual {
            position: relative; width: 500px; height: 500px; z-index: 1; display: flex; align-items: center; justify-content: center;
        }
        /* 3D Floating Elements Animation */
        .float-img { width: 100%; animation: float 6s ease-in-out infinite; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.15)); }
        @keyframes float { 0% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(1deg); } 100% { transform: translateY(0px) rotate(0deg); } }

        .btn-main {
            background: var(--brand); color: white; padding: 18px 40px; border-radius: 12px;
            font-size: 1.1rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 10px;
            transition: 0.3s; box-shadow: 0 10px 25px rgba(243, 112, 33, 0.4);
        }
        .btn-main:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 35px rgba(243, 112, 33, 0.5); }

        /* --- 3. CATEGORY PILLS --- */
        .categories { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px; }
        .cat-pill {
            background: white; padding: 10px 20px; border-radius: 50px; border: 1px solid #e2e8f0;
            font-weight: 600; color: var(--dark); display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        .cat-pill i { color: var(--brand); }

        /* --- 4. APP SHOWCASE (3D Phone) --- */
        .app-section { background: var(--dark); color: white; padding: 80px 5%; margin: 60px 0; border-radius: 0; position: relative; overflow: hidden; }
        .app-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; max-width: 1200px; margin: 0 auto; position: relative; z-index: 2; }
        
        .phone-mockup {
            width: 300px; height: 600px; background: #111; border-radius: 40px; border: 8px solid #333;
            margin: 0 auto; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            background-image: url('https://cdn.dribbble.com/users/1615584/screenshots/15710288/media/9d40533036814249dc651475d6540c4a.jpg?compress=1&resize=800x600');
            background-size: cover; background-position: center; transform: rotate(-5deg); transition: 0.5s;
        }
        .phone-mockup:hover { transform: rotate(0deg) scale(1.05); }

        /* --- 5. SHOP CARDS (3D Tilt) --- */
        .grid-container { max-width: 1200px; margin: 0 auto; padding: 80px 5%; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h2 { font-size: 2.5rem; font-weight: 800; margin-bottom: 15px; }
        
        .shops-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        
        .shop-card {
            background: white; border-radius: 20px; overflow: hidden; text-decoration: none; color: inherit;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(0,0,0,0.05); position: relative;
        }
        .shop-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        
        .card-img { height: 180px; background-size: cover; background-position: center; position: relative; }
        .card-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); }
        .card-body { padding: 25px; }
        
        .shop-badge { 
            position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.9); backdrop-filter: blur(5px);
            padding: 6px 12px; border-radius: 30px; font-weight: 700; font-size: 0.75rem; color: var(--dark);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* --- 6. FOOTER --- */
        footer { background: #fff; padding: 80px 5% 30px; border-top: 1px solid #eee; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .footer-col h4 { font-size: 1.1rem; margin-bottom: 20px; color: var(--dark); }
        .footer-col a { display: block; color: #64748b; text-decoration: none; margin-bottom: 12px; transition: 0.2s; }
        .footer-col a:hover { color: var(--brand); padding-left: 5px; }

        /* Responsive */
        @media(max-width: 768px) {
            .hero { flex-direction: column; text-align: center; padding-top: 100px; }
            .hero h1 { font-size: 2.8rem; }
            .hero-text { margin-bottom: 40px; }
            .hero-visual { width: 100%; height: auto; }
            .app-grid { grid-template-columns: 1fr; text-align: center; }
            .phone-mockup { margin: 40px auto 0; transform: rotate(0); }
            .categories { justify-content: center; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <i class="fas fa-cube" style="color:var(--brand); margin-right:10px;"></i> 
            Rider<span>Go</span>
        </a>
        
        <div class="nav-right">
            <a href="views/track.php" class="btn-track"><i class="fas fa-search"></i> Track Order</a>
            <div class="dropdown">
                <button class="dropbtn">
                    <i class="fas fa-user-circle"></i> Login <i class="fas fa-chevron-down" style="font-size:0.8rem"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="shop_dashboard.php"><i class="fas fa-store text-warning"></i> Vendor Dashboard</a>
                    <a href="rider.php"><i class="fas fa-motorcycle text-success"></i> Rider App</a>
                    <a href="admin.php" style="border-top:1px solid #eee"><i class="fas fa-lock text-danger"></i> Admin Control</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>

        <div class="hero-text">
            <div class="categories">
                <span class="cat-pill"><i class="fas fa-utensils"></i> Food</span>
                <span class="cat-pill"><i class="fas fa-shopping-bag"></i> Retail</span>
                <span class="cat-pill"><i class="fas fa-pills"></i> Pharmacy</span>
                <span class="cat-pill"><i class="fas fa-box"></i> Parcels</span>
            </div>
            <h1>Moving your world,<br>one delivery at a time.</h1>
            <p>RiderGo is Jalalpur Jattan's premium logistics partner. From hot meals to heavy hardware, we deliver anything, anywhere.</p>
            <a href="#shops" class="btn-main">Explore Stores <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="hero-visual">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/delivery-service-4345025-3605419.png" class="float-img" alt="3D Delivery">
        </div>
    </header>

    <section class="app-section">
        <div class="app-grid">
            <div style="padding-right: 20px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Track it all.<br>In Real-Time.</h2>
                <p style="font-size: 1.1rem; opacity: 0.8; line-height: 1.6; margin-bottom: 30px;">
                    Our advanced GPS logistics engine allows you to see exactly where your rider is. 
                    Whether it's a documents parcel or tonight's dinner, never lose sight of what matters.
                </p>
                <div style="display:flex; gap:15px; justify-content: center; flex-wrap: wrap;">
                    <div style="text-align:center;">
                        <h3 style="font-size:2.5rem; color:var(--brand); margin:0;">10+</h3>
                        <small>Active Riders</small>
                    </div>
                    <div style="width:1px; background:#333;"></div>
                    <div style="text-align:center;">
                        <h3 style="font-size:2.5rem; color:var(--brand); margin:0;">20m</h3>
                        <small>Avg. Time</small>
                    </div>
                </div>
            </div>
            <div>
                <div class="phone-mockup"></div>
            </div>
        </div>
    </section>

    <div class="grid-container" id="shops">
        <div class="section-header">
            <h4 style="color:var(--brand); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px;">Our Network</h4>
            <h2>Trusted Partners</h2>
            <p style="color:#64748b;">Browse top-rated businesses in J.J.</p>
        </div>

        <?php if(empty($shops)): ?>
            <div style="text-align:center; padding:80px; background:white; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.05);">
                <i class="fas fa-box-open fa-4x" style="color:#e2e8f0; margin-bottom:20px;"></i>
                <h3>No Partners Yet</h3>
                <p>We are currently onboarding merchants. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="shops-grid">
                <?php foreach($shops as $shop): ?>
                <a href="http://<?php echo $shop['subdomain']; ?>.arhamprinters.pk" class="shop-card">
                    <div class="card-img" style="background-image: url('<?php echo $shop['banner']; ?>');">
                        <div class="card-overlay"></div>
                        <span class="shop-badge"><?php echo ucfirst($shop['type'] ?? 'General'); ?></span>
                    </div>
                    <div class="card-body">
                        <h3 style="margin:0 0 5px 0; font-size:1.25rem;"><?php echo $shop['name']; ?></h3>
                        <p style="color:#64748b; font-size:0.9rem; margin-bottom:20px;"><?php echo $shop['tagline']; ?></p>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #e2e8f0; padding-top:15px; font-size:0.85rem; font-weight:600; color:#475569;">
                            <span><i class="fas fa-motorcycle" style="color:var(--brand)"></i> Rs. 50</span>
                            <span><i class="fas fa-star" style="color:#fbbf24"></i> 4.8/5</span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="background:var(--brand); color:white; text-align:center; padding:80px 20px; margin-top:80px;">
        <h2 style="font-size:2.5rem; margin-bottom:15px;">Run a Business?</h2>
        <p style="margin-bottom:30px; opacity:0.9; font-size:1.1rem;">Join RiderGo Logistics and reach thousands of customers instantly.</p>
        <a href="shop_dashboard.php" style="background:white; color:var(--brand); padding:15px 40px; border-radius:50px; font-weight:bold; text-decoration:none; display:inline-block; box-shadow:0 10px 20px rgba(0,0,0,0.1);">Become a Vendor</a>
    </div>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <div style="font-size:1.5rem; font-weight:800; display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                    <i class="fas fa-cube" style="color:var(--brand)"></i> RiderGo
                </div>
                <p style="color:#64748b; line-height:1.6;">The smartest logistics network in Jalalpur Jattan. Delivering excellence, one package at a time.</p>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <a href="#">About Us</a>
                <a href="#">Careers</a>
                <a href="#">Press</a>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <a href="views/track.php">Track Order</a>
                <a href="views/contact.php">Help Center</a>
                <a href="#">Terms & Privacy</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <a href="#"><i class="fas fa-phone"></i> +92 300 1234567</a>
                <a href="#"><i class="fas fa-envelope"></i> help@ridergo.pk</a>
            </div>
        </div>
        <div style="text-align:center; padding-top:40px; margin-top:40px; border-top:1px solid #f1f5f9; color:#94a3b8; font-size:0.85rem;">
            © <?php echo date('Y'); ?> RiderGo Logistics. Powered by <a href="https://arhamprinters.pk" style="color:var(--brand); text-decoration:none; font-weight:700;">Arham Printers</a>.
        </div>
    </footer>

</body>
</html>