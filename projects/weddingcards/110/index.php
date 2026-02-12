<?php
// Load Data
$json_data = file_get_contents('wedding_data.json');
$data = json_decode($json_data, true);

if (!$data) { die("Error: wedding_data.json not found."); }

$config = $data['config'];
$lists = $data['lists'];
$events = $data['events'];
$contacts = $data['contacts'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['groom_name']; ?> - Wedding Invitation</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --gold: #d4af37;
            --gold-gradient: linear-gradient(45deg, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            --glass-bg: rgba(20, 10, 10, 0.75);
            --glass-border: 1px solid rgba(212, 175, 55, 0.3);
            --neon-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
        }

        body {
            background-color: #0f0505;
            background-image: url('https://www.transparenttextures.com/patterns/black-linen.png');
            font-family: 'Montserrat', sans-serif;
            color: #fff;
            overflow-x: hidden;
        }

        /* --- Glass Card Effect --- */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            transform-style: preserve-3d;
            transition: transform 0.3s;
        }

        /* --- Typography --- */
        .gold-text {
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            color: transparent;
        }
        .font-cursive { font-family: 'Great Vibes', cursive; }
        .font-royal { font-family: 'Cinzel', serif; text-transform: uppercase; letter-spacing: 1px; }

        /* --- Hero Section --- */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
        }
        
        .host-name {
            font-size: 1.8rem;
            color: var(--gold);
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 20px;
        }

        .invitation-text {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #ccc;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* --- Event Cards --- */
        .event-card {
            background: linear-gradient(145deg, rgba(30,10,10,0.9), rgba(10,5,5,0.95));
            border: 1px solid var(--gold);
            border-radius: 15px;
            transition: all 0.4s;
            height: 100%;
        }
        .event-card:hover { transform: translateY(-10px); box-shadow: var(--neon-shadow); }

        /* --- Venue Buttons --- */
        .venue-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid var(--gold);
            color: var(--gold);
            display: inline-flex;
            align-items: center; justify-content: center;
            margin: 0 5px;
            text-decoration: none;
            transition: 0.3s;
        }
        .venue-btn:hover { background: var(--gold); color: #000; transform: scale(1.1); }

        /* --- Ticker --- */
        .ticker-wrap {
            position: fixed; bottom: 0; width: 100%;
            background: #000; border-top: 1px solid var(--gold);
            height: 35px; line-height: 35px; z-index: 999;
        }
        .ticker-content {
            display: inline-block; white-space: nowrap;
            padding-left: 100%; animation: ticker 25s linear infinite;
            color: var(--gold); font-size: 0.9rem;
        }
        @keyframes ticker { 0% { transform: translate3d(0, 0, 0); } 100% { transform: translate3d(-100%, 0, 0); } }

        #petals-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
    </style>
</head>
<body>

    <div id="petals-container"></div>
    
    <div style="position: fixed; bottom: 50px; left: 20px; z-index: 100;">
        <button onclick="toggleMusic()" class="btn btn-outline-warning rounded-circle p-3 shadow-lg" style="border-color: var(--gold); color: var(--gold);">
            <i class="fa fa-music" id="music-icon"></i>
        </button>
    </div>
    
    <audio id="wedding-audio" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>

    <section class="hero-section">
        <div class="container text-center" style="position: relative; z-index: 2;">
            <div class="glass-card mx-auto" style="max-width: 700px;" data-tilt>
                
                <h3 class="font-cursive mb-4" style="color: var(--gold); font-size: 2.2rem;" data-aos="fade-down">
                    <?php echo $config['bismillah']; ?>
                </h3>
                
                <div data-aos="zoom-in">
                    <h2 class="host-name font-royal"><?php echo $config['host_parents']; ?></h2>
                </div>

                <div class="px-4" data-aos="fade-up" data-aos-delay="200">
                    <p class="invitation-text">
                        <?php echo $config['invitation_intro']; ?> <br>
                        <span class="text-white fw-bold border-bottom border-warning pb-1"><?php echo $config['event_type']; ?></span> <br>
                        <?php echo $config['relation_text']; ?>
                    </p>
                </div>

                <h1 class="display-3 font-royal gold-text fw-bold mt-4 mb-4" data-aos="zoom-in-up" style="text-shadow: 0 0 20px rgba(212,175,55,0.3);">
                    <?php echo $config['groom_name']; ?>
                </h1>
                
                <?php if(!empty($config['bride_name'])): ?>
                    <h2 class="font-cursive text-white">Weds</h2>
                    <h1 class="display-4 font-royal gold-text"><?php echo $config['bride_name']; ?></h1>
                <?php endif; ?>

                <div class="mt-5">
                    <i class="fa fa-chevron-down text-warning fa-2x animate__animated animate__bounce infinite"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="position: relative; z-index: 2;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-royal gold-text">Our Wedding Events</h2>
                <hr style="width: 80px; margin: 15px auto; background: var(--gold); height: 2px; opacity: 1;">
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach($events as $index => $event): ?>
                <div class="col-lg-4 col-md-6" data-aos="flip-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="event-card p-4 text-center">
                        <i class="fa <?php echo $event['icon']; ?> gold-text fs-1 mb-3"></i>
                        <h3 class="font-royal text-white mb-2"><?php echo $event['name']; ?></h3>
                        <p class="text-warning fw-bold mb-4"><?php echo $event['date']; ?></p>
                        
                        <div class="text-start ms-3 mb-4 border-start border-warning ps-3">
                            <?php foreach($event['times'] as $time): ?>
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark me-2"><?php echo $time['time']; ?></span>
                                    <span class="text-white-50 small text-uppercase"><?php echo $time['label']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="border-top border-secondary pt-3">
                            <p class="small text-white mb-1 fw-bold"><?php echo $event['venue_name']; ?></p>
                            <p class="small text-white-50 mb-3"><?php echo $event['address']; ?></p>
                            
                            <div>
                                <a href="<?php echo $event['map_link']; ?>" target="_blank" class="venue-btn"><i class="fa fa-location-dot"></i></a>
                                <a href="tel:<?php echo $contacts['phone']; ?>" class="venue-btn"><i class="fa fa-phone"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5" style="position: relative; z-index: 2;">
        <div class="container">
            <div class="glass-card mx-auto text-center" style="max-width: 600px;" data-aos="zoom-in">
                
                <h2 class="font-royal gold-text mb-4">RSVP</h2>
                
                <div class="mb-5">
                    <div class="row justify-content-center">
                        <?php foreach($lists['rsvp_people'] as $person): ?>
                            <div class="col-6 mb-2">
                                <span class="text-white fw-light border-bottom border-secondary pb-1 d-block"><?php echo $person; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-5">
                    <h5 class="text-warning font-royal mb-3">Well Wishers</h5>
                    <?php foreach($lists['well_wishers'] as $wisher): ?>
                        <span class="d-block text-white fs-5 font-cursive"><?php echo $wisher; ?></span>
                    <?php endforeach; ?>
                </div>

                <h4 class="text-white mb-4">Contact for Details</h4>
                <div class="d-flex justify-content-center gap-3">
                    <a href="tel:<?php echo $contacts['phone']; ?>" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold" style="border-color: var(--gold); color: var(--gold);">
                        <i class="fa fa-phone me-2"></i> Call Now
                    </a>
                    <a href="https://wa.me/<?php echo $contacts['whatsapp']; ?>" target="_blank" class="btn rounded-pill px-4 py-2 fw-bold" style="background: #25D366; color: white; border: none;">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </section>

    <div class="ticker-wrap">
        <div class="ticker-content">
            <?php echo $data['footer']['ticker_text']; ?>     |     <?php echo $data['footer']['ticker_text']; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
    
    <script>
        // Init Animation
        AOS.init();
        
        // Music Logic
        function toggleMusic() {
            var audio = document.getElementById("wedding-audio");
            var icon = document.getElementById("music-icon");
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
        
        // Petals Logic
        function createPetal() {
            const petal = document.createElement('div');
            petal.innerHTML = '✿';
            petal.style.position = 'absolute';
            petal.style.color = '#d4af37'; // Gold
            petal.style.left = Math.random() * 100 + 'vw';
            petal.style.fontSize = Math.random() * 20 + 10 + 'px';
            petal.style.opacity = Math.random() * 0.6 + 0.2;
            petal.style.animation = 'fall ' + (Math.random() * 3 + 2) + 's linear';
            document.getElementById('petals-container').appendChild(petal);
            setTimeout(() => petal.remove(), 5000);
        }
        
        // Add Keyframes for Petals
        const styleSheet = document.createElement("style");
        styleSheet.innerText = `@keyframes fall { from { top: -10vh; transform: rotate(0deg); } to { top: 100vh; transform: rotate(720deg); } }`;
        document.head.appendChild(styleSheet);
        
        // Start Petals
        setInterval(createPetal, 400);
    </script>
</body>
</html>
