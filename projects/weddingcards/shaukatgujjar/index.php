<?php
$json_file = 'wedding_data.json';
if (!file_exists($json_file)) { die("Error: wedding_data.json missing."); }
$data = json_decode(file_get_contents($json_file), true);

$config = $data['config'];
$events = $data['events'];
$lists = $data['lists'];
$contacts = $data['contacts'];
$footer = $data['footer'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Invitation | <?php echo strip_tags($config['host_parents']); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Jost:wght@300;400;600;700&family=Noto+Nastaliq+Urdu:wght@400;700&family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --gold: #FFD700;
            --gold-shadow: #b8860b;
        }

        body {
            font-family: 'Jost', sans-serif;
            color: #fff;
            overflow-x: hidden;
            background: #000;
            padding-top: 40px; 
            padding-bottom: 40px;
        }

        /* --- VIBRANT ANIMATED BACKGROUND --- */
        .live-gradient-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            background: linear-gradient(-45deg, #2c0b0b, #0b1a2c, #04291c, #2b0b30);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG { 
            0% { background-position: 0% 50%; } 
            50% { background-position: 100% 50%; } 
            100% { background-position: 0% 50%; } 
        }

        /* --- TYPOGRAPHY --- */
        .font-royal { font-family: 'Cinzel', serif; letter-spacing: 1px; }
        .font-script { font-family: 'Great Vibes', cursive; }
        .font-urdu { font-family: 'Noto Nastaliq Urdu', serif; line-height: 2; }
        
        .text-gold {
            color: var(--gold);
            text-shadow: 0 2px 10px rgba(0,0,0,0.8);
        }
        .text-white-pop {
            color: #ffffff;
            font-weight: 500;
            text-shadow: 0 2px 4px rgba(0,0,0,0.9);
        }

        /* --- 3D CARD STYLING --- */
        .card-container {
            perspective: 1000px;
        }
        .crystal-card {
            background: rgba(10, 10, 10, 0.85); /* High opacity for text readability */
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 215, 0, 0.4);
            border-bottom: 4px solid var(--gold);
            border-radius: 20px;
            /* Deep 3D Shadow */
            box-shadow: 
                0 15px 35px rgba(0,0,0,0.5),
                0 5px 15px rgba(0,0,0,0.3),
                inset 0 0 20px rgba(255, 215, 0, 0.05);
            padding: 3rem 2rem;
            position: relative;
            transform-style: preserve-3d;
            transform: translateZ(0); /* Hardware accel */
        }
        /* Inner 3D Lift */
        .lift-content {
            transform: translateZ(40px);
        }

        /* --- TOP TICKER --- */
        .top-ticker {
            position: fixed; top: 0; left: 0; width: 100%;
            background: linear-gradient(90deg, #000, #222, #000);
            border-bottom: 2px solid var(--gold);
            color: var(--gold);
            z-index: 1000;
            padding: 10px 0;
            font-family: 'Cinzel', serif;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }

        /* --- BOTTOM FOOTER --- */
        .bottom-footer {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #111;
            border-top: 1px solid var(--gold);
            color: #fff;
            z-index: 1000;
            padding: 8px 0;
            text-align: center;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
        .arham-link {
            color: var(--gold); text-decoration: none; font-weight: bold;
        }

        /* --- BUTTONS & STRIPS --- */
        .time-strip {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 12px;
            display: flex; justify-content: space-between; align-items: center;
            border-left: 4px solid var(--gold);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }
        .time-strip:hover { transform: translateX(5px); background: rgba(255, 215, 0, 0.15); }

        .btn-3d {
            transition: transform 0.1s;
            box-shadow: 0 5px 0 var(--gold-shadow), 0 10px 10px rgba(0,0,0,0.3);
            transform: translateY(0);
        }
        .btn-3d:active {
            transform: translateY(5px);
            box-shadow: 0 0 0 var(--gold-shadow), 0 0 0 rgba(0,0,0,0.3);
        }

        .divider-gold {
            height: 2px;
            background: radial-gradient(circle, var(--gold) 0%, transparent 100%);
            margin: 30px auto;
            width: 80%;
            opacity: 0.8;
        }

    </style>
</head>
<body>

    <div class="live-gradient-bg"></div>

    <div class="top-ticker">
        <marquee scrollamount="8">
            <i class="fa fa-star mx-3"></i> <?php echo $footer['top_ticker']; ?> 
            <i class="fa fa-moon mx-3"></i> <?php echo $footer['top_ticker']; ?>
        </marquee>
    </div>

    <section class="min-vh-100 d-flex align-items-center justify-content-center py-5 card-container">
        <div class="container text-center">
            
            <div class="crystal-card mx-auto" style="max-width: 850px;" data-tilt>
                
                <div class="lift-content">
                    <h1 class="font-urdu text-gold display-4 mb-2" data-aos="zoom-in"><?php echo $config['bismillah']; ?></h1>
                    <p class="small text-white text-uppercase tracking-widest mb-4 opacity-75"><?php echo $config['bismillah_text']; ?></p>

                    <div class="divider-gold"></div>

                    <div class="py-2">
                        <h5 class="text-white font-royal text-uppercase mb-3 letter-spacing-2">Cordially Invited By</h5>
                        
                        <h2 class="font-royal fw-bold text-gold mb-2" style="line-height: 1.4; text-shadow: 0 0 20px rgba(255,215,0,0.2);">
                            <?php echo $config['host_parents']; ?>
                        </h2>
                    </div>

                    <div class="my-5" data-aos="fade-up">
                        <p class="lead font-script text-white fs-2 mb-2">
                            Wedding Ceremony of their Beloved
                        </p>
                        <h1 class="display-1 font-royal text-gold fw-bold mb-0 text-uppercase drop-shadow">
                            <?php echo $config['event_type']; ?>
                        </h1>
                    </div>

                    <div class="mt-4">
                         <i class="fa fa-chevron-down text-white fs-3 animate__animated animate__bounce infinite"></i>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-5 card-container">
        <div class="container">
            <?php foreach($events as $event): ?>
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-7" data-aos="fade-up">
                    
                    <div class="crystal-card mt-3 text-center">
                        <div class="lift-content">
                            <div class="mb-4">
                                <i class="fa <?php echo $event['icon']; ?> display-3 text-gold" style="filter: drop-shadow(0 0 10px rgba(255,215,0,0.5));"></i>
                            </div>

                            <h2 class="font-royal text-white-pop mb-2 display-5"><?php echo $event['name']; ?></h2>
                            <h4 class="text-gold mb-4 font-royal fw-bold"><?php echo $event['date']; ?></h4>
                            
                            <div class="text-start px-md-4 mb-4">
                                <?php foreach($event['times'] as $time): ?>
                                    <div class="time-strip">
                                        <span class="text-white-pop fs-5"><?php echo $time['label']; ?></span>
                                        <span class="badge bg-warning text-dark font-monospace fs-6 shadow"><?php echo $time['time']; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="divider-gold"></div>

                            <div class="pt-2">
                                <h5 class="text-uppercase text-gold small letter-spacing-2 fw-bold">Venue</h5>
                                <h3 class="font-royal text-white mb-1"><?php echo $event['venue_name']; ?></h3>
                                <p class="mb-4 text-white-pop"><?php echo $event['address']; ?></p>
                                
                                <a href="<?php echo $event['map_link']; ?>" target="_blank" class="btn btn-warning rounded-pill px-5 w-100 fw-bold btn-3d">
                                    <i class="fa fa-map-marker-alt me-2"></i> View Venue Map
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="py-5 mb-5 card-container">
        <div class="container">
            <div class="crystal-card mx-auto" style="max-width: 900px;" data-aos="zoom-in">
                <div class="lift-content">
                    <div class="row g-5 text-center">
                        
                        <div class="col-md-4 border-end border-light border-opacity-25">
                            <h4 class="font-royal text-gold h5 mb-4 border-bottom border-secondary pb-2 d-inline-block">Looking Forward</h4>
                            <ul class="list-unstyled text-white-pop">
                                <?php foreach($lists['looking_forward'] as $person): ?>
                                    <li class="mb-3"><?php echo $person; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="col-md-4 border-end border-light border-opacity-25">
                            <h4 class="font-royal text-gold h5 mb-4 border-bottom border-secondary pb-2 d-inline-block">R.S.V.P</h4>
                            <ul class="list-unstyled text-white-pop">
                                <?php foreach($lists['rsvp_people'] as $person): ?>
                                    <li class="mb-3 fw-bold"><?php echo $person; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="col-md-4">
                            <h4 class="font-royal text-gold h5 mb-4 border-bottom border-secondary pb-2 d-inline-block">Contact</h4>
                            <p class="text-white-pop mb-1 fw-bold"><?php echo $contacts['home_address']; ?></p>
                            
                            <div class="mb-4">
                                <?php if(!empty($contacts['home_map_link'])): ?>
                                <a href="<?php echo $contacts['home_map_link']; ?>" target="_blank" class="small text-gold text-decoration-none border-bottom border-warning pb-1">
                                    <i class="fa fa-map-pin"></i> View Home on Map
                                </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid gap-3">
                                <a href="tel:<?php echo $contacts['phone_1']; ?>" class="btn btn-outline-light fw-bold btn-3d">
                                    <i class="fa fa-phone me-2"></i> Call: <?php echo $contacts['phone_1']; ?>
                                </a>
                                
                                <a href="https://wa.me/<?php echo $contacts['phone_2']; ?>" target="_blank" class="btn btn-success fw-bold btn-3d" style="background-color: #25D366; border: none;">
                                    <i class="fab fa-whatsapp me-2 display-6 align-middle"></i> WhatsApp
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bottom-footer">
        <div class="container">
            <a href="https://arhamprinters.pk" target="_blank" class="arham-link">
                <?php echo $footer['bottom_credit']; ?>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.7.2/vanilla-tilt.min.js"></script>
    
    <script>
        AOS.init({ duration: 1000, once: false });
        
        VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
            max: 15,
            speed: 400,
            glare: true,
            "max-glare": 0.4,
            scale: 1.02,
            perspective: 1000
        });
    </script>
</body>
</html>