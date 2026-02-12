<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $info['name']; ?> | <?php echo $info['tagline']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Montserrat:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/css/glightbox.min.css" rel="stylesheet">
<?php include '../../../seo/seo.php'; ?>
<?php include '../../../secure.php'; ?>

    <style>
        :root {
            --gold: #D4AF37;
            --dark: #111111;
            --print-blue: #0056b3;
            --text-light: #f8f9fa;
        }
        
        body { font-family: 'Montserrat', sans-serif; background-color: var(--dark); color: var(--text-light); overflow-x: hidden; }
        
        /* Typography */
        h1, h2, h3, .cinematic-font { font-family: 'Cinzel', serif; letter-spacing: 2px; }
        
        /* Navbar */
        .navbar { background: rgba(0,0,0,0.9); border-bottom: 1px solid #333; padding: 15px 0; backdrop-filter: blur(10px); }
        .navbar-brand { font-size: 1.8rem; font-weight: 700; color: #fff !important; }
        .navbar-brand span { color: var(--gold); }
        .nav-link { color: #aaa !important; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        .btn-quote { border: 1px solid var(--gold); color: var(--gold); padding: 8px 25px; transition: 0.3s; text-transform: uppercase; font-size: 0.8rem; }
        .btn-quote:hover { background: var(--gold); color: #000; }

        /* Hero */
        .hero { position: relative; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .hero::before { content:''; position:absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(17,17,17,1)); z-index: 1; }
        .hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
        .hero-content { position: relative; z-index: 2; text-align: center; }
        
        /* Section Styling */
        .section-title { text-align: center; margin-bottom: 60px; position: relative; }
        .section-title h2 { color: var(--gold); font-size: 2.5rem; }
        .section-title p { color: #777; max-width: 600px; margin: 0 auto; }
        
        /* Cards */
        .service-card { background: #1a1a1a; border: 1px solid #333; padding: 30px; transition: 0.4s; height: 100%; position: relative; overflow: hidden; }
        .service-card:hover { border-color: var(--gold); transform: translateY(-10px); }
        .service-card i { color: var(--gold); margin-bottom: 20px; }
        
        /* Pricing Table */
        .price-card { background: #1a1a1a; border: 1px solid #333; padding: 40px; text-align: center; transition: 0.3s; position: relative; }
        .price-card.featured { border: 2px solid var(--gold); background: #222; transform: scale(1.05); }
        .price-tag { font-size: 2rem; color: var(--gold); font-family: 'Cinzel', serif; margin: 20px 0; }
        
        /* Footer */
        .footer { background: #000; padding: 60px 0; border-top: 1px solid #222; margin-top: 100px; }
        .social-link { width: 40px; height: 40px; border: 1px solid #444; color: #aaa; display: inline-flex; align-items: center; justify-content: center; margin: 0 5px; transition: 0.3s; text-decoration: none; }
        .social-link:hover { border-color: var(--gold); color: var(--gold); }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .price-card.featured { transform: scale(1); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <?php echo $info['logo_text']; ?> <span><?php echo $info['logo_accent']; ?></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <span class="fa fa-bars text-white"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#production">Production</a></li>
                    <li class="nav-item"><a class="nav-link" href="#printing">Printing</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#packages">Packages</a></li>
                    <li class="nav-item ms-3">
                        <a href="https://wa.me/<?php echo $info['whatsapp']; ?>" class="btn-quote">Get Quote</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>