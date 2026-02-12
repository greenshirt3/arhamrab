<?php
// Load Data
$json_file = 'hospital_data.json';
if (!file_exists($json_file)) { die("Error: hospital_data.json missing."); }
$data = json_decode(file_get_contents($json_file), true);
$info = $data['settings'];
$theme = $data['theme'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $info['name']; ?> | <?php echo $info['tagline']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium healthcare services in Jalalpur Jattan.">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
<?php include '../../../seo/seo.php'; ?>

    <style>
        :root {
            --primary: <?php echo $theme['primary']; ?>;
            --secondary: <?php echo $theme['secondary']; ?>;
            --accent: <?php echo $theme['accent']; ?>;
            --font-head: <?php echo $theme['font_heading']; ?>;
            --font-body: <?php echo $theme['font_body']; ?>;
        }

        body { font-family: var(--font-body); background-color: #f8f9fa; color: var(--secondary); overflow-x: hidden; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--font-head); font-weight: 700; color: var(--secondary); }
        
        /* --- GLASSMORPHISM NAVBAR --- */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 0;
            transition: all 0.3s;
        }
        .navbar-brand { font-size: 1.5rem; color: var(--primary) !important; font-weight: 700; }
        .nav-link { color: var(--secondary) !important; font-weight: 600; margin: 0 10px; position: relative; }
        .nav-link::after { content:''; display:block; width:0; height:2px; background:var(--primary); transition:0.3s; }
        .nav-link:hover::after { width:100%; }
        
        .btn-main {
            background: var(--primary); color: white; padding: 12px 30px; border-radius: 50px;
            font-weight: 600; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; border: none;
        }
        .btn-main:hover { background: var(--secondary); color: white; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        /* --- HERO SECTION --- */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(15, 81, 50, 0.85), rgba(20, 30, 48, 0.9)), url('<?php echo $data['hero']['image']; ?>');
            background-size: cover; background-position: center; background-attachment: fixed;
            display: flex; align-items: center; text-align: center; color: white; position: relative;
        }
        .hero-title { font-size: 4rem; margin-bottom: 20px; animation: fadeInUp 1s; color: white;}
        .hero-subtitle { font-size: 1.5rem; font-weight: 300; margin-bottom: 40px; animation: fadeInUp 1.2s; color: #dcdcdc; }

        /* --- CARDS & SERVICES --- */
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title span { color: var(--primary); font-family: var(--font-body); text-transform: uppercase; letter-spacing: 2px; font-weight: 700; font-size: 0.9rem; }
        .section-title h2 { font-size: 3rem; margin-top: 10px; }

        .service-card {
            background: white; padding: 40px 30px; border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05); transition: 0.4s; height: 100%; border: 1px solid transparent;
        }
        .service-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 20px 50px rgba(15, 81, 50, 0.15); }
        .icon-box {
            width: 70px; height: 70px; background: rgba(15, 81, 50, 0.1); color: var(--primary);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 25px; transition: 0.4s;
        }
        .service-card:hover .icon-box { background: var(--primary); color: white; }

        /* --- DOCTORS --- */
        .doctor-card {
            background: white; border-radius: 20px; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: 0.3s; position: relative;
        }
        .doctor-img { width: 100%; height: 350px; object-fit: cover; transition: 0.5s; }
        .doctor-card:hover .doctor-img { transform: scale(1.05); }
        .doctor-info { padding: 25px; text-align: center; position: relative; background: white; }
        .doc-role { color: var(--primary); font-weight: 600; font-size: 0.9rem; margin-bottom: 5px; }

        /* --- APPOINTMENT SECTION --- */
        .appointment-wrap {
            background: var(--secondary); border-radius: 30px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .form-control, .form-select {
            height: 55px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05); color: white; padding-left: 20px;
        }
        .form-control:focus, .form-select:focus { background: rgba(255,255,255,0.1); color: white; border-color: var(--primary); box-shadow: none; }
        .form-control::placeholder { color: rgba(255,255,255,0.5); }

        /* --- FOOTER --- */
        .footer { background: #0b111a; color: white; padding: 80px 0 30px; }
        .footer a { color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; }
        .footer a:hover { color: var(--primary); padding-left: 5px; }
        .social-btn {
            width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1);
            display: inline-flex; align-items: center; justify-content: center; margin-right: 10px; transition: 0.3s;
        }
        .social-btn:hover { background: var(--primary); color: white; }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .section-title h2 { font-size: 2rem; }
        }
    </style>
</head>
<body id="home" data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="70">

    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <i class="fa-solid fa-leaf me-2"></i><?php echo $info['name']; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#doctors">Doctors</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="#appointment" class="btn-main">Book Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo $data['hero']['title']; ?></h1>
                <p class="hero-subtitle"><?php echo $data['hero']['subtitle']; ?></p>
                <a href="<?php echo $data['hero']['cta_link']; ?>" class="btn-main px-5 py-3"><?php echo $data['hero']['cta_text']; ?></a>
            </div>
        </div>
    </header>

    <section id="about" class="py-5 my-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 wow fadeInLeft">
                    <div class="section-title text-start mb-4">
                        <span>Who We Are</span>
                        <h2><?php echo $data['about']['title']; ?></h2>
                    </div>
                    <p class="text-muted lead mb-4"><?php echo $data['about']['description']; ?></p>
                    <div class="row g-4">
                        <?php foreach($data['about']['stats'] as $stat): ?>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-box mb-0 me-3" style="width:50px; height:50px; font-size:20px;">
                                    <i class="fas <?php echo $stat['icon']; ?>"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold"><?php echo $stat['value']; ?></h4>
                                    <small class="text-muted"><?php echo $stat['label']; ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInRight">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-lg" alt="About Us">
                        <div class="position-absolute bottom-0 start-0 bg-white p-4 rounded-top-4 shadow-lg m-4">
                            <h5 class="m-0 text-primary fw-bold">Reg. PHC: 52522</h5>
                            <small>Punjab Healthcare Commission</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-5 bg-light">
        <div class="container">
            <div class="section-title wow fadeInUp">
                <span>Departments</span>
                <h2>Our Medical Services</h2>
            </div>
            <div class="row g-4">
                <?php foreach($data['services'] as $svc): ?>
                <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-card">
                        <div class="icon-box">
                            <i class="fas <?php echo $svc['icon']; ?>"></i>
                        </div>
                        <h4><?php echo $svc['title']; ?></h4>
                        <p class="text-muted mb-0"><?php echo $svc['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="doctors" class="py-5 my-5">
        <div class="container">
            <div class="section-title wow fadeInUp">
                <span>Our Team</span>
                <h2>Qualified Specialists</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach($data['doctors'] as $doc): ?>
                <div class="col-md-6 col-lg-4 wow fadeInUp">
                    <div class="doctor-card">
                        <div class="overflow-hidden">
                            <img src="<?php echo $doc['image']; ?>" class="doctor-img" alt="<?php echo $doc['name']; ?>">
                        </div>
                        <div class="doctor-info">
                            <h5 class="mb-1"><?php echo $doc['name']; ?></h5>
                            <div class="doc-role"><?php echo $doc['role']; ?></div>
                            <small class="text-muted"><?php echo $doc['qual']; ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="appointment" class="py-5" style="background: linear-gradient(rgba(15, 81, 50, 0.9), rgba(15, 81, 50, 0.9)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1920&q=80') fixed center; background-size: cover;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="appointment-wrap p-5 wow zoomIn">
                        <div class="row g-5 align-items-center">
                            <div class="col-lg-5 text-white">
                                <h5 class="text-uppercase text-warning mb-3">Book Appointment</h5>
                                <h2 class="display-5 text-white mb-4">Need A Doctor?</h2>
                                <p class="opacity-75 mb-4">Fill out the form to instantly request an appointment via WhatsApp. Our team will confirm your slot.</p>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-box bg-white text-primary mb-0 me-3" style="width:50px; height:50px; font-size:20px;">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-white-50">Emergency Call</small>
                                        <h5 class="m-0 text-white"><?php echo $info['phone']; ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <form onsubmit="sendToWhatsApp(event)">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="text" id="ptName" class="form-control" placeholder="Your Name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="ptPhone" class="form-control" placeholder="Phone Number" required>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="ptService" class="form-select text-white">
                                                <option selected>Select Service</option>
                                                <?php foreach($data['services'] as $s): echo "<option>".$s['title']."</option>"; endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select id="ptDoc" class="form-select text-white">
                                                <option selected value="Any">Any Doctor</option>
                                                <?php foreach($data['doctors'] as $d): echo "<option>".$d['name']."</option>"; endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <textarea id="ptMsg" class="form-control" rows="3" placeholder="Describe your problem..."></textarea>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-main w-100 py-3" style="background: #d4af37; color: black;">
                                                Confirm Appointment <i class="fab fa-whatsapp ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <h3 class="text-white mb-4"><?php echo $info['name']; ?></h3>
                    <p class="text-white-50 mb-4"><?php echo $info['tagline']; ?>. Combining ancient wisdom with modern science for holistic healing.</p>
                    <div class="d-flex">
                        <a class="social-btn" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="social-btn" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="social-btn" href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-4">Quick Contact</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3 text-primary"></i><?php echo $info['address']; ?></p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3 text-primary"></i><?php echo $info['phone']; ?></p>
                    <p class="mb-2"><i class="fa fa-envelope me-3 text-primary"></i><?php echo $info['email']; ?></p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-4">Tech Partner</h5>
                    <p class="small text-white-50">Designed & Developed by:</p>
                    <a href="https://arhamprinters.pk" target="_blank" class="d-block mb-3 text-decoration-none text-white fw-bold fs-5">
                        ARHAM PRINTERS
                    </a>
                    <a href="https://wa.me/923006238233" class="btn btn-outline-light btn-sm rounded-pill">
                        <i class="fas fa-laptop-code me-2"></i> Get a Site Like This
                    </a>
                </div>
            </div>
            <div class="row pt-5 mt-5 border-top border-secondary">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $info['name']; ?>. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        new WOW().init();

        // WhatsApp Logic
        function sendToWhatsApp(e) {
            e.preventDefault();
            const name = document.getElementById('ptName').value;
            const phone = document.getElementById('ptPhone').value;
            const service = document.getElementById('ptService').value;
            const doctor = document.getElementById('ptDoc').value;
            const msgBody = document.getElementById('ptMsg').value;
            
            const number = "<?php echo $info['whatsapp_clean']; ?>";
            
            let msg = `*New Appointment Request* %0a`;
            msg += `Name: ${name} %0a`;
            msg += `Phone: ${phone} %0a`;
            msg += `Service: ${service} %0a`;
            msg += `Doctor: ${doctor} %0a`;
            msg += `Note: ${msgBody} %0a`;
            msg += `---------------------------`;

            window.open(`https://wa.me/${number}?text=${msg}`, '_blank');
        }
    </script>
</body>
</html>