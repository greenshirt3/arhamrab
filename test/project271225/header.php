<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $info['name']; ?> | World Class Printing</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;900&family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <style>
        :root {
            --bg: #050505;
            --surface: #121212;
            --surface-2: #1e1e1e;
            --cyan: #00f2ea;
            --pink: #ff0050;
            --text: #ffffff;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body { background-color: var(--bg); color: var(--text); font-family: 'Outfit', sans-serif; overflow-x: hidden; }
        
        /* Typography */
        h1, h2, h3 { font-family: 'Space Grotesk', sans-serif; text-transform: uppercase; letter-spacing: -1px; }
        .text-gradient { background: linear-gradient(45deg, var(--cyan), var(--pink)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; }
        
        /* Navbar */
        .navbar { background: rgba(5,5,5,0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 20px 0; }
        .navbar-brand { font-weight: 900; font-size: 1.5rem; letter-spacing: 2px; color: white !important; }
        .navbar-brand span { color: var(--cyan); }
        .nav-link { color: #888 !important; font-weight: 500; transition: 0.3s; margin: 0 10px; font-size: 0.9rem; letter-spacing: 1px; }
        .nav-link:hover, .nav-link.active { color: var(--text) !important; text-shadow: 0 0 10px var(--cyan); }
        
        .btn-neon {
            background: transparent; border: 1px solid var(--cyan); color: var(--cyan);
            padding: 12px 30px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px;
            position: relative; overflow: hidden; transition: 0.4s;
        }
        .btn-neon:hover { background: var(--cyan); color: black; box-shadow: 0 0 30px var(--cyan); }

        /* Glass Cards */
        .glass-card {
            background: var(--surface); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0; padding: 20px; transition: 0.4s; position: relative; overflow: hidden;
            height: 100%;
        }
        .glass-card::before {
            content:''; position: absolute; top:0; left:0; width: 100%; height: 2px;
            background: linear-gradient(90deg, var(--cyan), var(--pink));
            transform: scaleX(0); transform-origin: left; transition: 0.4s;
        }
        .glass-card:hover { background: var(--surface-2); transform: translateY(-5px); }
        .glass-card:hover::before { transform: scaleX(1); }

        /* Product Card */
        .prod-card img { width: 100%; height: 250px; object-fit: cover; filter: grayscale(100%); transition: 0.5s; opacity: 0.7; }
        .prod-card:hover img { filter: grayscale(0%); opacity: 1; scale: 1.05; }
        .prod-price { font-size: 1.2rem; color: var(--cyan); font-weight: 700; }

        /* Marquee */
        .marquee-container { background: var(--cyan); color: black; padding: 10px 0; overflow: hidden; white-space: nowrap; transform: rotate(-1deg) scale(1.05); }
        .marquee-content { display: inline-block; animation: scroll 20s linear infinite; font-weight: 900; font-size: 1.5rem; text-transform: uppercase; }
        @keyframes scroll { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        /* Search Bar */
        .search-wrap { position: relative; margin-bottom: 40px; }
        .search-input { background: transparent; border: none; border-bottom: 2px solid #333; width: 100%; color: white; padding: 15px; font-size: 1.5rem; font-family: 'Space Grotesk'; outline: none; transition: 0.3s; }
        .search-input:focus { border-color: var(--pink); }

        /* Footer */
        .footer { border-top: 1px solid #222; padding-top: 80px; margin-top: 100px; background: #000; }
        
        /* Modal */
        .modal-content { background: #111; border: 1px solid #333; color: white; }
        .modal-header { border-bottom: 1px solid #222; }
        .form-control, .form-select { background: #222; border: 1px solid #333; color: white; }
        .form-control:focus { background: #222; color: white; border-color: var(--cyan); box-shadow: 0 0 10px rgba(0,242,234,0.2); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">ARHAM<span>PRINTERS</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fa fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#catalog">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Work</a></li>
                    <li class="nav-item ms-4">
                        <a href="https://wa.me/<?php echo $info['whatsapp']; ?>" class="btn-neon">
                            <i class="fab fa-whatsapp me-2"></i> WhatsApp
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>