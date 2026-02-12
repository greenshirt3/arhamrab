<?php
// --- CONFIGURATION & DATA FETCHING ---
$json_file = 'wedding_data.json';
if (!file_exists($json_file)) { die("Error: wedding_data.json missing."); }
$data = json_decode(file_get_contents($json_file), true);
if (!$data) { die("Error: JSON is invalid."); }

$config = $data['config'];
$events = $data['events'];
$lists = $data['lists'];
$contacts = $data['contacts'];

// Helper: Get first event date for Countdown
$first_event_date = $events[0]['date'] ?? '2026-01-01';
// Try to parse date for JS (Assuming format "Day, DD Mon YYYY")
$js_date = date("M d, Y 00:00:00", strtotime($first_event_date));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['groom_name']; ?> | Wedding Celebration</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Great+Vibes&family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- MODERN LUXURY VARIABLES --- */
        :root {
            --bg-deep: #0f172a;       /* Deep Slate Blue */
            --bg-darker: #020617;     /* Almost Black */
            --gold-accent: #fcd34d;   /* Bright Amber Gold */
            --gold-muted: #d4af37;    /* Classic Gold */
            --text-light: #f8fafc;    /* Off-white for reading */
            --glass: rgba(30, 41, 59, 0.7);
            --border: 1px solid rgba(252, 211, 77, 0.2);
            --shadow: 0 10px 30px -10px rgba(0,0,0,0.8);
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-light);
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
            line-height: 1.7;
        }

        /* --- BACKGROUND FX --- */
        .bg-fixed {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at top right, #1e293b 0%, #020617 100%);
            z-index: -2;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: 0.4; animation: float 10s infinite alternate;
        }
        .orb-1 { top: -10%; left: -10%; width: 50vw; height: 50vw; background: #3b0764; }
        .orb-2 { bottom: -10%; right: -10%; width: 40vw; height: 40vw; background: #1e1b4b; }
        @keyframes float { 0% { transform: translate(0,0); } 100% { transform: translate(30px, 50px); } }

        /* --- TYPOGRAPHY --- */
        .font-display { font-family: 'Cormorant Garamond', serif; letter-spacing: 1px; }
        .font-hand { font-family: 'Great Vibes', cursive; color: var(--gold-accent); }
        .text-gold { color: var(--gold-accent); }
        .text-gradient {
            background: linear-gradient(to right, #fcd34d, #f59e0b);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* --- CARDS & GLASSMORPHISM --- */
        .modern-card {
            background: var(--glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -5px rgba(252, 211, 77, 0.15);
        }

        /* --- TIMELINE COMPONENT --- */
        .timeline-section { position: relative; padding: 40px 0; }
        .timeline-line {
            position: absolute; left: 50%; top: 0; bottom: 0; width: 2px;
            background: linear-gradient(to bottom, transparent, var(--gold-muted), transparent);
            transform: translateX(-50%);
        }
        
        /* Mobile adjustment for timeline */
        @media (max-width: 768px) {
            .timeline-line { left: 20px; }
            .timeline-row { flex-direction: column !important; }
            .timeline-content { width: 100% !important; padding-left: 50px !important; text-align: left !important; }
            .timeline-dot { left: 20px !important; }
        }

        .timeline-row {
            display: flex; justify-content: center; align-items: center; margin-bottom: 60px; position: relative;
        }
        .timeline-content { width: 45%; }
        .text-end-md { text-align: right; } /* Desktop Right align */
        .text-start-md { text-align: left; } /* Desktop Left align */
        
        /* Reset for mobile in media query above */
        .timeline-dot {
            position: absolute; left: 50%; width: 20px; height: 20px;
            background: var(--gold-accent); border-radius: 50%;
            transform: translateX(-50%); box-shadow: 0 0 15px var(--gold-accent);
            z-index: 2;
        }

        /* --- COUNTDOWN --- */
        .countdown-box {
            display: inline-block; min-width: 70px;
            background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);
            padding: 10px; border-radius: 10px; margin: 0 5px;
        }
        .countdown-number { font-size: 1.5rem; font-weight: 700; color: var(--gold-accent); line-height: 1; }
        .countdown-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; }

        /* --- MUSIC BUTTON --- */
        .music-btn {
            position: fixed; bottom: 80px; right: 20px; z-index: 100;
            width: 50px; height: 50px; border-radius: 50%;
            background: var(--gold-accent); color: var(--bg-darker);
            border: none; box-shadow: 0 0 20px rgba(252, 211, 77, 0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; cursor: pointer; transition: 0.3s;
        }
        .music-btn:hover { transform: scale(1.1) rotate(180deg); }
        .music-ripple {
            position: absolute; width: 100%; height: 100%; border-radius: 50%;
            border: 2px solid var(--gold-accent); animation: ripple 2s infinite;
        }
        @keyframes ripple { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(2); opacity: 0; } }

        /* --- TICKER --- */
        .marquee-container {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: var(--gold-muted); color: var(--bg-darker);
            font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
            padding: 8px 0; z-index: 1000; overflow: hidden;
        }
    </style>
</head>
<body>

    <div class="bg-fixed">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <button class="music-btn" onclick="toggleMusic()">
        <div class="music-ripple"></div>
        <i class="fa fa-music" id="music-icon"></i>
    </button>
    <audio id="wedding-audio" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>

    <section class="min-vh-100 d-flex flex-column justify-content-center align-items-center text-center px-3 position-relative">
        
        <div data-aos="zoom-out" data-aos-duration="1500">
            <h3 class="font-hand display-4 mb-3"><?php echo $config['bismillah']; ?></h3>
            <p class="text-uppercase small tracking-widest text-light opacity-75 mb-4">
                <?php echo $config['host_parents']; ?>
            </p>
        </div>

        <div class="modern-card p-5 mx-auto" style="max-width: 800px; width: 100%;" data-tilt>
            <p class="mb-3 font-display fst-italic fs-5">Invite you to celebrate the union of</p>
            
            <h1 class="display-2 font-display fw-bold text-gradient mb-2">
                <?php echo $config['groom_name']; ?>
            </h1>
            
            <?php if(!empty($config['bride_name'])): ?>
                <div class="font-hand fs-1 my-1 text-white">&</div>
                <h1 class="display-2 font-display fw-bold text-gradient mt-2">
                    <?php echo $config['bride_name']; ?>
                </h1>
            <?php endif; ?>

            <div class="mt-5 pt-4 border-top border-secondary">
                <div id="countdown" class="d-flex justify-content-center flex-wrap gap-2">
                    </div>
                <p class="mt-3 small text-uppercase spacing-2 mb-0">Until the Celebration Begins</p>
            </div>
        </div>

        <div class="position-absolute bottom-0 pb-5 animate__animated animate__bounce infinite">
            <i class="fa fa-chevron-down text-gold fs-4"></i>
        </div>
    </section>

    <section class="container py-5 timeline-section">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="font-display display-4 text-gold">The Ceremony</h2>
            <p class="text-muted">We would be honored by your presence</p>
        </div>

        <div class="timeline-line"></div>

        <?php foreach($events as $index => $event): 
            $is_even = ($index % 2 == 0);
            $align_class = $is_even ? 'text-end-md' : 'text-start-md';
            $order_class = $is_even ? '' : 'flex-row-reverse';
            $fade_dir = $is_even ? 'fade-right' : 'fade-left';
        ?>
        <div class="timeline-row <?php echo $order_class; ?>">
            <div class="timeline-content <?php echo $align_class; ?>" data-aos="<?php echo $fade_dir; ?>">
                <div class="modern-card p-4">
                    <div class="d-inline-block p-2 rounded-circle border border-warning mb-3">
                        <i class="fa <?php echo $event['icon']; ?> fs-3 text-gold"></i>
                    </div>
                    <h3 class="font-display fw-bold mb-1"><?php echo $event['name']; ?></h3>
                    <h5 class="text-gold mb-3"><?php echo $event['date']; ?></h5>
                    
                    <ul class="list-unstyled mb-3">
                        <?php foreach($event['times'] as $time): ?>
                        <li class="mb-1">
                            <span class="badge bg-warning text-dark me-2"><?php echo $time['time']; ?></span>
                            <span class="small opacity-75"><?php echo $time['label']; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="border-top border-secondary pt-3 mt-3">
                        <p class="fw-bold mb-0"><?php echo $event['venue_name']; ?></p>
                        <p class="small opacity-75 mb-3"><?php echo $event['address']; ?></p>
                        <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill px-4">
                            See Location
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="timeline-dot"></div>
            
            <div class="timeline-content d-none d-md-block"></div>
        </div>
        <?php endforeach; ?>

    </section>

    <section class="py-5" style="background: rgba(0,0,0,0.3);">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-md-6" data-aos="flip-left">
                    <div class="modern-card h-100 text-center">
                        <h3 class="font-display text-gold mb-4">RSVP</h3>
                        <div class="row">
                            <?php foreach($lists['rsvp_people'] as $person): ?>
                            <div class="col-6 mb-3">
                                <div class="py-2 border-bottom border-secondary opacity-75">
                                    <?php echo $person; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6" data-aos="flip-right">
                    <div class="modern-card h-100 text-center d-flex flex-column justify-content-center">
                        <h3 class="font-display text-gold mb-2">Well Wishers</h3>
                        <?php foreach($lists['well_wishers'] as $wisher): ?>
                            <h4 class="font-hand text-white my-1"><?php echo $wisher; ?></h4>
                        <?php endforeach; ?>
                        
                        <div class="mt-4 d-flex gap-3 justify-content-center">
                            <a href="tel:<?php echo $contacts['phone']; ?>" class="btn btn-light rounded-pill px-4 fw-bold">
                                <i class="fa fa-phone me-2"></i> Call
                            </a>
                            <a href="https://wa.me/<?php echo $contacts['whatsapp']; ?>" class="btn btn-success rounded-pill px-4 fw-bold">
                                <i class="fab fa-whatsapp me-2"></i> Chat
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div style="height: 50px;"></div> <div class="marquee-container">
        <marquee scrollamount="8">
            <?php echo $data['footer']['ticker_text']; ?> &nbsp;&nbsp;&diams;&nbsp;&nbsp; 
            <?php echo $data['footer']['ticker_text']; ?> &nbsp;&nbsp;&diams;&nbsp;&nbsp; 
            <?php echo $data['footer']['ticker_text']; ?>
        </marquee>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>

    <script>
        // 1. Init Animations
        AOS.init({ offset: 100, duration: 1000, once: false });

        // 2. Countdown Logic
        const targetDate = new Date("<?php echo $js_date; ?>").getTime();
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown").innerHTML = "<h3 class='text-gold'>Celebration Started!</h3>";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const template = (val, label) => `
                <div class="countdown-box">
                    <div class="countdown-number">${val}</div>
                    <div class="countdown-label">${label}</div>
                </div>`;

            document.getElementById("countdown").innerHTML = 
                template(days, "Days") + template(hours, "Hours") + 
                template(minutes, "Mins") + template(seconds, "Secs");
        }, 1000);

        // 3. Music Logic
        function toggleMusic() {
            const audio = document.getElementById('wedding-audio');
            const icon = document.getElementById('music-icon');
            if (audio.paused) {
                audio.play();
                icon.classList.remove('fa-music');
                icon.classList.add('fa-pause');
            } else {
                audio.pause();
                icon.classList.add('fa-music');
                icon.classList.remove('fa-pause');
            }
        }
    </script>
</body>
</html>
