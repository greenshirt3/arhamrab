<?php
$data = json_decode(file_get_contents('data/hall_data.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Grand Imperial | Royal Weddings</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Cormorant+Garamond:ital,wght@0,300;0,600;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #D4AF37;
            --gold-dim: #aa8c2c;
            --dark: #0a0a0a;
            --charcoal: #121212;
            --cream: #F5F5F5;
            --white: #ffffff;
        }

        * { box-sizing: border-box; }
        body { 
            font-family: 'Montserrat', sans-serif; 
            margin: 0; background: var(--dark); color: var(--cream); 
            overflow-x: hidden; scroll-behavior: smooth;
        }

        h1, h2, h3, h4 { font-family: 'Cinzel', serif; font-weight: 400; letter-spacing: 2px; }
        p { font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; line-height: 1.6; color: #ccc; }

        /* --- PRELOADER --- */
        #preloader {
            position: fixed; inset: 0; background: var(--dark); z-index: 9999;
            display: flex; justify-content: center; align-items: center; flex-direction: column;
            transition: opacity 1s ease;
        }
        .loader-logo { font-family: 'Cinzel'; font-size: 3rem; color: var(--gold); border: 2px solid var(--gold); padding: 10px 30px; animation: pulse 2s infinite; }

        /* --- NAVBAR --- */
        .navbar {
            position: fixed; top: 0; width: 100%; padding: 25px 5%; display: flex; justify-content: space-between; align-items: center;
            z-index: 100; mix-blend-mode: difference; transition: 0.4s;
        }
        .nav-logo { font-family: 'Cinzel'; font-size: 1.5rem; color: #fff; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; }
        .nav-links { display: flex; gap: 40px; }
        .nav-links a { color: #fff; text-decoration: none; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 2px; position: relative; }
        .nav-links a::after { content: ''; position: absolute; width: 0; height: 1px; bottom: -5px; left: 0; background: var(--gold); transition: 0.3s; }
        .nav-links a:hover::after { width: 100%; }

        .btn-reserve {
            border: 1px solid var(--gold); color: var(--gold); padding: 10px 30px; text-transform: uppercase; 
            font-size: 0.8rem; letter-spacing: 2px; text-decoration: none; transition: 0.4s;
        }
        .btn-reserve:hover { background: var(--gold); color: var(--dark); }

        /* --- HERO SECTION (CINEMATIC) --- */
        .hero {
            height: 100vh; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;
        }
        .hero-bg {
            position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1544124499-58912cbddad0?q=80&w=1920'); 
            background-size: cover; background-position: center; filter: brightness(0.4); transform: scale(1.1);
            animation: panZoom 20s infinite alternate;
        }
        .hero-content { position: relative; z-index: 2; text-align: center; padding: 20px; border: 1px solid rgba(212, 175, 55, 0.3); padding: 60px 40px; backdrop-filter: blur(3px); }
        .hero-subtitle { color: var(--gold); font-size: 1rem; letter-spacing: 5px; text-transform: uppercase; margin-bottom: 20px; display: block; }
        .hero h1 { font-size: 5vw; color: white; margin: 0; line-height: 1.1; margin-bottom: 30px; text-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        
        /* --- SECTIONS: THE GOLDEN GRID --- */
        .section-padding { padding: 120px 5%; position: relative; }
        
        /* OVERLAPPING LAYOUT */
        .overlap-grid { display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 50px; position: relative; }
        .overlap-text { padding: 40px; border-left: 2px solid var(--gold); }
        .overlap-img-wrapper { position: relative; height: 600px; }
        .overlap-img { width: 100%; height: 100%; object-fit: cover; filter: sepia(20%); }
        .floating-box {
            position: absolute; bottom: -50px; left: -50px; background: var(--dark); padding: 40px;
            border: 1px solid var(--gold); max-width: 300px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        /* --- AMENITIES (Horizontal Scroll Look) --- */
        .amenities-section { background: var(--charcoal); }
        .amenity-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1px; background: var(--gold-dim); }
        .amenity-card { 
            background: var(--charcoal); padding: 60px 30px; text-align: center; transition: 0.4s; 
            border: 1px solid transparent; cursor: pointer;
        }
        .amenity-card:hover { background: #1a1a1a; border-color: var(--gold); transform: translateY(-10px); z-index: 2; }
        .amenity-icon { font-size: 2.5rem; color: var(--gold); margin-bottom: 25px; }

        /* --- PACKAGES (Glass Cards) --- */
        .packages-section {
            background: url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1600') fixed center/cover;
            position: relative;
        }
        .glass-overlay { position: absolute; inset: 0; background: rgba(10, 10, 10, 0.85); }
        .pkg-container { position: relative; z-index: 2; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; }
        
        .pkg-card {
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px; text-align: center; transition: 0.5s; position: relative; overflow: hidden;
        }
        .pkg-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--gold);
            transform: scaleX(0); transition: 0.5s; transform-origin: left;
        }
        .pkg-card:hover { transform: translateY(-15px); background: rgba(255, 255, 255, 0.1); }
        .pkg-card:hover::before { transform: scaleX(1); }

        .pkg-name { font-family: 'Cinzel'; font-size: 1.5rem; color: white; margin-bottom: 10px; }
        .pkg-price { font-size: 2rem; color: var(--gold); font-weight: 700; font-family: 'Montserrat'; margin-bottom: 30px; }
        .pkg-list { list-style: none; padding: 0; margin-bottom: 30px; text-align: left; }
        .pkg-list li { margin-bottom: 15px; color: #ccc; font-size: 0.9rem; display: flex; gap: 10px; }
        .pkg-list li i { color: var(--gold); }

        /* --- FOOTER --- */
        footer { border-top: 1px solid #222; padding: 80px 5%; text-align: center; }
        .footer-logo { font-size: 3rem; color: var(--gold); font-family: 'Cinzel'; margin-bottom: 20px; }
        .socials i { font-size: 1.2rem; color: #555; margin: 0 10px; transition: 0.3s; }
        .socials i:hover { color: var(--gold); }

        /* Floating WA */
        .wa-float {
            position: fixed; bottom: 30px; right: 30px; background: transparent; border: 2px solid var(--gold);
            color: var(--gold); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 24px; z-index: 99; transition: 0.3s;
        }
        .wa-float:hover { background: var(--gold); color: var(--dark); }

        @keyframes pulse { 0% { opacity: 0.5; } 50% { opacity: 1; } 100% { opacity: 0.5; } }
        @keyframes panZoom { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }

        @media(max-width: 768px) {
            .navbar { background: var(--dark); }
            .nav-links { display: none; }
            .overlap-grid { grid-template-columns: 1fr; }
            .overlap-img-wrapper { height: 400px; }
            .floating-box { position: relative; bottom: 0; left: 0; max-width: 100%; margin-top: -50px; }
            .hero h1 { font-size: 3rem; }
        }
    </style>
</head>
<body>

    <div id="preloader">
        <div class="loader-logo">GI</div>
        <p style="margin-top:20px; font-size:0.8rem; letter-spacing:3px; text-transform:uppercase;">Grand Imperial</p>
    </div>

    <nav class="navbar">
        <div class="nav-logo">Grand Imperial</div>
        <div class="nav-links">
            <a href="#">The Venue</a>
            <a href="#experience">Experience</a>
            <a href="#dining">Dining</a>
            <a href="planner.php" class="btn-reserve">Plan Event</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-bg"></div>
        <div class="hero-content" data-aos="fade-up" data-aos-duration="1500">
            <span class="hero-subtitle">Jalalpur Jattan • Est 2024</span>
            <h1>A Symphony of<br>Luxury & Love</h1>
            <div style="margin-top: 40px;">
                <a href="planner.php" class="btn-reserve" style="padding: 15px 40px; font-size: 1rem;">Begin The Journey</a>
            </div>
        </div>
    </header>

    <section class="section-padding" id="experience">
        <div class="overlap-grid">
            <div class="overlap-img-wrapper" data-aos="fade-right">
                <img src="https://images.unsplash.com/photo-1519225421980-715cb0202128?q=80&w=1600" class="overlap-img">
                <div class="floating-box">
                    <h3 style="color:var(--gold); margin:0 0 10px 0;">3 Luxurious Halls</h3>
                    <p style="font-size:0.9rem; margin:0;">Segregated spaces for Barat, Walima, and Corporate Galas designed with acoustic perfection.</p>
                </div>
            </div>
            <div class="overlap-text" data-aos="fade-left">
                <h4 style="color:var(--gold);">The Grand Experience</h4>
                <h2 style="font-size: 3rem; margin-top: 10px;">Architecture of Dreams</h2>
                <p>We believe a wedding is not just an event; it is a legacy. The Grand Imperial offers an architectural marvel in the heart of Jalalpur Jattan.</p>
                <p>From our 24ft high ceilings to our imported crystal chandeliers, every corner is crafted for the perfect photograph.</p>
                <br>
                <a href="planner.php" style="color:var(--gold); text-decoration:none; text-transform:uppercase; letter-spacing:2px; font-size:0.8rem; border-bottom:1px solid var(--gold);">View Availability</a>
            </div>
        </div>
    </section>

    <section class="amenities-section">
        <div class="amenity-grid">
            <div class="amenity-card">
                <i class="fas fa-snowflake amenity-icon"></i>
                <h3>Climate Control</h3>
                <p style="font-size:0.9rem; color:#888;">State-of-the-art HVAC systems for year-round comfort.</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-parking amenity-icon"></i>
                <h3>Valet Parking</h3>
                <p style="font-size:0.9rem; color:#888;">Secure facility for 400+ vehicles with valet service.</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-bolt amenity-icon"></i>
                <h3>Power Backup</h3>
                <p style="font-size:0.9rem; color:#888;">Industrial generators ensuring zero downtime.</p>
            </div>
            <div class="amenity-card">
                <i class="fas fa-shield-alt amenity-icon"></i>
                <h3>Secure Venue</h3>
                <p style="font-size:0.9rem; color:#888;">24/7 CCTV surveillance and uniformed security.</p>
            </div>
        </div>
    </section>

    <section class="section-padding packages-section" id="dining">
        <div class="glass-overlay"></div>
        <div class="pkg-container">
            <div style="grid-column: 1 / -1; text-align:center; margin-bottom:50px;">
                <h2 style="font-size:3rem; color:white;">Culinary Art</h2>
                <div style="width:50px; height:2px; background:var(--gold); margin:20px auto;"></div>
            </div>

            <?php foreach($data['packages'] as $pkg): ?>
            <div class="pkg-card" data-aos="fade-up">
                <div class="pkg-name"><?php echo $pkg['name']; ?></div>
                <div class="pkg-price">Rs. <?php echo number_format($pkg['price']); ?></div>
                <ul class="pkg-list">
                    <?php foreach($pkg['dishes'] as $dish): ?>
                    <li><i class="fas fa-circle-notch"></i> <?php echo $dish; ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="planner.php" class="btn-reserve" style="width:100%; display:inline-block; text-align:center;">Customize</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <div class="footer-logo">Grand Imperial</div>
        <p style="font-size:0.9rem; opacity:0.7;">Circular Road, Jalalpur Jattan, Gujrat.</p>
        <div class="socials" style="margin: 30px 0;">
            <i class="fab fa-instagram"></i>
            <i class="fab fa-facebook-f"></i>
            <i class="fab fa-whatsapp"></i>
        </div>
        <p style="font-size:0.7rem; color:#444; text-transform:uppercase; letter-spacing:2px;">
            Designed by <a href="https://arhamprinters.pk" style="color:#666; text-decoration:none;">Arham Printers</a>
        </p>
    </footer>

    <a href="planner.php" class="wa-float"><i class="fas fa-calendar-check"></i></a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });
        
        window.addEventListener('load', () => {
            const loader = document.getElementById('preloader');
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 1000);
        });
    </script>
</body>
</html>