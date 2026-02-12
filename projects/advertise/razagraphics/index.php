<?php
// Load Data
$json = file_get_contents('data.json');
$data = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['settings']['app_name']; ?> | Production House</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Amiri:wght@400;700&family=Montserrat:wght@300;500;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --gold: #c5a059;
            --gold-glow: #ffd700;
            --dark-bg: #0a0a0a;
            --dark-card: #141414;
            --islamic-green: #1a472a;
        }

        body {
            background-color: var(--dark-bg);
            color: white;
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
        }

        /* --- ISLAMIC GEOMETRY PATTERN OVERLAY --- */
        .pattern-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(var(--gold) 1px, transparent 1px), radial-gradient(var(--gold) 1px, transparent 1px);
            background-size: 50px 50px;
            background-position: 0 0, 25px 25px;
            opacity: 0.05;
            z-index: -1;
            pointer-events: none;
        }

        /* --- TYPOGRAPHY --- */
        .font-arabic { font-family: 'Amiri', serif; }
        .font-luxury { font-family: 'Cinzel', serif; letter-spacing: 2px; }
        .text-gold { color: var(--gold) !important; }

        /* --- NAVBAR --- */
        .navbar {
            background: rgba(10, 10, 10, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(197, 160, 89, 0.2);
        }
        .nav-link { color: #ccc !important; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; transition: 0.3s; }
        .nav-link:hover { color: var(--gold) !important; text-shadow: 0 0 10px var(--gold); }

        /* --- HERO SECTION --- */
        .hero {
            height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .hero-bg {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), var(--dark-bg)), url('<?php echo $data['hero']['bg_image']; ?>');
            background-size: cover; background-position: center;
            animation: zoomSlow 20s infinite alternate;
            z-index: 0;
        }
        .hero-content { position: relative; z-index: 2; text-align: center; }
        .bismillah {
            font-size: 3rem; color: var(--gold); margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(197, 160, 89, 0.5);
            animation: fadeInDown 2s ease;
        }
        
        /* --- 3D TILT CARDS --- */
        .card-3d-wrap { perspective: 1000px; }
        .card-3d {
            background: var(--dark-card);
            border: 1px solid rgba(197, 160, 89, 0.1);
            padding: 30px;
            border-radius: 15px;
            transform-style: preserve-3d;
            transform: translateZ(0);
            transition: transform 0.1s; /* Smooth JS tilt */
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .card-3d::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(197, 160, 89, 0.1), transparent 70%);
            opacity: 0; transition: 0.5s;
        }
        .card-3d:hover { border-color: var(--gold); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .card-3d:hover::before { opacity: 1; }
        
        .card-icon-box {
            width: 80px; height: 80px; margin: 0 auto 20px;
            border: 2px solid var(--gold);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: var(--gold);
            transform: translateZ(20px); /* 3D pop */
            background: rgba(0,0,0,0.5);
        }

        /* --- PORTFOLIO --- */
        .portfolio-item {
            position: relative; overflow: hidden; border-radius: 10px; height: 300px;
            border: 1px solid #333;
        }
        .portfolio-img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .portfolio-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, black, transparent);
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 20px;
            opacity: 0; transform: translateY(20px);
            transition: 0.4s;
        }
        .portfolio-item:hover .portfolio-img { transform: scale(1.1) rotate(2deg); }
        .portfolio-item:hover .portfolio-overlay { opacity: 1; transform: translateY(0); }

        /* --- PRICING (ISLAMIC ARCH) --- */
        .price-card {
            background: #111;
            border: 1px solid #333;
            border-top: 5px solid var(--gold);
            border-radius: 10px 10px 0 0; /* Arch hint */
            padding: 40px 20px;
            text-align: center;
            transition: 0.3s;
            position: relative;
        }
        .price-card:hover { transform: translateY(-10px); box-shadow: 0 0 25px rgba(197, 160, 89, 0.2); }
        .price-amount { font-size: 2.5rem; font-weight: 800; color: white; margin: 15px 0; }
        .price-amount span { font-size: 1rem; color: var(--gold); }

        /* --- ANIMATIONS --- */
        @keyframes zoomSlow { 0% { transform: scale(1); } 100% { transform: scale(1.1); } }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-10px); } 100% { transform: translateY(0px); } }

        /* --- BUTTONS --- */
        .btn-gold {
            background: linear-gradient(45deg, #aa771c, #fcf6ba, #aa771c);
            background-size: 200%;
            color: #000;
            border: none;
            padding: 12px 35px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.5s;
            border-radius: 0;
            clip-path: polygon(10% 0, 100% 0, 100% 80%, 90% 100%, 0 100%, 0 20%);
        }
        .btn-gold:hover { background-position: right center; box-shadow: 0 0 20px var(--gold); color: #000; }

    </style>
</head>
<body>

    <div class="pattern-overlay"></div>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand font-luxury text-white" href="#">
                <?php echo $data['settings']['app_name']; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars text-gold"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Work</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Packages</a></li>
                    <li class="nav-item ms-3">
                        <a href="https://wa.me/<?php echo $data['settings']['whatsapp']; ?>" class="btn-gold">
                            <i class="fab fa-whatsapp me-2"></i> Discuss
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero">
        <div class="hero-bg"></div>
        <div class="container hero-content">
            <div class="bismillah font-arabic"><?php echo $data['settings']['bismillah']; ?></div>
            <h1 class="display-3 font-luxury fw-bold text-white mb-3" data-aos="zoom-in">
                <?php echo $data['hero']['title']; ?>
            </h1>
            <div style="height: 3px; width: 100px; background: var(--gold); margin: 0 auto 30px;" data-aos="fade-right"></div>
            <p class="lead text-light mb-5 font-arabic fs-4" data-aos="fade-up" data-aos-delay="200">
                <?php echo $data['hero']['subtitle']; ?>
            </p>
            <div data-aos="fade-up" data-aos-delay="400">
                <a href="#portfolio" class="btn-gold me-3">View Masterpieces</a>
                <a href="#services" class="text-white text-decoration-none border-bottom border-warning pb-1">Explore Services</a>
            </div>
        </div>
    </section>

    <section id="services" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-down">
                <span class="text-gold text-uppercase small letter-spacing-2">What We Do</span>
                <h2 class="font-luxury display-5 mt-2">Our Expertise</h2>
            </div>
            <div class="row g-4">
                <?php foreach($data['services'] as $svc): ?>
                <div class="col-md-6 col-lg-3 card-3d-wrap" data-aos="flip-left" data-aos-delay="<?php echo $svc['id'] * 100; ?>">
                    <div class="card-3d js-tilt">
                        <div class="card-icon-box">
                            <i class="fas <?php echo $svc['icon']; ?>"></i>
                        </div>
                        <h4 class="font-luxury text-white mb-3 text-center" style="transform: translateZ(10px);">
                            <?php echo $svc['title']; ?>
                        </h4>
                        <p class="text-secondary text-center small mb-0" style="transform: translateZ(5px);">
                            <?php echo $svc['desc']; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="portfolio" class="py-5 bg-darker" style="background: rgba(0,0,0,0.3);">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="font-luxury text-white mb-0" data-aos="fade-right">Latest Creations</h2>
                    <p class="text-gold mb-0">Fusion of Art & Technology</p>
                </div>
                <a href="#" class="text-secondary text-decoration-none d-none d-md-block">See All <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="row g-3">
                <?php foreach($data['portfolio'] as $idx => $item): ?>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                    <div class="portfolio-item">
                        <img src="<?php echo $item['img']; ?>" class="portfolio-img">
                        <div class="portfolio-overlay">
                            <span class="badge bg-warning text-dark mb-2 w-auto align-self-start"><?php echo $item['cat']; ?></span>
                            <h3 class="font-luxury text-white mb-0"><?php echo $item['title']; ?></h3>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <i class="fas fa-crown text-gold fa-2x mb-3" style="animation: float 3s infinite ease-in-out;"></i>
                <h2 class="font-luxury">Premium Packages</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach($data['pricing'] as $idx => $pkg): ?>
                <div class="col-md-4" data-aos="zoom-in" data-aos-delay="<?php echo $idx * 150; ?>">
                    <div class="price-card">
                        <h5 class="text-secondary text-uppercase"><?php echo $pkg['plan']; ?></h5>
                        <div class="price-amount">
                            <span>PKR</span> <?php echo $pkg['price']; ?>
                        </div>
                        <ul class="list-unstyled text-secondary my-4">
                            <?php foreach($pkg['features'] as $ft): ?>
                                <li class="mb-3 border-bottom border-dark pb-2">
                                    <i class="fas fa-check text-gold me-2"></i> <?php echo $ft; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="https://wa.me/<?php echo $data['settings']['whatsapp']; ?>?text=I want to book <?php echo $pkg['plan']; ?>" class="btn btn-outline-light rounded-0 w-100">
                            Select Plan
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="py-5 text-center border-top border-secondary bg-black">
        <div class="container">
            <h2 class="font-luxury text-white mb-3"><?php echo $data['settings']['app_name']; ?></h2>
            <p class="text-gold font-arabic mb-4">"Creating Beauty is an Act of Worship"</p>
            
            <div class="d-flex justify-content-center gap-4 mb-5">
                <a href="#" class="text-secondary fs-4 hover-gold"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-secondary fs-4 hover-gold"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-secondary fs-4 hover-gold"><i class="fab fa-youtube"></i></a>
            </div>
            
            <p class="small text-secondary mb-0">
                &copy; 2025 <?php echo $data['settings']['app_name']; ?>. All Rights Reserved. 
                <br>Powered by <a href="#" class="text-gold text-decoration-none">Arham Printers</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
    
    <script>
        // Init Scroll Animations
        AOS.init({ duration: 1000, once: true });

        // Init 3D Tilt Effect
        VanillaTilt.init(document.querySelectorAll(".js-tilt"), {
            max: 15,
            speed: 400,
            glare: true,
            "max-glare": 0.2,
            scale: 1.05
        });
    </script>
</body>
</html>