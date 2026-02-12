<?php
// =================================================================
// CONFIGURATION & DATA (Embedded for Single File Usage)
// =================================================================

// Define the Data Structure (Formerly JSON)
$data = [
    "settings" => [
        "title" => "Winter Gala Commendation",
        "client" => "Pakistan Army & Govt of Gilgit-Baltistan",
        "year" => "2025",
        "location" => "Rattu, Gilgit Baltistan",
        "contact_phone" => "+923006238233", // Sanitized
        "display_phone" => "+92 300 6238233"
    ],
    "hero" => [
        "title" => "LEGENDS OF THE NORTH",
        "subtitle" => "Visual Deployment Report: National Snow Sports Festival",
        "bg_image" => "https://arhamprinters.pk/img/rattu/rattu4.webp"
    ],
    "organizers" => [
        [ "name" => "FCNA", "logo" => "https://arhamprinters.pk/img/rattu/fcnalogo.webp" ],
        [ "name" => "80 Brigade", "logo" => "https://arhamprinters.pk/img/rattu/80bde.webp" ],
        [ "name" => "Tiger Force", "logo" => "https://arhamprinters.pk/img/rattu/tigerlogo.webp" ]
    ],
    "gallery" => [
        [ "title" => "The Grand Arena", "desc" => "100ft Panoramic Impact Backdrop", "image" => "https://arhamprinters.pk/img/rattu/rattu1.webp", "size" => "col-md-8" ],
        [ "title" => "Alpine Track", "desc" => "High-Altitude Course Branding", "image" => "https://arhamprinters.pk/img/rattu/rattu2.webp", "size" => "col-md-4" ],
        [ "title" => "Victory Gates", "desc" => "3D Arch Structures", "image" => "https://arhamprinters.pk/img/rattu/rattu5.webp", "size" => "col-md-4" ],
        [ "title" => "Media Zone", "desc" => "Broadcast Ready Backdrops", "image" => "https://arhamprinters.pk/img/rattu/rattu6.webp", "size" => "col-md-8" ]
    ],
    "stats" => [
        [ "label" => "Square Feet Deployed", "value" => "9,000+" ],
        [ "label" => "Visual Assets", "value" => "500+" ],
        [ "label" => "Altitude Execution", "value" => "9,500ft" ]
    ]
];

// Smart SEO Generation
$page_title = $data['settings']['title'] . " | Arham Printers";
$page_desc = "Official visual branding portfolio for the National Snow Sports Gala, executed by Arham Printers for Pakistan Army and FCNA.";
$page_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_desc; ?>">
    
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $page_desc; ?>">
    <meta property="og:image" content="<?php echo $data['hero']['bg_image']; ?>">
    <meta property="og:url" content="<?php echo $page_url; ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --gold: #D4AF37;
            --gold-dim: #AA8C2C;
            --dark-blue: #0a192f;
            --snow: #f8f9fa;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--dark-blue);
            color: var(--snow);
            overflow-x: hidden;
        }

        /* TYPOGRAPHY */
        .font-ceremonial { font-family: 'Cinzel', serif; letter-spacing: 2px; }
        .text-gold { color: var(--gold) !important; }
        
        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--dark-blue); }
        ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 4px; }

        /* HERO SECTION */
        .hero-section {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(10, 25, 47, 0.7), rgba(10, 25, 47, 0.9)), url('<?php echo $data['hero']['bg_image']; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: 0;
            animation: zoomSlow 20s infinite alternate;
        }
        @keyframes zoomSlow { from { transform: scale(1); } to { transform: scale(1.1); } }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            border: 1px solid rgba(212, 175, 55, 0.3);
            padding: 4rem 2rem;
            background: rgba(10, 25, 47, 0.6);
            backdrop-filter: blur(5px);
            max-width: 900px;
        }

        /* ORGANIZERS */
        .organizer-strip {
            border-top: 1px solid var(--gold);
            border-bottom: 1px solid var(--gold);
            background: rgba(0,0,0,0.5);
            padding: 2rem 0;
        }
        .org-logo {
            filter: grayscale(100%) brightness(1.2);
            transition: all 0.4s ease;
            height: 70px;
            object-fit: contain;
        }
        .org-logo:hover { filter: grayscale(0%); transform: scale(1.1); }

        /* GALLERY CARDS */
        .gallery-card {
            position: relative;
            overflow: hidden;
            border-radius: 4px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            height: 400px;
            group: hover;
        }
        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .gallery-overlay {
            position: absolute;
            bottom: 0; left: 0; width: 100%;
            background: linear-gradient(to top, #0a192f, transparent);
            padding: 2rem;
            transform: translateY(20px);
            transition: 0.4s;
        }
        .gallery-card:hover .gallery-img { transform: scale(1.1); }
        .gallery-card:hover .gallery-overlay { transform: translateY(0); }

        /* STATS */
        .stat-box {
            border: 1px solid var(--gold);
            padding: 2rem;
            text-align: center;
            transition: 0.3s;
            background: rgba(212, 175, 55, 0.05);
        }
        .stat-box:hover { background: rgba(212, 175, 55, 0.1); transform: translateY(-5px); }

        /* CTA BUTTON */
        .btn-royal {
            background: transparent;
            color: var(--gold);
            border: 2px solid var(--gold);
            padding: 15px 40px;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
            display: inline-block;
            text-decoration: none;
        }
        .btn-royal:hover {
            background: var(--gold);
            color: var(--dark-blue);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.5);
        }

        /* CANVAS SNOW */
        #snow-canvas {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; z-index: 1; opacity: 0.4;
        }
    </style>
</head>
<body>

    <canvas id="snow-canvas"></canvas>

    <nav class="navbar fixed-top p-3" style="background: rgba(10, 25, 47, 0.9); border-bottom: 1px solid rgba(212,175,55,0.2);">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="font-ceremonial fw-bold h5 m-0 text-gold">ARHAM PRINTERS</div>
            <div class="small text-white-50 d-none d-md-block">OFFICIAL PRINTING PARTNER</div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="hero-bg"></div>
        <div class="container hero-content" data-aos="zoom-in" data-aos-duration="1500">
            <div class="text-gold font-ceremonial mb-3 small">ESTABLISHED EXCELLENCE IN JALALPUR JATTAN</div>
            <h1 class="display-3 font-ceremonial fw-bold text-white mb-4"><?php echo $data['hero']['title']; ?></h1>
            <div style="height: 2px; width: 100px; background: var(--gold); margin: 0 auto 20px auto;"></div>
            <h4 class="font-ceremonial text-gold mb-4"><?php echo $data['hero']['subtitle']; ?></h4>
            <p class="lead text-white-50 mb-5">
                Executing precision visual branding for the Pakistan Army & Government of Gilgit-Baltistan in the harshest terrains.
            </p>
            <a href="#portfolio" class="btn-royal me-3">View Visual Report</a>
        </div>
    </header>

    <section class="organizer-strip">
        <div class="container">
            <div class="row text-center align-items-center justify-content-center g-4">
                <div class="col-12 text-center mb-2">
                    <small class="font-ceremonial text-gold">IN STRATEGIC PARTNERSHIP WITH</small>
                </div>
                <?php foreach($data['organizers'] as $org): ?>
                <div class="col-4 col-md-2" data-aos="fade-up">
                    <img src="<?php echo $org['logo']; ?>" alt="<?php echo $org['name']; ?>" class="org-logo" title="<?php echo $org['name']; ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-dark position-relative">
        <div class="container py-5 position-relative" style="z-index: 2;">
            <div class="row g-4">
                <?php foreach($data['stats'] as $index => $stat): ?>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="stat-box">
                        <h2 class="display-4 font-ceremonial text-white mb-0"><?php echo $stat['value']; ?></h2>
                        <div class="text-gold small letter-spacing-2 mt-2 text-uppercase"><?php echo $stat['label']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="portfolio" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-ceremonial text-gold">MISSION VISUALS: <?php echo $data['settings']['year']; ?></h2>
                <p class="text-white-50">Documenting the scale and precision of branding at <?php echo $data['settings']['location']; ?></p>
            </div>

            <div class="row g-4">
                <?php foreach($data['gallery'] as $index => $item): ?>
                <div class="<?php echo $item['size']; ?>" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="gallery-card">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="gallery-img">
                        <div class="gallery-overlay">
                            <h4 class="font-ceremonial text-white mb-1"><?php echo $item['title']; ?></h4>
                            <div class="text-gold small text-uppercase"><?php echo $item['desc']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: radial-gradient(circle at center, #112240 0%, #0a192f 100%);">
        <div class="container text-center py-5">
            <div class="border border-warning p-5 position-relative overflow-hidden" data-aos="zoom-in">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(212, 175, 55, 0.05);"></div>
                <div class="position-relative z-2">
                    <h6 class="text-gold font-ceremonial mb-3">UPCOMING SEASON PROTOCOLS</h6>
                    <h2 class="display-5 text-white font-ceremonial mb-4">READY FOR DEPLOYMENT</h2>
                    <p class="lead text-white-50 mb-5 mx-auto" style="max-width: 700px;">
                        Arham Printers stands ready to execute the branding requirements for the upcoming Snow Sports Gala with enhanced capabilities and premium materials.
                    </p>
                    <a href="https://wa.me/<?php echo $data['settings']['contact_phone']; ?>" class="btn-royal">
                        <i class="fab fa-whatsapp me-2"></i> Discuss <?php echo date('Y') + 1; ?> Strategy
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 text-center border-top border-secondary" style="background: #020c1b;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0 text-white-50 small">&copy; <?php echo date('Y'); ?> Arham Printers. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-gold small font-ceremonial">JALALPUR JATTAN • PAKISTAN</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init Animations
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-out-cubic'
        });

        // Elegant Snow Effect (Canvas)
        const canvas = document.getElementById('snow-canvas');
        const ctx = canvas.getContext('2d');
        let width = window.innerWidth;
        let height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;

        const flakes = [];
        const flakeCount = 100; // Not too many, keep it classy

        function Flake() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = Math.random() * 1 - 0.5;
            this.vy = Math.random() * 1 + 0.5;
            this.radius = Math.random() * 2;
            this.alpha = Math.random() * 0.5 + 0.1;
        }

        Flake.prototype.draw = function() {
            ctx.fillStyle = `rgba(255, 255, 255, ${this.alpha})`;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fill();
        };

        Flake.prototype.update = function() {
            this.x += this.vx;
            this.y += this.vy;
            if (this.y > height) {
                this.y = 0;
                this.x = Math.random() * width;
            }
        };

        for (let i = 0; i < flakeCount; i++) {
            flakes.push(new Flake());
        }

        function animateSnow() {
            ctx.clearRect(0, 0, width, height);
            flakes.forEach(flake => {
                flake.update();
                flake.draw();
            });
            requestAnimationFrame(animateSnow);
        }

        animateSnow();

        window.addEventListener('resize', function() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
        });
    </script>
</body>
</html>