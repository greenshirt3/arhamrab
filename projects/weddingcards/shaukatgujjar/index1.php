<?php
// --- DATA LOADING ---
$json_file = 'wedding_data.json';
if (!file_exists($json_file)) { die("Error: wedding_data.json missing."); }
$data = json_decode(file_get_contents($json_file), true);
if (!$data) { die("Error: JSON is invalid."); }

$config = $data['config'];
$events = $data['events'];
$lists = $data['lists'];
$contacts = $data['contacts'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding of <?php echo $config['bride_name']; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cinzel+Decorative:wght@400;700&family=Pinyon+Script&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* --- THEME VARIABLES --- */
        :root {
            --primary-cream: #fdfbf7;
            --soft-pink: #fce4ec;
            --soft-mint: #e0f2f1;
            --gold-accent: #d4af37;
            --gold-shimmer: linear-gradient(45deg, #d4af37, #f9e79f, #d4af37);
            --pearl-white: #f8f9fa;
            --text-dark: #4a4a4a;
            --glass-bg: rgba(255, 255, 255, 0.6);
            --glass-border: 1px solid rgba(212, 175, 55, 0.3);
            --shadow-soft: 0 15px 40px rgba(212, 175, 55, 0.15);
        }

        body {
            background-color: var(--primary-cream);
            font-family: 'Jost', sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M50 0C22.4 0 0 22.4 0 50s22.4 50 50 50 50-22.4 50-50S77.6 0 50 0zm0 90C27.9 90 10 72.1 10 50S27.9 10 50 10s40 17.9 40 40-17.9 40-40 40z' fill='%23d4af37' fill-opacity='0.05'/%3E%3C/svg%3E");
        }

        /* --- TYPOGRAPHY --- */
        .font-arabic { font-family: 'Amiri', serif; }
        .font-royal { font-family: 'Cinzel Decorative', cursive; }
        .font-script { font-family: 'Pinyon Script', cursive; }
        
        .text-gold {
            background: var(--gold-shimmer);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* --- FLOATING ELEMENTS --- */
        #particles-js {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1; pointer-events: none;
        }

        /* --- MAIN FRAME --- */
        .royal-frame {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 2px solid var(--gold-accent);
            border-radius: 30px;
            padding: 4rem 2rem;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            transition: transform 0.3s ease-out;
        }
        .royal-frame::before, .royal-frame::after {
            content: ''; position: absolute; width: 150px; height: 150px;
            background-size: contain; background-repeat: no-repeat; opacity: 0.6;
        }
        .royal-frame::before {
            top: -20px; left: -20px;
            background-image: url('https://cdn-icons-png.flaticon.com/512/608/608958.png'); /* Floral Corner */
            transform: rotate(-45deg);
        }
        .royal-frame::after {
            bottom: -20px; right: -20px;
            background-image: url('https://cdn-icons-png.flaticon.com/512/608/608958.png');
            transform: rotate(135deg);
        }

        /* --- EVENT CARDS --- */
        .event-card {
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--primary-cream) 100%);
            border: var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.4s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
        }
        .event-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-soft);
            border-color: var(--gold-accent);
        }
        .event-icon-box {
            width: 80px; height: 80px;
            background: var(--primary-cream);
            border: 2px solid var(--gold-accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
        }

        /* --- TIMELINE LIST --- */
        .timeline-list { list-style: none; padding: 0; text-align: left; display: inline-block; }
        .timeline-list li {
            padding: 10px 0;
            border-bottom: 1px dashed rgba(212, 175, 55, 0.3);
            display: flex; align-items: center;
        }
        .timeline-list li:last-child { border-bottom: none; }
        .timeline-time {
            font-family: 'Cinzel Decorative', cursive;
            font-weight: bold; color: var(--gold-accent);
            min-width: 100px;
        }

        /* --- MUSIC PLAYER --- */
        .music-fab {
            position: fixed; bottom: 70px; left: 20px; z-index: 100;
            width: 50px; height: 50px; border-radius: 50%;
            background: var(--primary-cream); border: 2px solid var(--gold-accent);
            color: var(--gold-accent); display: flex;
            align-items: center; justify-content: center; cursor: pointer;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); } 70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); } 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); } }

        /* --- TICKER --- */
        .ticker-bar {
            position: fixed; bottom: 0; width: 100%;
            background: var(--gold-accent); color: var(--primary-cream);
            font-family: 'Jost', sans-serif; font-weight: 500;
            padding: 8px 0; z-index: 99;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <audio id="bg-music" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>
    <div class="music-fab" onclick="toggleAudio()">
        <i class="fa fa-music" id="play-icon"></i>
    </div>

    <section class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container text-center">
            
            <div class="royal-frame mx-auto" style="max-width: 900px;" data-tilt data-tilt-max="5">
                <div data-aos="fade-down">
                    <h1 class="font-arabic text-gold display-4 mb-0"><?php echo $config['bismillah']; ?></h1>
                    <p class="font-script fs-5 text-muted mt-2"><?php echo $config['bismillah_text']; ?></p>
                </div>

                <div class="my-5" data-aos="zoom-in">
                    <h3 class="font-royal text-dark"><?php echo $config['host_parents']; ?></h3>
                    <div class="d-flex justify-content-center my-3">
                        <img src="https://cdn-icons-png.flaticon.com/512/4359/4359941.png" width="40" alt="ornament" style="opacity: 0.7;">
                    </div>
                    <p class="lead font-jost px-md-5">
                        <?php echo $config['invitation_intro']; ?> <br>
                        <span class="font-script fs-3 text-gold"><?php echo $config['event_type']; ?></span>
                    </p>
                </div>

                <div class="py-4" data-aos="fade-up">
                    <h1 class="font-royal display-3 text-gold fw-bold mb-0">
                        <?php echo $config['bride_name']; ?>
                    </h1>
                </div>

                <div class="mt-5 animate__animated animate__bounce infinite">
                    <i class="fa fa-chevron-down text-gold fs-4"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-royal text-gold display-5">The Wedding Ceremony</h2>
                <p class="text-muted font-script fs-4">A celebration of love and blessings</p>
            </div>

            <div class="row justify-content-center">
                <?php foreach($events as $event): ?>
                <div class="col-lg-8" data-aos="flip-up">
                    <div class="event-card p-4 p-md-5">
                        <div class="event-icon-box">
                            <i class="fa <?php echo $event['icon']; ?> fs-2 text-gold"></i>
                        </div>

                        <span class="badge bg-warning text-dark rounded-pill px-3 mb-3 font-jost">
                            <?php echo $event['islamic_date']; ?>
                        </span>
                        <h2 class="font-royal text-dark mb-2"><?php echo $event['name']; ?></h2>
                        <h4 class="text-gold font-royal mb-5"><?php echo $event['date']; ?></h4>

                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0 border-end-md border-gold">
                                <h5 class="font-royal mb-3">Program Schedule</h5>
                                <ul class="timeline-list">
                                    <?php foreach($event['times'] as $time): ?>
                                        <li>
                                            <span class="timeline-time"><?php echo $time['time']; ?></span>
                                            <span class="font-jost"><?php echo $time['label']; ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="mb-4">
                                    <i class="fa fa-map-marker-alt text-gold fs-3 mb-2"></i>
                                    <h5 class="font-royal mb-1"><?php echo $event['venue_name']; ?></h5>
                                    <p class="text-muted mb-0"><?php echo $event['address']; ?></p>
                                </div>
                                <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-outline-warning rounded-pill px-4 align-self-center">
                                    View Map
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5">
        <div class="container">
            <div class="royal-frame mx-auto" style="max-width: 900px; background: linear-gradient(to right, var(--soft-mint), var(--primary-cream));" data-aos="zoom-in">
                <div class="row g-4">
                    
                    <div class="col-md-4 text-center text-md-start" data-aos="fade-right">
                        <h4 class="font-royal text-gold mb-3">Looking Forward</h4>
                        <ul class="list-unstyled font-jost">
                            <?php foreach($lists['looking_forward'] as $person): ?>
                                <li class="mb-2"><?php echo $person; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-md-4 text-center border-start-md border-end-md border-gold" data-aos="fade-up">
                        <h4 class="font-royal text-gold mb-3">R.S.V.P</h4>
                        <ul class="list-unstyled font-jost mb-4">
                            <?php foreach($lists['rsvp_people'] as $person): ?>
                                <li class="mb-2"><?php echo $person; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="text-center">
                             <img src="https://cdn-icons-png.flaticon.com/512/1057/1057241.png" width="60" alt="rings" style="opacity: 0.6;">
                        </div>
                    </div>

                    <div class="col-md-4 text-center text-md-end" data-aos="fade-left">
                        <h4 class="font-royal text-gold mb-3">Home Address & Contact</h4>
                        <p class="font-jost mb-4">
                            <i class="fa fa-home text-gold me-2"></i> <?php echo $contacts['home_address']; ?>
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end font-jost">
                            <a href="tel:<?php echo $contacts['phone_1']; ?>" class="btn btn-sm btn-outline-dark rounded-pill">
                                <i class="fa fa-phone"></i> <?php echo $contacts['phone_1']; ?>
                            </a>
                            <a href="tel:<?php echo $contacts['phone_2']; ?>" class="btn btn-sm btn-outline-dark rounded-pill">
                                <i class="fa fa-phone"></i> <?php echo $contacts['phone_2']; ?>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="ticker-bar">
        <marquee scrollamount="6">
            <i class="fa fa-heart mx-2"></i> <?php echo $data['footer']['ticker_text']; ?> 
            <i class="fa fa-star-and-crescent mx-2"></i> <?php echo $data['footer']['ticker_text']; ?>
        </marquee>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <script>
        // 1. Init Animations
        AOS.init({ duration: 1000, once: true });

        // 2. Init 3D Tilt
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
            max: 5, speed: 400, glare: true, "max-glare": 0.2, scale: 1.01
        });

        // 3. Music Player Toggle
        function toggleAudio() {
            const audio = document.getElementById("bg-music");
            const icon = document.getElementById("play-icon");
            if (audio.paused) {
                audio.play();
                icon.classList.remove('fa-music'); icon.classList.add('fa-pause');
            } else {
                audio.pause();
                icon.classList.add('fa-music'); icon.classList.remove('fa-pause');
            }
        }

        // 4. Floating Particles (Pearl/Light effect)
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 40, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#d4af37" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3, "random": true },
                "size": { "value": 4, "random": true },
                "line_linked": { "enable": false },
                "move": { "enable": true, "speed": 1, "direction": "top", "random": true, "straight": false, "out_mode": "out" }
            },
            "interactivity": { "events": { "onhover": { "enable": false } } },
            "retina_detect": true
        });
    </script>
</body>
</html>