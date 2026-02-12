<?php
// Load Configuration
$config_file = 'data/config.json';
if (!file_exists($config_file)) { die("Configuration file missing."); }
$data = json_decode(file_get_contents($config_file), true);
$theme = $data['theme'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $data['identity']['description']; ?>">
    <title><?php echo $data['identity']['name']; ?> | Luxury Weddings in Jalalpur Jattan</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- 1. CSS VARIABLES --- */
        :root {
            --primary: <?php echo $theme['primary']; ?>;
            --gold: <?php echo $theme['secondary']; ?>;
            --light: <?php echo $theme['accent']; ?>;
            --text: <?php echo $theme['text_dark']; ?>;
            --white: <?php echo $theme['text_light']; ?>;
            --shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        /* --- 2. GLOBAL RESET --- */
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--light); color: var(--text); overflow-x: hidden; }
        h1, h2, h3, h4, .cinzel { font-family: 'Cinzel', serif; letter-spacing: 1px; }
        a { text-decoration: none; transition: 0.3s; }
        ul { list-style: none; padding: 0; margin: 0; }

        /* --- 3. TOP BAR (CONTACT) --- */
        .top-bar {
            background: var(--primary); color: rgba(255,255,255,0.7); padding: 12px 5%;
            display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .top-links a { color: inherit; margin-right: 20px; display: inline-flex; align-items: center; gap: 8px; }
        .top-links a:hover { color: var(--gold); }
        .top-socials i { margin-left: 15px; cursor: pointer; transition: 0.3s; }
        .top-socials i:hover { color: var(--gold); }

        /* --- 4. MAIN NAVBAR --- */
        .navbar {
            background: rgba(255, 255, 255, 0.98); padding: 15px 5%; display: flex; 
            justify-content: space-between; align-items: center; position: sticky; top: 0; 
            z-index: 1000; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .logo { font-size: 1.6rem; color: var(--primary); font-weight: 700; text-transform: uppercase; display: flex; align-items: center; gap: 10px; }
        .logo span { color: var(--gold); font-size: 2.2rem; line-height: 1; }
        
        .nav-menu { display: flex; gap: 30px; align-items: center; }
        .nav-menu a { color: var(--primary); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; position: relative; }
        .nav-menu a::after { content: ''; display: block; width: 0; height: 2px; background: var(--gold); transition: width .3s; }
        .nav-menu a:hover { color: var(--gold); }
        .nav-menu a:hover::after { width: 100%; }
        
        .btn-book {
            background: var(--primary); color: var(--white); padding: 12px 30px; 
            border-radius: 2px; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px;
            border: 1px solid var(--primary); font-weight: 700;
        }
        .btn-book:hover { background: transparent; color: var(--primary); }
        
        .mobile-menu-btn { display: none; font-size: 1.5rem; color: var(--primary); cursor: pointer; }

        /* --- 5. HERO SECTION (PARALLAX) --- */
        .hero {
            height: 85vh; min-height: 600px;
            background: url('<?php echo $data['media']['hero_bg']; ?>') center/cover fixed no-repeat;
            position: relative; display: flex; align-items: center; justify-content: center; text-align: center;
        }
        .hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.8)); }
        .hero-content { position: relative; z-index: 2; color: white; max-width: 900px; padding: 20px; }
        
        .hero-tag { display: inline-block; border-bottom: 2px solid var(--gold); padding-bottom: 5px; margin-bottom: 20px; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); }
        .hero h1 { font-size: 4.5rem; margin: 0 0 20px; line-height: 1.1; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9; font-weight: 300; max-width: 600px; margin-left: auto; margin-right: auto; }
        
        .btn-hero {
            background: var(--gold); color: var(--primary); padding: 18px 45px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px; display: inline-block; transition: 0.3s;
        }
        .btn-hero:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4); }

        /* --- 6. LIVE STATS COUNTER --- */
        .stats-section {
            background: var(--primary); color: white; padding: 80px 5%;
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px; text-align: center; position: relative; margin-top: -50px; z-index: 5; max-width: 1200px; margin-left: auto; margin-right: auto; box-shadow: var(--shadow); border-radius: 4px;
        }
        .stat-item h3 { font-size: 3rem; color: var(--gold); margin: 0; font-weight: 700; }
        .stat-item p { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-top: 10px; }

        /* --- 7. GENERAL SECTIONS --- */
        .section { padding: 100px 5%; position: relative; }
        .section-header { text-align: center; margin-bottom: 60px; max-width: 700px; margin-left: auto; margin-right: auto; }
        .section-header h2 { font-size: 3rem; color: var(--primary); margin: 0 0 15px; }
        .divider { width: 80px; height: 3px; background: var(--gold); margin: 0 auto 20px; }
        .section-header p { color: #666; line-height: 1.6; }

        /* --- 8. PACKAGES GRID --- */
        .pkg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; }
        .pkg-card {
            background: white; border: 1px solid #eee; padding: 50px 30px; text-align: center;
            transition: 0.4s; position: relative; overflow: hidden; border-radius: 8px;
        }
        .pkg-card:hover { transform: translateY(-15px); border-color: var(--gold); box-shadow: var(--shadow); }
        .pkg-card h3 { font-size: 1.5rem; margin-bottom: 10px; color: var(--primary); }
        .pkg-price { font-size: 2.2rem; color: var(--gold); font-family: 'Cinzel'; margin: 20px 0; font-weight: 700; }
        .pkg-desc { color: #777; font-size: 0.9rem; margin-bottom: 30px; font-style: italic; }
        
        .pkg-list { list-style: none; padding: 0; color: #555; margin-bottom: 40px; text-align: left; }
        .pkg-list li { padding: 10px 0; border-bottom: 1px dashed #eee; display: flex; align-items: center; gap: 10px; }
        .pkg-list li i { color: var(--gold); }

        /* --- 9. GALLERY (MASONRY) --- */
        .gallery-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;
        }
        .gallery-item {
            height: 350px; overflow: hidden; position: relative; border-radius: 4px; cursor: pointer;
        }
        .gallery-item img {
            width: 100%; height: 100%; object-fit: cover; transition: 0.5s;
        }
        .gallery-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.4); opacity: 0; 
            transition: 0.3s; display: flex; align-items: center; justify-content: center;
        }
        .gallery-item:hover img { transform: scale(1.1); }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay i { color: white; font-size: 2rem; }

        /* --- 10. FAQ ACCORDION --- */
        .faq-wrapper { max-width: 800px; margin: 0 auto; }
        .faq-item { background: white; margin-bottom: 15px; border-radius: 8px; overflow: hidden; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .faq-question { padding: 20px 25px; cursor: pointer; font-weight: 600; display: flex; justify-content: space-between; align-items: center; color: var(--primary); transition: 0.3s; }
        .faq-question:hover { background: #f9f9f9; }
        .faq-answer { padding: 0 25px 25px; display: none; color: #666; line-height: 1.6; font-size: 0.95rem; border-top: 1px solid #f0f0f0; padding-top: 15px; }
        
        .faq-item.active .faq-answer { display: block; }
        .faq-item.active .faq-question { color: var(--gold); }
        .faq-item.active .faq-question i { transform: rotate(45deg); }

        /* --- 11. FOOTER (4 COLS) --- */
        footer { background: var(--primary); color: #94a3b8; padding: 80px 5% 30px; border-top: 8px solid var(--gold); margin-top: 80px; }
        .footer-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 50px; margin-bottom: 60px; }
        
        .footer-col h4 { color: var(--white); font-size: 1.2rem; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .footer-desc { line-height: 1.8; margin-bottom: 25px; font-size: 0.95rem; }
        
        .footer-links li { margin-bottom: 15px; }
        .footer-links a { color: #94a3b8; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; }
        .footer-links a:hover { color: var(--gold); padding-left: 5px; }
        
        /* BRANDING BOX */
        .arham-brand { background: rgba(255,255,255,0.03); padding: 30px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; }
        .arham-logo { font-family: 'Cinzel'; font-weight: 700; color: var(--gold); font-size: 1.3rem; margin-bottom: 15px; display: block; }
        .arham-text { font-size: 0.85rem; margin-bottom: 20px; color: #cbd5e1; }
        .arham-btn { color: var(--white); text-decoration: underline; font-size: 0.9rem; font-weight: 600; }

        /* GOOGLE MAP */
        .map-frame { width: 100%; height: 220px; border: 0; filter: grayscale(100%); transition: 0.5s; border-radius: 8px; }
        .map-frame:hover { filter: grayscale(0%); }

        .copyright { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; text-align: center; font-size: 0.85rem; opacity: 0.6; }

        /* --- 12. FLOATING WHATSAPP --- */
        .wa-float {
            position: fixed; bottom: 30px; right: 30px; background: #25D366; color: white;
            width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 30px; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4); z-index: 999; 
            transition: 0.3s; animation: pulse 2s infinite;
        }
        .wa-float:hover { transform: scale(1.1); }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); } 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); } }

        /* --- 13. RESPONSIVE --- */
        @media(max-width: 991px) {
            .hero h1 { font-size: 3rem; }
            .top-bar { display: none; }
            .navbar { padding: 15px; }
            .mobile-menu-btn { display: block; }
            .nav-menu {
                position: absolute; top: 100%; left: 0; width: 100%; background: white;
                flex-direction: column; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                display: none;
            }
            .nav-menu.active { display: flex; }
            .stats-section { grid-template-columns: 1fr 1fr; padding: 40px; margin-top: 0; }
        }
        @media(max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .pkg-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="top-links">
            <a href="tel:<?php echo $data['contact']['phone']; ?>"><i class="fas fa-phone-alt"></i> <?php echo $data['contact']['phone']; ?></a>
            <a href="mailto:<?php echo $data['contact']['email']; ?>"><i class="fas fa-envelope"></i> <?php echo $data['contact']['email']; ?></a>
        </div>
        <div class="top-socials">
            <i class="fab fa-facebook-f"></i>
            <i class="fab fa-instagram"></i>
            <i class="fab fa-tiktok"></i>
        </div>
    </div>

    <nav class="navbar">
        <a href="index.php" class="logo">
            <span><?php echo $data['identity']['logo_text']; ?></span> <?php echo $data['identity']['name']; ?>
        </a>
        <div class="mobile-menu-btn" onclick="document.querySelector('.nav-menu').classList.toggle('active')">
            <i class="fas fa-bars"></i>
        </div>
        <div class="nav-menu">
            <a href="#">Home</a>
            <a href="#about">About</a>
            <a href="#packages">Menus</a>
            <a href="#gallery">Gallery</a>
            <a href="#faq">FAQ</a>
            <a href="planner.php" class="btn-book">Plan Your Event</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content" data-aos="zoom-in" data-aos-duration="1200">
            <span class="hero-tag">Since 2012</span>
            <h1><?php echo $data['identity']['name']; ?></h1>
            <p><?php echo $data['identity']['description']; ?></p>
            <a href="planner.php" class="btn-hero">Check Availability</a>
        </div>
    </header>

    <div class="stats-section">
        <?php foreach($data['stats'] as $stat): ?>
        <div class="stat-item" data-aos="fade-up">
            <h3><?php echo $stat['value']; ?></h3>
            <p><?php echo $stat['label']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <section class="section" id="packages">
        <div class="section-header" data-aos="fade-up">
            <h2>Exquisite Menus</h2>
            <div class="divider"></div>
            <p>Choose from our curated selection of premium dining experiences.</p>
        </div>
        <div class="pkg-grid">
            <?php foreach($data['packages'] as $pkg): ?>
            <div class="pkg-card" data-aos="fade-up">
                <h3><?php echo $pkg['name']; ?></h3>
                <div class="pkg-price"><?php echo $data['identity']['currency'] . number_format($pkg['price']); ?></div>
                <div class="pkg-desc"><?php echo $pkg['desc']; ?></div>
                <ul class="pkg-list">
                    <?php foreach($pkg['items'] as $item): ?>
                    <li><i class="fas fa-circle-check"></i> <?php echo $item; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="planner.php" class="btn-book" style="width:100%; display:inline-block; text-align:center;">Select Package</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section" id="gallery" style="background:white;">
        <div class="section-header" data-aos="fade-up">
            <h2>Venue Gallery</h2>
            <div class="divider"></div>
        </div>
        <div class="gallery-grid">
            <?php foreach($data['gallery'] as $img): ?>
            <div class="gallery-item" data-aos="fade-in">
                <img src="<?php echo $img; ?>" alt="Venue Image">
                <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section" id="faq" style="background:var(--light);">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <div class="divider"></div>
        </div>
        <div class="faq-wrapper">
            <?php foreach($data['faqs'] as $faq): ?>
            <div class="faq-item" onclick="this.classList.toggle('active')">
                <div class="faq-question">
                    <?php echo $faq['q']; ?>
                    <i class="fas fa-plus"></i>
                </div>
                <div class="faq-answer"><?php echo $faq['a']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4 class="cinzel"><?php echo $data['identity']['name']; ?></h4>
                <p class="footer-desc"><?php echo $data['identity']['description']; ?></p>
                <div style="font-size:1.5rem; color:var(--gold);">
                    <i class="fab fa-facebook margin-right:15px;"></i>
                    <i class="fab fa-instagram margin-right:15px;"></i>
                    <i class="fab fa-youtube"></i>
                </div>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-angle-right"></i> Home</a></li>
                    <li><a href="planner.php"><i class="fas fa-angle-right"></i> Availability</a></li>
                    <li><a href="#packages"><i class="fas fa-angle-right"></i> Menu Packages</a></li>
                    <li><a href="#gallery"><i class="fas fa-angle-right"></i> Photo Gallery</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Location</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> <?php echo $data['contact']['address']; ?></a></li>
                    <li><a href="tel:<?php echo $data['contact']['phone']; ?>"><i class="fas fa-phone"></i> <?php echo $data['contact']['phone']; ?></a></li>
                </ul>
                <div style="margin-top:20px;">
                    <iframe src="<?php echo $data['contact']['map_embed']; ?>" class="map-frame" loading="lazy"></iframe>
                </div>
            </div>

            <div class="footer-col arham-brand">
                <a href="https://arhamprinters.pk" class="arham-logo"><i class="fas fa-cube"></i> ARHAM PRINTERS</a>
                <p class="footer-desc" style="font-size:0.85rem; color:#cbd5e1;">
                    Designed and engineered by Arham Printers, the leading digital agency in Jalalpur Jattan.
                </p>
                <a href="https://arhamprinters.pk" class="arham-btn">Get Your Website →</a>
            </div>
        </div>

        <div class="copyright">
            © <?php echo date('Y'); ?> <?php echo $data['identity']['name']; ?>. All Rights Reserved.
        </div>
    </footer>

    <a href="https://wa.me/<?php echo $data['contact']['whatsapp']; ?>" class="wa-float" target="_blank"><i class="fab fa-whatsapp"></i></a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
    </script>
</body>
</html>