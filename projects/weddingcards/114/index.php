<?php
// --- LOAD DATA ---
$json_file = 'wedding_data.json';
if (!file_exists($json_file)) { die("Error: wedding_data.json missing."); }
$data = json_decode(file_get_contents($json_file), true);

$config = $data['config'];
$events = $data['events'];
$lists = $data['lists'];
$contacts = $data['contacts'];

// Calculate first event time for countdown
$target_date = $events[0]['date'] ?? '2026-01-01';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['groom_name']; ?> | The Wedding</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,700;1,400&family=Jost:wght@300;400;600&family=Mrs+Saint+Delafield&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2c3e50;
            --accent: #d4af37; /* Gold */
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: 1px solid rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 20px 50px rgba(0,0,0,0.15);
            --font-main: 'Jost', sans-serif;
            --font-header: 'Bodoni Moda', serif;
            --font-script: 'Mrs Saint Delafield', cursive;
        }

        body {
            background: #0f0c29;
            color: #fff;
            font-family: var(--font-main);
            overflow-x: hidden;
            margin: 0;
        }

        /* --- ANIMATED MESH GRADIENT BACKGROUND --- */
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            background: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                        radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-size: 200% 200%;
            animation: meshFlow 15s ease infinite;
        }
        @keyframes meshFlow { 
            0% { background-position: 0% 50%; } 
            50% { background-position: 100% 50%; } 
            100% { background-position: 0% 50%; } 
        }

        /* --- FLOATING PARTICLES --- */
        .particle {
            position: absolute; width: 5px; height: 5px; background: rgba(212, 175, 55, 0.5);
            border-radius: 50%; animation: floatUp linear infinite; z-index: -1;
        }
        @keyframes floatUp { to { transform: translateY(-100vh) rotate(360deg); } }

        /* --- 3D PRISM CARD --- */
        .prism-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: var(--glass-border);
            border-radius: 30px;
            padding: 4rem 2rem;
            transform-style: preserve-3d;
            transform: perspective(1000px);
            box-shadow: 0 25px 45px rgba(0,0,0,0.2);
            position: relative; overflow: hidden;
            transition: transform 0.1s ease-out; /* Smooth JS tilt */
        }
        
        /* Shimmer Effect */
        .prism-card::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-25deg); transition: 0.5s; pointer-events: none;
        }
        .prism-card:hover::after { left: 150%; transition: 1s; }

        /* 3D Text Pop */
        .translate-z { transform: translateZ(50px); }
        .translate-z-sm { transform: translateZ(30px); }

        /* --- TYPOGRAPHY --- */
        h1, h2, h3 { font-family: var(--font-header); letter-spacing: -1px; }
        .text-script { font-family: var(--font-script); font-size: 3rem; color: var(--accent); line-height: 1; }
        .text-gold-gradient {
            background: linear-gradient(45deg, #c5a059, #f3e5ab, #c5a059);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* --- EVENT ORBS (Modern Timeline) --- */
        .event-orb-container {
            display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; margin-top: 50px;
        }
        .event-orb {
            width: 300px; height: 300px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; padding: 20px;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }
        .event-orb:hover {
            background: rgba(255,255,255,0.08);
            transform: scale(1.1);
            border-color: var(--accent);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.2);
        }
        .event-orb::before {
            content: ''; position: absolute; width: 100%; height: 100%; border-radius: 50%;
            border: 1px dashed rgba(255,255,255,0.1); animation: spinSlow 20s linear infinite;
        }
        @keyframes spinSlow { to { transform: rotate(360deg); } }

        /* --- CIRCULAR COUNTDOWN --- */
        .countdown-wrap { display: flex; gap: 20px; justify-content: center; margin-top: 2rem; }
        .circle-timer {
            width: 80px; height: 80px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.1);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: rgba(0,0,0,0.3);
        }
        .circle-timer span { font-weight: 700; font-size: 1.2rem; color: var(--accent); }
        .circle-timer small { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px; }

        /* --- MUSIC FAB --- */
        .music-fab {
            position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
            background: var(--glass-bg); backdrop-filter: blur(10px);
            border: 1px solid var(--accent); border-radius: 50%;
            color: #fff; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;
            z-index: 1000; cursor: pointer; transition: 0.3s;
        }
        .music-fab:hover { transform: scale(1.1) rotate(15deg); background: var(--accent); color: #000; }

        /* --- RSVP GLASS CARD --- */
        .rsvp-strip {
            background: rgba(0,0,0,0.4); border-top: 1px solid rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1); padding: 40px 0;
        }
    </style>
</head>
<body>

    <div class="mesh-bg"></div>
    <div id="particles"></div>

    <div class="music-fab" onclick="toggleAudio()">
        <i class="fa fa-play" id="music-icon"></i>
    </div>
    <audio id="wedding-audio" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>

    <section class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container text-center perspective-container">
            
            <div class="prism-card mx-auto" id="tilt-card" style="max-width: 800px;">
                
                <div class="translate-z-sm mb-3" data-aos="fade-down">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/23/Bismillah.svg" 
                         width="150" style="filter: invert(1); opacity: 0.8;" alt="Bismillah">
                </div>

                <div class="translate-z-sm text-uppercase small letter-spacing-2 mb-2 opacity-75">
                    <?php echo $config['host_parents']; ?>
                </div>
                
                <div class="translate-z-sm fst-italic text-script mb-4">Cordially Invite You</div>

                <div class="translate-z">
                    <h1 class="display-3 fw-bold text-uppercase mb-0"><?php echo $config['groom_name']; ?></h1>
                    
                    <?php if(!empty($config['bride_name'])): ?>
                        <div class="my-2 fs-2 opacity-50">&</div>
                        <h1 class="display-3 fw-bold text-uppercase text-gold-gradient"><?php echo $config['bride_name']; ?></h1>
                    <?php endif; ?>
                </div>

                <div class="translate-z-sm mt-5 pt-4 border-top border-secondary w-75 mx-auto opacity-75">
                    <p class="mb-0"><?php echo $config['invitation_intro']; ?></p>
                    <p class="text-accent text-uppercase fw-bold mt-2"><?php echo $config['event_type']; ?></p>
                </div>

                <div class="translate-z countdown-wrap" id="countdown"></div>

            </div>

        </div>
    </section>

    <section class="py-5 position-relative">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="zoom-in">
                <span class="text-script text-white display-4">The Timeline</span>
                <h2 class="text-uppercase text-gold-gradient mt-2">Celebration Events</h2>
            </div>

            <div class="event-orb-container">
                <?php foreach($events as $index => $event): ?>
                <div class="event-orb" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="text-accent fs-1 mb-3">
                        <i class="fa <?php echo $event['icon']; ?>"></i>
                    </div>
                    <h3 class="h4 mb-1"><?php echo $event['name']; ?></h3>
                    <p class="small text-uppercase opacity-75 mb-3"><?php echo $event['date']; ?></p>
                    
                    <div class="small fst-italic opacity-50 mb-3 px-4">
                        <?php echo $event['venue_name']; ?><br>
                        <?php echo $event['address']; ?>
                    </div>

                    <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-4" 
                       style="border-color: var(--accent); color: var(--accent);">
                       <i class="fa fa-map-pin me-1"></i> Location
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="rsvp-strip text-center mt-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                
                <div class="col-md-4" data-aos="fade-right">
                    <h4 class="text-accent text-uppercase mb-3">RSVP</h4>
                    <ul class="list-unstyled opacity-75">
                        <?php foreach($lists['rsvp_people'] as $person): ?>
                            <li class="mb-2"><?php echo $person; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col-md-4" data-aos="zoom-in">
                    <div class="p-4 border border-light rounded-circle d-inline-block" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center;">
                         <div class="text-script" style="font-size: 2.5rem;">Duas</div>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-left">
                    <h4 class="text-accent text-uppercase mb-3">Contact</h4>
                    <div class="d-grid gap-2 col-8 mx-auto">
                        <a href="tel:<?php echo $contacts['phone']; ?>" class="btn btn-outline-light">Call Now</a>
                        <a href="https://wa.me/<?php echo $contacts['whatsapp']; ?>" class="btn btn-light text-dark">WhatsApp</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="text-center py-4 opacity-50 small text-uppercase">
        <div class="container">
            <?php echo $data['footer']['ticker_text']; ?>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>

    <script>
        // 1. Initialize Animations
        AOS.init({ duration: 1200, once: false, mirror: true });

        // 2. Initialize 3D Tilt
        VanillaTilt.init(document.querySelector("#tilt-card"), {
            max: 10,
            speed: 400,
            glare: true,
            "max-glare": 0.3,
            scale: 1.02
        });

        // 3. Floating Particles Generator
        const particleContainer = document.getElementById('particles');
        for(let i=0; i<30; i++) {
            let p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.top = '100vh';
            p.style.animationDuration = (Math.random() * 5 + 5) + 's';
            p.style.opacity = Math.random();
            particleContainer.appendChild(p);
        }

        // 4. Countdown Logic
        // Trying to parse standard date formats
        const targetStr = "<?php echo $target_date; ?>";
        const targetDate = new Date(targetStr).getTime();
        
        // If date parsing fails, default to +10 days for demo
        const finalTarget = isNaN(targetDate) ? new Date().getTime() + (10 * 24 * 60 * 60 * 1000) : targetDate;

        setInterval(() => {
            const now = new Date().getTime();
            const diff = finalTarget - now;

            if (diff < 0) {
                document.getElementById('countdown').innerHTML = "<div class='text-accent'>Mubarak!</div>";
                return;
            }

            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            const html = `
                <div class="circle-timer"><span>${d}</span><small>Days</small></div>
                <div class="circle-timer"><span>${h}</span><small>Hrs</small></div>
                <div class="circle-timer"><span>${m}</span><small>Min</small></div>
                <div class="circle-timer"><span>${s}</span><small>Sec</small></div>
            `;
            document.getElementById('countdown').innerHTML = html;
        }, 1000);

        // 5. Audio Toggle
        function toggleAudio() {
            const audio = document.getElementById("wedding-audio");
            const icon = document.getElementById("music-icon");
            if (audio.paused) {
                audio.play();
                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');
            } else {
                audio.pause();
                icon.classList.add('fa-play');
                icon.classList.remove('fa-pause');
            }
        }
    </script>
</body>
</html>