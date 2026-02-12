<?php
// =================================================================
// DATA CONFIGURATION
// =================================================================

$data = [
    "settings" => [
        "title" => "Winter Snow Sports Gala",
        "year" => "2025",
        "location" => "Rattu & Astore, Gilgit-Baltistan",
        "contact_phone" => "+923006238233",
        "display_phone" => "+92 300 6238233"
    ],
    "hero" => [
        "title" => "OPERATIONAL EXCELLENCE",
        "subtitle" => "Deployment Report: National Snow Sports Festival 2024-2025",
        "bg_image" => "https://arhamprinters.pk/img/rattu/rattu4.webp"
    ],
    // 1. MILITARY COMMAND (The 4 Key Pillars)
    "command_military" => [
        [
            "rank" => "Corps Commander",
            "role" => "Patron-in-Chief",
            "image" => "fcnalogo.webp" // Upload Image
        ],
        [
            "rank" => "Division Commander",
            "role" => "Strategic Command",
            "image" => "tigerlogo.webp" // Upload Image
        ],
        [
            "rank" => "Brigade Commander",
            "role" => "Operational Command",
            "image" => "80bde.webp" // Upload Image
        ],
        [
            "rank" => "Unit Commander",
            "role" => "Field Execution",
            "image" => "30sr.webp" // Upload Image
        ]
    ],
    // 2. CIVIL ADMINISTRATION (Govt & DC Astore)
    "command_civil" => [
        [
            "title" => "Government of Gilgit-Baltistan",
            "role" => "Provincial Patronage",
            "image" => "gblogo.webp" // Upload Logo/Image
        ],
        [
            "title" => "Deputy Commissioner Astore",
            "role" => "District Administration",
            "image" => "gblogo.webp" // Upload Image
        ]
    ],
    // 3. HISTORICAL LEGACY
    "heroes" => [
        [
            "name" => "Muhammad Ali Jinnah",
            "title" => "Quaid-e-Azam",
            "desc" => "The Father of the Nation.",
            "image" => "quaid.webp"
        ],
        [
            "name" => "Dr. Allama Iqbal",
            "title" => "Mufakkir-e-Pakistan",
            "desc" => "The Dreamer of Independence.",
            "image" => "iqbal.webp"
        ],
        [
            "name" => "Col. Mirza Hassan Khan",
            "title" => "Liberator of GB",
            "desc" => "Hero of the 1947 Revolution.",
            "image" => "hassan.webp"
        ],
        [
            "name" => "Subedar Major Babar Khan",
            "title" => "Ghazi-e-Millat",
            "desc" => "Key figure of Freedom.",
            "image" => "babar.webp"
        ]
    ],
    "gallery" => [
        [ "title" => "Grand Arena", "desc" => "100ft Impact Backdrop", "image" => "https://arhamprinters.pk/img/rattu/rattu1.webp", "col" => "col-md-8" ],
        [ "title" => "Alpine Track", "desc" => "High-Altitude Branding", "image" => "https://arhamprinters.pk/img/rattu/rattu2.webp", "col" => "col-md-4" ],
        [ "title" => "Victory Gates", "desc" => "3D Arch Structures", "image" => "https://arhamprinters.pk/img/rattu/rattu5.webp", "col" => "col-md-4" ],
        [ "title" => "Media Zone", "desc" => "Broadcast Backdrops", "image" => "https://arhamprinters.pk/img/rattu/rattu6.webp", "col" => "col-md-8" ]
    ],
    "stats" => [
        [ "val" => "9,500ft", "lbl" => "Altitude Execution" ],
        [ "val" => "12,000+", "lbl" => "Sq.ft Deployed" ],
        [ "val" => "Zero", "lbl" => "Compromise on Quality" ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['settings']['title']; ?> | Arham Printers</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<?php include '../../../seo/seo.php'; ?>
<?php include '../../../secure.php'; ?>
    <style>
        :root {
            --gold: #C5A059;
            --gold-glow: #ffd700;
            --navy: #0B1622;
            --navy-light: #162433;
            --snow: #ffffff;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--navy);
            color: var(--snow);
            overflow-x: hidden;
        }

        /* --- TYPOGRAPHY --- */
        .font-royal { font-family: 'Cinzel', serif; letter-spacing: 1px; }
        .text-gold { color: var(--gold) !important; }
        
        .divider-gold {
            height: 3px; width: 80px; background: var(--gold);
            margin: 20px auto; position: relative;
        }
        .divider-gold::after {
            content: '★'; position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: var(--navy); color: var(--gold); padding: 0 10px; font-size: 1.2rem;
        }

        /* --- HERO --- */
        .hero-wrap {
            height: 100vh; position: relative; display: flex; align-items: center; justify-content: center;
            background-attachment: fixed; overflow: hidden;
        }
        .hero-bg {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(11, 22, 34, 0.5), rgba(11, 22, 34, 0.9)), url('<?php echo $data['hero']['bg_image']; ?>');
            background-size: cover; background-position: center;
            animation: zoomHero 25s infinite alternate;
        }
        @keyframes zoomHero { from { transform: scale(1); } to { transform: scale(1.15); } }
        
        .hero-glass {
            background: rgba(11, 22, 34, 0.7); backdrop-filter: blur(10px);
            padding: 3rem; border: 1px solid rgba(197, 160, 89, 0.3);
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            position: relative; z-index: 2;
        }

        /* --- 3D CARD ANIMATIONS --- */
        .hover-card {
            background: var(--navy-light);
            border: 1px solid rgba(197, 160, 89, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative; overflow: hidden;
        }
        .hover-card:hover {
            transform: translateY(-10px);
            border-color: var(--gold);
            box-shadow: 0 15px 30px rgba(0,0,0,0.5), 0 0 15px rgba(197, 160, 89, 0.2);
        }
        /* Shine Effect */
        .hover-card::before {
            content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-25deg); transition: 0.5s;
        }
        .hover-card:hover::before { left: 150%; }

        /* --- OFFICIALS IMAGES --- */
        .official-img-box {
            width: 140px; height: 140px; margin: 0 auto 20px;
            border-radius: 50%; padding: 5px;
            border: 2px solid var(--gold);
            position: relative;
        }
        .official-img {
            width: 100%; height: 100%; border-radius: 50%;
            object-fit: cover; filter: grayscale(30%);
            transition: 0.4s;
        }
        .hover-card:hover .official-img { filter: grayscale(0%); transform: scale(1.05); }

        /* --- GALLERY GRID --- */
        .gallery-item {
            position: relative; overflow: hidden; height: 350px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .gallery-item img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.8s;
        }
        .gallery-item:hover img { transform: scale(1.15); }
        .gallery-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(11,22,34,0.95), transparent);
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 2rem; opacity: 0; transform: translateY(20px);
            transition: 0.4s;
        }
        .gallery-item:hover .gallery-overlay { opacity: 1; transform: translateY(0); }

        /* --- CTA BUTTON --- */
        .btn-gold-glow {
            color: var(--gold); border: 2px solid var(--gold);
            padding: 15px 40px; font-family: 'Cinzel', serif; font-weight: 700;
            text-decoration: none; display: inline-block;
            box-shadow: 0 0 10px rgba(197, 160, 89, 0.2);
            transition: 0.3s;
        }
        .btn-gold-glow:hover {
            background: var(--gold); color: #000;
            box-shadow: 0 0 30px rgba(197, 160, 89, 0.6);
        }

        /* --- SNOW CANVAS --- */
        #snow { position: fixed; inset: 0; pointer-events: none; z-index: 1; opacity: 0.3; }
    </style>
</head>
<body>

    <canvas id="snow"></canvas>

    <nav class="navbar fixed-top p-3" style="background: rgba(11,22,34,0.95); border-bottom: 1px solid rgba(197,160,89,0.3);">
        <div class="container">
            <div class="font-royal fw-bold h5 m-0 text-gold">ARHAM PRINTERS</div>
            <div class="small text-white-50 ms-auto font-royal d-none d-sm-block">OFFICIAL BRANDING PARTNER</div>
        </div>
    </nav>

    <header class="hero-wrap">
        <div class="hero-bg"></div>
        <div class="container text-center position-relative z-2">
            <div class="hero-glass mx-auto" style="max-width: 900px;" data-aos="zoom-in" data-aos-duration="1000">
                <div class="text-gold font-royal small letter-spacing-2 mb-2">REPORT: <?php echo $data['settings']['year']; ?></div>
                <h1 class="display-3 font-royal fw-bold text-white mb-3"><?php echo $data['hero']['title']; ?></h1>
                <div class="divider-gold"></div>
                <p class="lead text-white-50 mb-4"><?php echo $data['hero']['subtitle']; ?></p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#command" class="btn-gold-glow">View Leadership</a>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5" style="background: #0d1b2a;">
        <div class="container py-5 position-relative z-2">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-royal text-white">GUARDIANS OF INDEPENDENCE</h2>
                <p class="text-white-50">Honoring the architects of our freedom.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach($data['heroes'] as $i => $hero): ?>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?php echo $i*100; ?>">
                    <div class="hover-card h-100 p-4 text-center rounded">
                        <div class="official-img-box">
                            <img src="<?php echo $hero['image']; ?>" class="official-img">
                        </div>
                        <h6 class="font-royal text-gold mb-1"><?php echo $hero['title']; ?></h6>
                        <div class="small text-white fw-bold"><?php echo $hero['name']; ?></div>
                        <p class="small text-white-50 mt-2 mb-0 d-none d-md-block"><?php echo $hero['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="command" class="py-5 bg-dark" style="background: url('https://www.transparenttextures.com/patterns/cubes.png');">
        <div class="container py-5 position-relative z-2">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-royal text-gold">DISTINGUISHED MILITARY PATRONAGE</h2>
                <div class="divider-gold"></div>
                <p class="text-white-50">Under the visionary command of Pakistan Army Leadership.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php foreach($data['command_military'] as $i => $cm): ?>
                <div class="col-6 col-lg-3" data-aos="flip-left" data-aos-delay="<?php echo $i*150; ?>">
                    <div class="hover-card h-100 p-4 text-center rounded">
                        <div class="official-img-box" style="width: 120px; height: 120px;">
                            <img src="<?php echo $cm['image']; ?>" class="official-img">
                        </div>
                        <h5 class="font-royal text-white mb-1"><?php echo $cm['rank']; ?></h5>
                        <div class="badge bg-secondary font-royal small"><?php echo $cm['role']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: linear-gradient(to right, #0B1622, #152331);">
        <div class="container py-5 position-relative z-2">
            <div class="row align-items-center g-5">
                <div class="col-lg-4 text-center text-lg-start" data-aos="fade-right">
                    <h2 class="font-royal text-gold">STRATEGIC GOVERNANCE</h2>
                    <p class="text-white-50 mb-0">In close coordination with the Civil Administration of Gilgit-Baltistan and District Astore to ensure regional prosperity and seamless execution.</p>
                </div>
                <div class="col-lg-8">
                    <div class="row g-4">
                        <?php foreach($data['command_civil'] as $cc): ?>
                        <div class="col-md-6" data-aos="zoom-in">
                            <div class="d-flex align-items-center hover-card p-3 rounded">
                                <img src="<?php echo $cc['image']; ?>" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid var(--gold); object-fit: cover;">
                                <div class="ms-3">
                                    <h5 class="font-royal text-white mb-1"><?php echo $cc['title']; ?></h5>
                                    <div class="small text-gold"><?php echo $cc['role']; ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-black">
        <div class="container py-5 position-relative z-2">
            <div class="text-center mb-5">
                <h2 class="font-royal text-white">DEPLOYMENT VISUALS</h2>
                <p class="text-white-50">Precision branding in extreme conditions.</p>
            </div>
            <div class="row g-4">
                <?php foreach($data['gallery'] as $i => $item): ?>
                <div class="<?php echo $item['col']; ?>" data-aos="fade-up">
                    <div class="gallery-item rounded">
                        <img src="<?php echo $item['image']; ?>">
                        <div class="gallery-overlay">
                            <h4 class="font-royal text-gold mb-1"><?php echo $item['title']; ?></h4>
                            <div class="small text-white text-uppercase letter-spacing-2"><?php echo $item['desc']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5" style="border-top: 1px solid rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container">
            <div class="row text-center g-4">
                <?php foreach($data['stats'] as $stat): ?>
                <div class="col-md-4" data-aos="fade-up">
                    <h2 class="display-4 font-royal text-gold mb-0"><?php echo $stat['val']; ?></h2>
                    <div class="small text-white-50 text-uppercase letter-spacing-2"><?php echo $stat['lbl']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="text-center py-5" style="background: #020c1b;">
        <div class="container py-4">
            <div class="border border-secondary p-5 position-relative overflow-hidden rounded" data-aos="zoom-in">
                <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(197,160,89,0.1) 0%, transparent 70%);"></div>
                
                <div class="position-relative z-2">
                    <h5 class="text-gold font-royal mb-3">OPERATIONAL READINESS CONFIRMED</h5>
                    <h2 class="text-white font-royal mb-5">Arham Printers is fully mobilized to execute the 2026 Visual Directive with enhanced capabilities and precision.</h2>
                    <a href="https://wa.me/<?php echo str_replace(['+',' '], '', $data['settings']['contact_phone']); ?>" class="btn-gold-glow">
                        <i class="fab fa-whatsapp me-2"></i> ESTABLISH STRATEGIC CONTACT
                    </a>
                </div>
            </div>
            <p class="text-white-50 mt-5 small mb-0">&copy; <?php echo date('Y'); ?> Arham Printers. Jalalpur Jattan.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });

        // Cinematic Snow
        const canvas = document.getElementById('snow');
        const ctx = canvas.getContext('2d');
        let w = canvas.width = window.innerWidth;
        let h = canvas.height = window.innerHeight;
        const particles = Array.from({length: 100}, () => ({
            x: Math.random()*w, y: Math.random()*h, r: Math.random()*2+0.5, d: Math.random()*0.5+0.5
        }));

        function draw() {
            ctx.clearRect(0,0,w,h);
            ctx.fillStyle = "rgba(255,255,255,0.4)";
            ctx.beginPath();
            particles.forEach(p => {
                ctx.moveTo(p.x, p.y); ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
            });
            ctx.fill();
            move();
        }
        function move() {
            particles.forEach(p => {
                p.y += p.d; if(p.y > h) p.y = -5;
            });
            requestAnimationFrame(draw);
        }
        draw();
        window.onresize = () => { w = canvas.width = window.innerWidth; h = canvas.height = window.innerHeight; };
    </script>
</body>
</html>