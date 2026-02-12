<?php
// --- DATA LOAD ---
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
    <title><?php echo $config['groom_name']; ?> | Shadi Mubarak</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Nastaliq+Urdu:wght@400;700&family=Outfit:wght@300;400;600&family=Reem+Kufi:wght@400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            /* Pakistani Wedding Palette */
            --royal-maroon: #800020;
            --deep-emerald: #043927;
            --bright-gold: #FFD700;
            --antique-gold: #c5a059;
            --cream-bg: #fffbf0;
            --pattern-opacity: 0.05;
        }

        body {
            background-color: var(--cream-bg);
            color: #333;
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        /* --- ISLAMIC GEOMETRIC BACKGROUND --- */
        .islamic-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23800020' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: -1;
        }

        /* --- TYPOGRAPHY --- */
        .font-urdu { font-family: 'Amiri', serif; font-size: 1.4rem; }
        .font-kufi { font-family: 'Reem Kufi', sans-serif; text-transform: uppercase; letter-spacing: 2px; }
        .font-nastaliq { font-family: 'Noto Nastaliq Urdu', serif; line-height: 2; }
        
        .text-maroon { color: var(--royal-maroon); }
        .text-emerald { color: var(--deep-emerald); }
        .text-gold { color: var(--antique-gold); }

        /* --- MUGHAL ARCH FRAME --- */
        .mughal-frame {
            border: 2px solid var(--antique-gold);
            border-radius: 15px;
            position: relative;
            padding: 10px;
            margin: 20px auto;
            max-width: 900px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(128, 0, 32, 0.15);
        }
        .mughal-frame::before {
            content: ''; position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px;
            border: 1px dashed var(--royal-maroon);
            border-radius: 10px; pointer-events: none;
        }

        /* --- ARCH CARDS (MEHRAB) --- */
        .arch-card {
            background: linear-gradient(180deg, #fff 0%, #fffbf0 100%);
            border-radius: 200px 200px 10px 10px; /* The Dome Shape */
            border: 1px solid var(--antique-gold);
            padding: 4rem 2rem 2rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .arch-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(128, 0, 32, 0.2);
            border-color: var(--royal-maroon);
        }
        /* Decorative Hanger inside arch */
        .lantern-hanger {
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 2px; height: 40px; background: var(--antique-gold);
        }
        .lantern-icon {
            position: absolute; top: 40px; left: 50%; transform: translateX(-50%);
            color: var(--royal-maroon); font-size: 1.5rem;
        }

        /* --- MANDALA SPIN ANIMATION --- */
        .mandala-bg {
            position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='50' r='40' stroke='%23c5a059' stroke-width='1' fill='none'/%3E%3Cpath d='M50 10 L60 40 L90 50 L60 60 L50 90 L40 60 L10 50 L40 40 Z' fill='%23c5a059' opacity='0.2'/%3E%3C/svg%3E");
            background-size: contain;
            animation: spin 60s linear infinite;
            z-index: 0; opacity: 0.5;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* --- MUSIC PLAYER (TRADITIONAL STYLE) --- */
        .tabla-btn {
            position: fixed; bottom: 80px; left: 20px; z-index: 1000;
            width: 55px; height: 55px;
            background: var(--royal-maroon); border: 2px solid var(--bright-gold);
            border-radius: 50%; color: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            cursor: pointer;
        }
        .tabla-btn i { animation: beat 1s infinite; }
        @keyframes beat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        /* --- FLORAL DIVIDERS --- */
        .divider-flower {
            text-align: center; margin: 30px 0; font-size: 1.5rem; color: var(--royal-maroon);
        }
        .divider-flower::before, .divider-flower::after {
            content: ''; display: inline-block; width: 50px; height: 1px;
            background: var(--antique-gold); vertical-align: middle; margin: 0 10px;
        }
    </style>
</head>
<body>

    <div class="islamic-pattern"></div>

    <button class="tabla-btn" onclick="toggleAudio()">
        <i class="fa fa-music"></i>
    </button>
    <audio id="wedding-audio" loop>
        <source src="<?php echo $config['bg_music']; ?>" type="audio/mpeg">
    </audio>

    <section class="min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="container text-center">
            
            <div class="mughal-frame p-4 p-md-5 position-relative" data-aos="zoom-in" data-aos-duration="1200">
                <div class="mandala-bg"></div>

                <div class="mb-4">
                    <h2 class="font-urdu text-emerald display-6" style="line-height: 1.6;">
                        ﷽
                    </h2>
                    <p class="small text-muted text-uppercase tracking-widest mt-2">In the name of Allah, the Most Gracious</p>
                </div>

                <h4 class="font-kufi text-maroon mt-4"><?php echo $config['host_parents']; ?></h4>
                <div class="divider-flower"><i class="fas fa-leaf"></i></div>

                <p class="lead text-dark fst-italic px-md-5">
                    <?php echo $config['invitation_intro']; ?> <br>
                    <strong><?php echo $config['event_type']; ?></strong> <br>
                    <?php echo $config['relation_text']; ?>
                </p>

                <div class="py-4">
                    <h1 class="font-urdu text-maroon display-3 fw-bold mb-0">
                        <?php echo $config['groom_name']; ?>
                    </h1>
                    <?php if(!empty($config['bride_name'])): ?>
                        <div class="font-kufi text-gold my-2 fs-4">weds</div>
                        <h1 class="font-urdu text-emerald display-3 fw-bold">
                            <?php echo $config['bride_name']; ?>
                        </h1>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <span class="badge bg-danger rounded-pill px-4 py-2 text-uppercase" style="letter-spacing: 2px;">Save The Dates</span>
                </div>
            </div>

        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="font-kufi text-maroon fs-1">Wedding Festivities</h2>
                <p class="text-muted">Join us in our joy</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach($events as $index => $event): 
                    // Dynamic Colors for variety
                    $border_color = ($index % 2 == 0) ? 'var(--royal-maroon)' : 'var(--deep-emerald)';
                    $icon_color = ($index % 2 == 0) ? 'text-danger' : 'text-success';
                ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $index * 150; ?>">
                    <div class="arch-card" style="border-bottom: 5px solid <?php echo $border_color; ?>;">
                        
                        <div class="lantern-hanger"></div>
                        <i class="fas fa-kaaba lantern-icon"></i>

                        <div class="mt-4">
                            <h3 class="font-kufi text-dark fw-bold mb-2"><?php echo $event['name']; ?></h3>
                            <div class="divider-flower my-2" style="font-size: 1rem;"><i class="fas fa-star text-gold"></i></div>
                            <h5 class="text-maroon font-urdu fw-bold"><?php echo $event['date']; ?></h5>
                        </div>

                        <div class="bg-light p-3 rounded mt-4 mx-2 border border-warning">
                            <?php foreach($event['times'] as $time): ?>
                                <div class="d-flex justify-content-between border-bottom border-white py-1">
                                    <span class="text-muted small text-uppercase"><?php echo $time['label']; ?></span>
                                    <span class="fw-bold text-dark"><?php echo $time['time']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4">
                            <i class="fas fa-map-marker-alt text-gold fs-4 mb-2"></i>
                            <p class="mb-1 fw-bold"><?php echo $event['venue_name']; ?></p>
                            <small class="text-muted"><?php echo $event['address']; ?></small>
                            <br>
                            <a href="<?php echo $event['map_link']; ?>" class="btn btn-outline-danger btn-sm mt-3 rounded-pill px-4">
                                Navigate
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white position-relative" style="box-shadow: 0 0 20px rgba(0,0,0,0.05);">
        <div class="container py-4">
            <div class="row align-items-center">
                
                <div class="col-md-5 text-center text-md-end border-end-md border-warning" data-aos="fade-right">
                    <h3 class="font-kufi text-emerald mb-4">Eagerly Awaiting</h3>
                    <ul class="list-unstyled">
                        <?php foreach($lists['rsvp_people'] as $person): ?>
                            <li class="font-urdu fs-5 mb-2 text-dark"><?php echo $person; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="col-md-2 text-center my-4 my-md-0">
                    <img src="https://cdn-icons-png.flaticon.com/512/3655/3655566.png" width="80" alt="Decoration">
                </div>

                <div class="col-md-5 text-center text-md-start" data-aos="fade-left">
                    <h3 class="font-kufi text-maroon mb-4">Duas & Love</h3>
                    <?php foreach($lists['well_wishers'] as $wisher): ?>
                        <h4 class="font-nastaliq text-gold mb-2"><?php echo $wisher; ?></h4>
                    <?php endforeach; ?>
                    
                    <div class="mt-4">
                        <a href="https://wa.me/<?php echo $contacts['whatsapp']; ?>" class="btn btn-success rounded-0 px-4">
                            <i class="fab fa-whatsapp me-2"></i> Send Duas
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="py-1" style="background: var(--royal-maroon); color: var(--bright-gold);">
        <marquee scrollamount="6" class="py-2 font-kufi" style="font-size: 0.9rem;">
            <i class="fas fa-star mx-2"></i> <?php echo $data['footer']['ticker_text']; ?> 
            <i class="fas fa-moon mx-2"></i> <?php echo $data['footer']['ticker_text']; ?> 
            <i class="fas fa-star mx-2"></i> <?php echo $data['footer']['ticker_text']; ?>
        </marquee>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        AOS.init({ duration: 1000 });

        function toggleAudio() {
            var audio = document.getElementById("wedding-audio");
            if (audio.paused) audio.play();
            else audio.pause();
        }

        // Falling Rose Petals (CSS/JS Hybrid)
        function createPetal() {
            const petal = document.createElement('div');
            petal.innerHTML = '❀';
            petal.style.position = 'fixed';
            petal.style.color = Math.random() > 0.5 ? '#800020' : '#FFD700';
            petal.style.left = Math.random() * 100 + 'vw';
            petal.style.top = '-20px';
            petal.style.fontSize = Math.random() * 20 + 10 + 'px';
            petal.style.opacity = Math.random() * 0.5 + 0.3;
            petal.style.zIndex = '9999';
            petal.style.pointerEvents = 'none';
            petal.style.transition = 'top 5s linear, transform 5s linear';
            
            document.body.appendChild(petal);

            setTimeout(() => {
                petal.style.top = '100vh';
                petal.style.transform = `rotate(${Math.random() * 360}deg)`;
            }, 100);

            setTimeout(() => petal.remove(), 5000);
        }
        setInterval(createPetal, 800);
    </script>
</body>
</html>