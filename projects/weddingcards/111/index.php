<?php
// --- DATA RETRIEVAL ---
// Strictly fetching from your existing wedding_data.json structure
$json_file = 'wedding_data.json';

if (!file_exists($json_file)) {
    die('<div style="color:white; text-align:center; padding:50px; background:#000;">Error: wedding_data.json not found. Please upload the file.</div>');
}

$json_data = file_get_contents($json_file);
$data = json_decode($json_data, true);

if (!$data) {
    die('<div style="color:white; text-align:center; padding:50px; background:#000;">Error: Invalid JSON format.</div>');
}

// Map JSON keys to variables for easier usage
$config = $data['config'];
$lists = $data['lists'];
$events = $data['events'];
$contacts = $data['contacts'];
$footer = $data['footer'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['groom_name']; ?> | Wedding Invitation</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel+Decorative:wght@400;700;900&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- MASTERPIECE THEME CONFIG --- */
        :root {
            --bg-dark: #0a0a0a;
            --bg-panel: #141414;
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #aa771c;
            --text-muted: #a0a0a0;
            
            /* The Royal Gradient */
            --gold-gradient: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            --gold-text-clip: linear-gradient(180deg, #bf953f 0%, #fcf6ba 50%, #b38728 100%);
        }

        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at 50% 50%, #1a1a1a 0%, #000000 100%);
            color: #fff;
            font-family: 'Jost', sans-serif;
            overflow-x: hidden;
            margin: 0;
        }

        /* --- PRELOADER --- */
        #preloader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #000; z-index: 9999;
            display: flex; justify-content: center; align-items: center;
            transition: opacity 1s ease;
        }
        .rings-loader {
            width: 80px; height: 80px; border: 4px solid rgba(212, 175, 55, 0.3);
            border-top: 4px solid var(--gold-primary); border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* --- TYPOGRAPHY CLASSES --- */
        .font-script { font-family: 'Alex Brush', cursive; }
        .font-royal { font-family: 'Cinzel Decorative', cursive; letter-spacing: 2px; }
        
        .text-gold {
            background: var(--gold-text-clip);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0px 2px 10px rgba(212, 175, 55, 0.2);
        }
        
        .section-title {
            position: relative; display: inline-block;
            margin-bottom: 3rem; padding-bottom: 15px;
        }
        .section-title::after {
            content: ''; position: absolute; bottom: 0; left: 50%;
            transform: translateX(-50%); width: 60px; height: 3px;
            background: var(--gold-gradient);
        }

        /* --- 3D TILT CARD --- */
        .luxury-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform-style: preserve-3d;
            transform: perspective(1000px);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        /* Shine effect on card hover */
        .luxury-card::before {
            content: ''; position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: 0.5s;
        }
        .luxury-card:hover::before { left: 100%; }

        /* --- ORNAMENTAL BORDERS --- */
        .border-ornament {
            position: relative;
            border: 2px solid transparent;
            border-image: var(--gold-gradient);
            border-image-slice: 1;
            padding: 20px;
        }

        /* --- EVENTS TIMELINE --- */
        .event-box {
            background: linear-gradient(145deg, #111, #1a1a1a);
            border: 1px solid #333;
            border-bottom: 3px solid var(--gold-primary);
            border-radius: 10px;
            transition: all 0.4s ease;
        }
        .event-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.15);
            border-color: var(--gold-primary);
        }
        
        /* --- MUSIC PLAYER --- */
        .music-fab {
            position: fixed; bottom: 60px; left: 20px; z-index: 1000;
            width: 50px; height: 50px; border-radius: 50%;
            background: var(--bg-panel); border: 2px solid var(--gold-primary);
            color: var(--gold-primary); display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
            transition: transform 0.3s;
        }
        .music-fab:hover { transform: scale(1.1); }
        .music-wave { height: 20px; display: flex; align-items: flex-end; gap: 3px; }
        .bar { width: 3px; background: var(--gold-primary); animation: equalizer 1s infinite ease-in-out; }
        .bar:nth-child(2) { animation-delay: 0.1s; } .bar:nth-child(3) { animation-delay: 0.2s; }
        @keyframes equalizer { 0%, 100% { height: 5px; } 50% { height: 15px; } }

        /* --- TICKER --- */
        .ticker-bar {
            position: fixed; bottom: 0; width: 100%;
            background: var(--gold-gradient); color: #000;
            font-weight: 700; font-size: 0.85rem; padding: 6px 0;
            z-index: 2000; white-space: nowrap; overflow: hidden;
        }
        .ticker-move { display: inline-block; animation: ticker 30s linear infinite; padding-left: 100%; }
        @keyframes ticker { 0% { transform: translate3d(0, 0, 0); } 100% { transform: translate3d(-100%, 0, 0); } }

        /* --- FALLING PETALS --- */
        #canvas-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
    </style>
</head>
<body>

    <div id="preloader">
        <div class="text-center">
            <div class="rings-loader mx-auto mb-3"></div>
            <div class="font-royal text-gold" style="font-size: 1.2rem;">Loading Luxury...</div>
        </div>
    </div>

    <canvas id="canvas-container"></canvas>

    <audio id="bg-music" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>
    <div class="music-fab" onclick="toggleAudio()">
        <div class="music-wave" id="music-bars" style="display:none;">
            <div class="bar"></div><div class="bar"></div><div class="bar"></div>
        </div>
        <i class="fa fa-play" id="play-icon"></i>
    </div>

    <section class="d-flex align-items-center justify-content-center min-vh-100 position-relative" style="z-index: 2;">
        <div class="container text-center">
            
            <div class="luxury-card mx-auto" data-tilt data-tilt-max="5" data-tilt-speed="400" style="max-width: 800px;">
                
                <div class="mb-4" data-aos="fade-down">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/23/Bismillah.svg" 
                         style="width: 200px; filter: invert(100%) drop-shadow(0 0 5px gold);" alt="Bismillah">
                </div>

                <div data-aos="zoom-in" data-aos-delay="200">
                    <p class="font-royal text-uppercase text-white-50 mb-2" style="font-size: 0.9rem; letter-spacing: 4px;">Soliciting your gracious presence</p>
                    <h3 class="font-royal text-gold mb-4"><?php echo $config['host_parents']; ?></h3>
                </div>

                <div class="my-4 px-lg-5 text-muted" style="font-size: 1.1rem; line-height: 1.8;">
                    <?php echo $config['invitation_intro']; ?> <br>
                    <span class="text-white font-royal mt-2 d-inline-block border-bottom border-warning"><?php echo $config['event_type']; ?></span> <br>
                    <?php echo $config['relation_text']; ?>
                </div>

                <div class="py-4 position-relative">
                    <h1 class="font-royal display-2 text-gold fw-bold" data-aos="flip-up" style="text-shadow: 0 5px 15px rgba(0,0,0,0.8);">
                        <?php echo $config['groom_name']; ?>
                    </h1>
                    
                    <?php if(!empty($config['bride_name'])): ?>
                    <div class="font-script text-white display-5 my-2">&</div>
                    <h1 class="font-royal display-3 text-gold fw-bold" data-aos="flip-up" data-aos-delay="100">
                        <?php echo $config['bride_name']; ?>
                    </h1>
                    <?php endif; ?>
                </div>

                <div class="mt-5 animate__animated animate__bounce infinite">
                    <span class="text-white-50 text-uppercase small">Scroll to celebrate</span><br>
                    <i class="fa fa-chevron-down text-gold mt-2"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 position-relative" style="z-index: 2; background: rgba(0,0,0,0.4);">
        <div class="container py-5">
            <div class="text-center" data-aos="fade-up">
                <h2 class="section-title font-royal text-gold display-5">The Wedding Events</h2>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach($events as $index => $event): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="event-box h-100 p-4 text-center position-relative">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" 
                             style="width:70px; height:70px; border: 1px solid var(--gold-primary); background: #000;">
                            <i class="fa <?php echo $event['icon']; ?> fs-3 text-gold"></i>
                        </div>

                        <h3 class="font-royal text-white mb-2"><?php echo $event['name']; ?></h3>
                        <p class="text-gold font-royal fs-5 mb-4"><?php echo $event['date']; ?></p>
                        
                        <div style="width: 50px; height: 1px; background: #333; margin: 0 auto 20px;"></div>

                        <div class="d-flex justify-content-center gap-3 mb-4 flex-wrap">
                            <?php foreach($event['times'] as $time): ?>
                                <div class="badge bg-dark border border-secondary px-3 py-2">
                                    <span class="d-block text-gold fw-bold"><?php echo $time['time']; ?></span>
                                    <span class="d-block text-muted small text-uppercase" style="font-size: 0.65rem;"><?php echo $time['label']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-auto">
                            <h6 class="text-white fw-bold mb-0"><?php echo $event['venue_name']; ?></h6>
                            <small class="text-muted d-block mb-3"><?php echo $event['address']; ?></small>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                    <i class="fa fa-location-dot me-1 text-gold"></i> Map
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5 position-relative" style="z-index: 2;">
        <div class="container">
            <div class="luxury-card mx-auto" style="max-width: 900px;" data-aos="zoom-in">
                <div class="row align-items-center">
                    
                    <div class="col-md-6 border-end border-secondary mb-4 mb-md-0 text-center text-md-end pe-md-5">
                        <h4 class="font-royal text-gold mb-4">Cordially Invited By</h4>
                        <div class="row justify-content-end">
                            <?php foreach($lists['rsvp_people'] as $person): ?>
                            <div class="col-12 mb-2">
                                <span class="font-royal text-white-50" style="font-size: 0.9rem; letter-spacing: 1px;">
                                    <?php echo $person; ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-6 ps-md-5 text-center text-md-start">
                        <h4 class="font-royal text-gold mb-3">With Best Compliments From</h4>
                        <ul class="list-unstyled mb-4">
                            <?php foreach($lists['well_wishers'] as $wisher): ?>
                                <li class="font-script text-white display-6 mb-2"><?php echo $wisher; ?></li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="p-3 rounded" style="background: rgba(212, 175, 55, 0.1); border: 1px dashed var(--gold-dark);">
                            <p class="small text-uppercase text-muted mb-2">RSVP & Contact</p>
                            <div class="d-flex gap-2 justify-content-center justify-content-md-start">
                                <a href="tel:<?php echo $contacts['phone']; ?>" class="btn btn-warning w-100 fw-bold">
                                    <i class="fa fa-phone"></i> Call
                                </a>
                                <a href="https://wa.me/<?php echo $contacts['whatsapp']; ?>" class="btn btn-success w-100 fw-bold">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="ticker-bar">
        <div class="ticker-move">
            <?php echo $footer['ticker_text']; ?> &nbsp;&bull;&nbsp; 
            <?php echo $footer['ticker_text']; ?> &nbsp;&bull;&nbsp; 
            <?php echo $footer['ticker_text']; ?> &nbsp;&bull;&nbsp; 
            <?php echo $footer['ticker_text']; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
    
    <script>
        // --- 1. PRELOADER ---
        window.addEventListener('load', () => {
            const preloader = document.getElementById('preloader');
            preloader.style.opacity = '0';
            setTimeout(() => { preloader.style.display = 'none'; }, 1000);
            AOS.init({ duration: 1000, once: true });
        });

        // --- 2. MUSIC PLAYER LOGIC ---
        let isPlaying = false;
        const audio = document.getElementById("bg-music");
        const bars = document.getElementById("music-bars");
        const icon = document.getElementById("play-icon");

        function toggleAudio() {
            if (isPlaying) {
                audio.pause();
                bars.style.display = "none";
                icon.className = "fa fa-play";
                icon.style.display = "block";
            } else {
                audio.play();
                bars.style.display = "flex";
                icon.style.display = "none";
            }
            isPlaying = !isPlaying;
        }

        // --- 3. CANVAS GOLD DUST EFFECT (Lightweight) ---
        const canvas = document.getElementById('canvas-container');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        class Particle {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.size = Math.random() * 2 + 0.5;
                this.speedY = Math.random() * 0.5 + 0.2;
                this.color = `rgba(212, 175, 55, ${Math.random() * 0.5})`;
            }
            update() {
                this.y += this.speedY;
                if (this.y > height) this.y = 0;
            }
            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function initParticles() {
            for (let i = 0; i < 50; i++) {
                particles.push(new Particle());
            }
        }
        
        function animateParticles() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animateParticles);
        }

        initParticles();
        animateParticles();
    </script>
</body>
</html>
