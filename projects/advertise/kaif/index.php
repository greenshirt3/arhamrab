<?php
// =================================================================
// 1. DATA CONFIGURATION (Embedded JSON Data)
// =================================================================
$json_data = '{
    "profile": {
        "name": "Kaif Ali",
        "tagline": "Arham Printers",
        "bio": "We turn ideas into digital reality. From viral TikTok content to premium business branding and printing solutions.",
        "whatsapp": "923001234567",
        "email": "kaif@arhamprinters.pk",
        "location": "Jalalpur Jattan, Pakistan",
        "hero_image": "https://arhamprinters.pk/img/profiles/kaifimg.webp"
    },
    "stats": {
        "tiktok_followers": "500K+",
        "projects_done": "1.2K+",
        "happy_clients": "900+"
    },
    "socials": {
        "tiktok": "https://tiktok.com/@kaif",
        "instagram": "https://instagram.com/kaif",
        "youtube": "https://youtube.com/kaif",
        "behance": "https://behance.net/kaif"
    },
    "services": [
        {
            "title": "Brand Identity",
            "icon": "fa-fingerprint",
            "desc": "Logo design, color palette, and complete brand guidelines."
        },
        {
            "title": "Flex & Print Design",
            "icon": "fa-print",
            "desc": "High-quality vector designs for large format printing."
        },
        {
            "title": "Social Media Kit",
            "icon": "fa-hashtag",
            "desc": "Post templates, reels editing, and profile optimization."
        }
    ],
    "shop": [
        {
            "name": "Visiting Cards (1000 Qty)",
            "price": "PKR 1,500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/vcard.webp",
            "url": "https://arhamprinters.pk/?product=Visiting Cards"
        },
        {
            "name": "Luxury Matte/UV Cards",
            "price": "PKR 3,500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/uvcard.webp",
            "url": "https://arhamprinters.pk/?product=Luxury Cards"
        },
        {
            "name": "Official Letterheads",
            "price": "PKR 4,500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/letterpad.webp",
            "url": "https://arhamprinters.pk/?product=Letterheads"
        },
        {
            "name": "Bill Books (Carbonless)",
            "price": "PKR 550",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/billbooks.webp",
            "url": "https://arhamprinters.pk/?product=Bill Books"
        },
        {
            "name": "Office Envelopes",
            "price": "PKR 2,500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/envelops.webp",
            "url": "https://arhamprinters.pk/?product=Envelopes"
        },
        {
            "name": "PVC ID Cards + Lanyard",
            "price": "PKR 350",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/pvc.webp",
            "url": "https://arhamprinters.pk/?product=ID Cards"
        },
        {
            "name": "Rubber Stamps (Self Ink)",
            "price": "PKR 850",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/stamps.webp",
            "url": "https://arhamprinters.pk/?product=Rubber Stamps"
        },
        {
            "name": "Flex Banner (Star/China)",
            "price": "PKR 50/sq.ft",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/star.webp",
            "url": "https://arhamprinters.pk/?product=Flex Banners"
        },
        {
            "name": "Marketing Flyers (1000 Qty)",
            "price": "PKR 6,000",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/ishtihar.webp",
            "url": "https://arhamprinters.pk/?product=Flyers (Ishtihar)"
        },
        {
            "name": "Vinyl / Glass Stickers",
            "price": "PKR 120/sq.ft",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/stickers.webp",
            "url": "https://arhamprinters.pk/?product=Stickers"
        },
        {
            "name": "Standees (X-Stand/Rollup)",
            "price": "PKR 1,200",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/placeholder.png",
            "url": "https://arhamprinters.pk/?product=Standees"
        },
        {
            "name": "Custom Printed Mugs",
            "price": "PKR 500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/mugs.webp",
            "url": "https://arhamprinters.pk/?product=Mugs"
        },
        {
            "name": "T-Shirt Printing (DTF/Sub)",
            "price": "PKR 850",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/shirt.webp",
            "url": "https://arhamprinters.pk/?product=T-Shirts"
        },
        {
            "name": "Custom Caps",
            "price": "PKR 450",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/caps.webp",
            "url": "https://arhamprinters.pk/?product=Caps"
        },
        {
            "name": "Custom Keychains",
            "price": "PKR 350",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/keychains.webp",
            "url": "https://arhamprinters.pk/?product=Keychains"
        },
        {
            "name": "Shields & Awards",
            "price": "PKR 1,500",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/placeholder.png",
            "url": "https://arhamprinters.pk/?product=Awards"
        },
        {
            "name": "Wedding Invitations",
            "price": "Starts PKR 30",
            "image": "https://arhamprinters.pk/img/wedding/placeholder.webp",
            "url": "https://arhamprinters.pk/?product=Printed Wedding Cards"
        },
        {
            "name": "Custom Neon Signs",
            "price": "PKR 3,500",
            "image": "https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=600&q=80",
            "url": "https://arhamprinters.pk/#catalog"
        },
        {
            "name": "Kaif Merch Hoodie",
            "price": "PKR 2,500",
            "image": "https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=600&q=80",
            "url": "https://arhamprinters.pk/#catalog"
        },
        {
            "name": "3D Wallpapers",
            "price": "PKR 80/sq.ft",
            "image": "https://arhamprinters.pk/img/arhamdata/arhamproducts/wallpapers.webp",
            "url": "https://arhamprinters.pk/?product=Wallpapers"
        }
    ],
    "tiktok_highlights": [
        "https://www.tiktok.com/@kaif.ali.09/video/7579819470365953298?is_from_webapp=1&sender_device=pc&web_id=7591103912771012103",
        "https://images.unsplash.com/photo-1611605698389-eb4f91555211?auto=format&fit=crop&w=400&q=80",
        "https://images.unsplash.com/photo-1542204165-65bf26472b9b?auto=format&fit=crop&w=400&q=80"
    ]
}';

$data = json_decode($json_data, true);
$profile = $data['profile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $profile['name']; ?> | <?php echo $profile['tagline']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <meta name="description" content="<?php echo $profile['bio']; ?>">
    <meta name="keywords" content="Kaif Ali, Arham Printers, TikTok, Branding, Printing, Jalalpur Jattan">
    <meta property="og:title" content="<?php echo $profile['name']; ?> - <?php echo $profile['tagline']; ?>">
    <meta property="og:description" content="<?php echo $profile['bio']; ?>">
    <meta property="og:image" content="<?php echo $profile['hero_image']; ?>">
    <meta name="author" content="Arham Printers">

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-card: #141414;
            --accent: #00f2ea; /* Cyan Neon */
            --accent-2: #ff0050; /* TikTok Red */
            --text-main: #ffffff;
            --text-muted: #a0a0a0;
        }

        body { background-color: var(--bg-dark); color: var(--text-main); font-family: 'Outfit', sans-serif; overflow-x: hidden; }
        
        /* Navbar */
        .navbar { background: rgba(10, 10, 10, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #222; }
        .navbar-brand { font-weight: 900; font-size: 1.5rem; color: var(--text-main) !important; letter-spacing: 1px; }
        .nav-link { color: var(--text-muted) !important; font-weight: 500; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--accent) !important; }
        
        /* Buttons */
        .btn-neon {
            background: transparent; border: 2px solid var(--accent); color: var(--accent);
            padding: 10px 25px; border-radius: 50px; font-weight: 700; transition: 0.3s;
            text-transform: uppercase; letter-spacing: 1px;
            text-decoration: none;
        }
        .btn-neon:hover { background: var(--accent); color: #000; box-shadow: 0 0 20px var(--accent); }

        .btn-tiktok {
            background: linear-gradient(45deg, #00f2ea, #ff0050); border: none; color: white;
            padding: 12px 30px; border-radius: 50px; font-weight: 700; text-decoration: none;
        }
        .btn-tiktok:hover { opacity: 0.9; transform: scale(1.05); color: white; }

        /* Sections */
        .hero-img { border-radius: 20px; box-shadow: -20px 20px 0px #222; filter: grayscale(20%); transition: 0.5s; }
        .hero-img:hover { filter: grayscale(0%); transform: translateY(-10px); }
        
        .section-title { font-weight: 900; font-size: 3rem; margin-bottom: 30px; }
        .text-gradient { background: linear-gradient(to right, var(--accent), var(--accent-2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* Cards */
        .glass-card {
            background: var(--bg-card); border: 1px solid #333; border-radius: 15px;
            padding: 30px; transition: 0.4s; height: 100%;
        }
        .glass-card:hover { border-color: var(--accent); transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0, 242, 234, 0.1); }
        
        .shop-card img { width: 100%; border-radius: 10px; height: 250px; object-fit: cover; margin-bottom: 15px; }
        
        .stats-box { text-align: center; border-right: 1px solid #333; }
        .stats-box:last-child { border-right: none; }
        .stats-num { font-size: 2.5rem; font-weight: 900; color: var(--accent); }

        /* Footer */
        .footer { background: #000; border-top: 1px solid #222; padding: 50px 0; margin-top: 80px; }
        .social-btn { width: 45px; height: 45px; border-radius: 50%; background: #222; display: inline-flex; align-items: center; justify-content: center; color: white; margin: 0 5px; transition: 0.3s; text-decoration: none; }
        .social-btn:hover { background: var(--accent); color: black; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">KAIF<span style="color:var(--accent);">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="fa fa-bars text-white"></span>
            </button>
            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#shop">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="https://wa.me/<?php echo $profile['whatsapp']; ?>" class="btn-neon">Let's Talk</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="container" style="padding-top: 150px; padding-bottom: 80px;">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 wow fadeInLeft">
                <h4 class="text-uppercase text-muted mb-3" style="letter-spacing: 3px;">Hello, I'm</h4>
                <h1 class="display-1 fw-bold mb-3"><?php echo $profile['name']; ?></h1>
                <h2 class="h1 mb-4 text-gradient"><?php echo $profile['tagline']; ?></h2>
                <p class="lead text-muted mb-5 w-75"><?php echo $profile['bio']; ?></p>
                
                <div class="d-flex gap-3">
                    <a href="<?php echo $data['socials']['tiktok']; ?>" target="_blank" class="btn-tiktok">
                        <i class="fab fa-tiktok me-2"></i> Follow Me
                    </a>
                    <a href="#shop" class="btn-neon">Visit Shop <i class="fas fa-shopping-bag ms-2"></i></a>
                </div>

                <div class="row mt-5 pt-4 border-top border-secondary w-75">
                    <div class="col-4 stats-box">
                        <div class="stats-num"><?php echo $data['stats']['tiktok_followers']; ?></div>
                        <div class="small text-muted">Followers</div>
                    </div>
                    <div class="col-4 stats-box">
                        <div class="stats-num"><?php echo $data['stats']['projects_done']; ?></div>
                        <div class="small text-muted">Projects</div>
                    </div>
                    <div class="col-4 stats-box">
                        <div class="stats-num"><?php echo $data['stats']['happy_clients']; ?></div>
                        <div class="small text-muted">Clients</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center wow fadeInRight">
                <img src="<?php echo $profile['hero_image']; ?>" class="img-fluid hero-img" alt="Kaif Profile">
            </div>
        </div>
    </section>

    <section id="services" class="container py-5">
        <div class="text-center mb-5">
            <h6 class="text-accent text-uppercase" style="color: var(--accent);">What I Do</h6>
            <h2 class="section-title">Creative <span class="text-gradient">Services</span></h2>
        </div>
        <div class="row g-4">
            <?php foreach($data['services'] as $service): ?>
            <div class="col-md-4 wow fadeInUp">
                <div class="glass-card">
                    <i class="fas <?php echo $service['icon']; ?> fa-3x mb-4" style="color: var(--accent);"></i>
                    <h3><?php echo $service['title']; ?></h3>
                    <p class="text-muted"><?php echo $service['desc']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="portfolio" class="container-fluid py-5 bg-black">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Trending on <span style="color: #ff0050;">TikTok</span></h2>
            </div>
            <div class="row g-3 justify-content-center">
                <?php foreach($data['tiktok_highlights'] as $thumb): ?>
                <div class="col-6 col-md-3 wow zoomIn">
                    <div class="position-relative overflow-hidden rounded-3 border border-dark">
                        <img src="<?php echo $thumb; ?>" class="w-100" style="height: 400px; object-fit: cover; opacity: 0.7;" alt="TikTok Video">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <a href="<?php echo $data['socials']['tiktok']; ?>" target="_blank" class="btn btn-light rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-play text-dark"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="shop" class="container py-5 mt-5">
        <div class="text-center mb-5">
            <h6 class="text-accent text-uppercase" style="color: var(--accent);">Merch & Printing</h6>
            <h2 class="section-title">Exclusive <span class="text-gradient">Store</span></h2>
        </div>
        <div class="row g-4">
            <?php foreach($data['shop'] as $product): ?>
            <div class="col-md-3 wow fadeInUp">
                <div class="glass-card shop-card p-2">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <div class="p-3">
                        <h5><?php echo $product['name']; ?></h5>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="h5 mb-0 text-gradient"><?php echo $product['price']; ?></span>
                            <a href="<?php echo isset($product['url']) ? $product['url'] : 'https://arhamprinters.pk'; ?>" target="_blank" class="btn btn-sm btn-light rounded-pill px-3">
                                Buy <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <h2 class="fw-bold mb-3"><?php echo $profile['name']; ?></h2>
                    <p class="text-muted w-75 mx-auto mx-md-0"><?php echo $profile['bio']; ?></p>
                    <div class="mt-4">
                        <a href="<?php echo $data['socials']['tiktok']; ?>" class="social-btn"><i class="fab fa-tiktok"></i></a>
                        <a href="<?php echo $data['socials']['instagram']; ?>" class="social-btn"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo $data['socials']['youtube']; ?>" class="social-btn"><i class="fab fa-youtube"></i></a>
                        <a href="mailto:<?php echo $profile['email']; ?>" class="social-btn"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="p-4 rounded border border-dark" style="background: #0f0f0f; display: inline-block; text-align: left;">
                        <h6 class="text-muted text-uppercase small mb-3">Tech Partner</h6>
                        <a href="https://arhamprinters.pk" target="_blank" class="text-decoration-none">
                            <h4 class="text-white mb-1" style="font-family: 'Montserrat', sans-serif;">ARHAM <span style="color: var(--accent);">PRINTERS</span></h4>
                        </a>
                        <p class="small text-muted mb-3">Professional Printing & Web Solutions</p>
                        <a href="https://arhamprinters.pk" class="text-muted small text-decoration-none me-3"><i class="fas fa-globe me-1"></i> arhamprinters.pk</a>
                        <a href="https://wa.me/923006238233" class="text-muted small text-decoration-none"><i class="fab fa-whatsapp me-1"></i> Get a site like this</a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 pt-4 border-top border-secondary text-muted small">
                &copy; 2025. Powered by <a href="https://arhamprinters.pk" class="text-white text-decoration-none">Arham Printers</a>.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>new WOW().init();</script>
</body>
</html>