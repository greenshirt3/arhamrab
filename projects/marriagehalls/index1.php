<?php
$data = json_decode(file_get_contents('data/hall_data.json'), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Grand Imperial | Luxury Weddings</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --gold: #D4AF37;
            --emerald: #022c22;
            --dark: #0f172a;
            --cream: #f8f5f2;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Lato', sans-serif; margin: 0; background: var(--cream); color: var(--dark); overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Cinzel', serif; letter-spacing: 1px; }

        /* --- NAVBAR --- */
        .navbar {
            display: flex; justify-content: space-between; align-items: center; padding: 15px 5%;
            position: fixed; top: 0; width: 100%; z-index: 1000;
            background: rgba(2, 44, 34, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(212, 175, 55, 0.3);
            transition: 0.3s;
        }
        .logo { font-size: 1.5rem; color: var(--gold); font-weight: 700; text-transform: uppercase; border: 2px solid var(--gold); padding: 5px 15px; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { color: white; text-decoration: none; text-transform: uppercase; font-size: 0.85rem; transition: 0.3s; letter-spacing: 1px; }
        .nav-links a:hover { color: var(--gold); }
        .mobile-toggle { display: none; color: var(--gold); font-size: 1.5rem; cursor: pointer; }

        /* --- HERO --- */
        .hero {
            height: 100vh; position: relative; display: flex; align-items: center; justify-content: center; text-align: center;
            background: url('https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=1920');
            background-size: cover; background-position: center; background-attachment: fixed;
        }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(2,44,34,0.6), rgba(2,44,34,0.9)); }
        .hero-content { position: relative; z-index: 2; padding: 20px; max-width: 800px; }
        .hero h1 { font-size: 4rem; color: var(--gold); margin-bottom: 15px; line-height: 1.1; text-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .hero p { font-size: 1.2rem; color: #ddd; margin-bottom: 40px; letter-spacing: 2px; text-transform: uppercase; }
        
        .btn-gold {
            background: var(--gold); color: var(--emerald); padding: 15px 40px; font-weight: 700; text-decoration: none; 
            border: 2px solid var(--gold); text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; display: inline-block;
        }
        .btn-gold:hover { background: transparent; color: var(--gold); }

        /* --- SECTIONS --- */
        .section { padding: 100px 5%; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h2 { font-size: 3rem; color: var(--emerald); margin-bottom: 15px; }
        .divider { width: 80px; height: 3px; background: var(--gold); margin: 0 auto; }

        /* FEATURE GRID */
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .feature-card { background: white; padding: 40px 30px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; border-bottom: 3px solid transparent; }
        .feature-card:hover { transform: translateY(-10px); border-bottom-color: var(--gold); }
        .f-icon { font-size: 2.5rem; color: var(--gold); margin-bottom: 20px; }

        /* 360 TOUR PLACEHOLDER */
        .tour-section { background: var(--dark); color: white; text-align: center; }
        .video-container { 
            position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 1000px; margin: 40px auto 0; border: 2px solid var(--gold);
        }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

        /* TESTIMONIALS */
        .testimonial-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 50px; }
        .testimonial { background: white; padding: 30px; border-left: 4px solid var(--gold); font-style: italic; color: #555; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* FOOTER */
        footer { background: #011812; padding: 80px 5% 30px; color: #aaa; text-align: center; border-top: 5px solid var(--gold); }
        .footer-logo { font-family: 'Cinzel'; font-size: 2rem; color: var(--gold); margin-bottom: 20px; }

        /* FLOATING WHATSAPP */
        .float-wa { position: fixed; bottom: 30px; right: 30px; background: #25D366; color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 999; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }

        /* MOBILE RESPONSIVE */
        @media(max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .nav-links { display: none; position: absolute; top: 70px; left: 0; width: 100%; background: var(--emerald); flex-direction: column; padding: 20px; text-align: center; }
            .nav-links.active { display: flex; }
            .mobile-toggle { display: block; }
            .section-header h2 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">Grand Imperial</div>
        <div class="mobile-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
        <div class="nav-links" id="navLinks">
            <a href="#">Home</a>
            <a href="#features">Services</a>
            <a href="#tour">Virtual Tour</a>
            <a href="#reviews">Reviews</a>
            <a href="planner.php" style="color:var(--gold); font-weight:bold;">Plan Event</a>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content" data-aos="fade-up">
            <p>Jalalpur Jattan's Premium Venue</p>
            <h1>Where Royalty<br>Come to Celebrate</h1>
            <a href="planner.php" class="btn-gold">Check Availability & Rates</a>
        </div>
    </header>

    <section class="section" id="features">
        <div class="section-header" data-aos="fade-up">
            <h2>Experience Perfection</h2>
            <div class="divider"></div>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-utensils f-icon"></i>
                <h3>Gourmet Catering</h3>
                <p>Authentic Pakistani cuisine prepared by master chefs using premium ingredients.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-couch f-icon"></i>
                <h3>Luxury Decor</h3>
                <p>Imported fresh flowers, crystal chandeliers, and royal stage setups included.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-parking f-icon"></i>
                <h3>Valet Parking</h3>
                <p>Secure parking for 300+ cars with complimentary valet service for your guests.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <i class="fas fa-mosque f-icon"></i>
                <h3>Prayer & Bridal Rooms</h3>
                <p>Dedicated segregated areas for prayers and a luxury suite for the bridal party.</p>
            </div>
        </div>
    </section>

    <section class="section tour-section" id="tour">
        <div class="section-header">
            <h2 style="color:var(--gold);">Virtual Tour</h2>
            <p style="color:#ddd;">Walk through our halls from the comfort of your home.</p>
        </div>
        <div class="video-container">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/ScMzIvxBSi4?controls=0&start=10&autoplay=1&mute=1&loop=1&playlist=ScMzIvxBSi4" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <br>
        <a href="https://wa.me/923001234567?text=I%20want%20to%20visit%20the%20hall" class="btn-gold" style="margin-top:20px;">Schedule a Physical Visit</a>
    </section>

    <section class="section" id="reviews">
        <div class="section-header">
            <h2>Client Love</h2>
            <div class="divider"></div>
        </div>
        <div class="testimonial-grid">
            <div class="testimonial" data-aos="fade-right">
                <p>"The food was absolutely divine. My guests are still talking about the Mutton Kunna. Best decision to book Grand Imperial!"</p>
                <br><strong>- Saif Ullah & Family</strong>
            </div>
            <div class="testimonial" data-aos="fade-left">
                <p>"Decor was exactly as promised. The management is very cooperative. Highly recommended for Walima functions."</p>
                <br><strong>- Mr. & Mrs. Ahmed</strong>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-logo">The Grand Imperial</div>
        <p>Circular Road, Jalalpur Jattan, Pakistan</p>
        <p>+92 300 1234567 | bookings@grandimperial.pk</p>
        <br>
        <div style="font-size:1.5rem; margin-bottom:20px;">
            <i class="fab fa-facebook margin-right:10px;"></i>
            <i class="fab fa-instagram margin-right:10px;"></i>
            <i class="fab fa-youtube"></i>
        </div>
        <p style="font-size:0.8rem; opacity:0.5;">Powered by <a href="https://arhamprinters.pk" style="color:var(--gold); text-decoration:none;">Arham Printers</a></p>
    </footer>

    <a href="https://wa.me/923001234567?text=Salam,%20I%20have%20a%20query%20about%20booking" class="float-wa" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }
    </script>
</body>
</html>