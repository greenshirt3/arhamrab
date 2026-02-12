<?php include 'includes/header.php'; ?>

<header class="hero-gradient py-5 overflow-hidden position-relative">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm mb-3">
                    <i class="fas fa-star me-1"></i> World Class Facility
                </span>
                <h1 class="display-3 fw-bolder mb-4 text-dark">
                    Smart Healthcare <br><span class="text-primary">Reimagined.</span>
                </h1>
                <p class="lead text-secondary mb-5">
                    Welcome to the first fully digital hospital. From QR-coded prescriptions to instant lab results, we connect you to health instantly.
                </p>
                <div class="d-flex gap-3">
                    <a href="check_reports.php" class="btn btn-primary btn-lg rounded-pill shadow-lg px-5">
                        Patient Portal
                    </a>
                    <a href="#services" class="btn btn-outline-dark btn-lg rounded-pill px-5">
                        Our Services
                    </a>
                </div>
            </div>
            <div class="col-lg-6 position-relative d-none d-lg-block" data-aos="fade-left">
                <div class="glass-panel p-4 text-center position-relative z-index-2" style="transform: rotate(-5deg); max-width: 400px; margin: auto;">
                    <i class="fas fa-qrcode fa-5x text-dark mb-3"></i>
                    <h5 class="fw-bold">Scan & Go</h5>
                    <p class="text-muted small">Every patient gets a unique QR Identity Card for instant history access.</p>
                </div>
                <div class="glass-panel p-4 position-absolute top-0 end-0 mt-5 me-5 bg-white border-0 shadow-lg" style="transform: rotate(5deg); z-index: 1;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle p-3"><i class="fas fa-check"></i></div>
                        <div>
                            <h6 class="mb-0 fw-bold">Lab Result Ready</h6>
                            <small class="text-success">Just now</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section id="services" class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <h6 class="text-primary fw-bold text-uppercase">Departments</h6>
            <h2 class="fw-bolder">Centers of Excellence</h2>
        </div>
        <div class="row g-4">
            <?php 
            $services = [
                ['icon'=>'fa-heartbeat', 'title'=>'Cardiology', 'desc'=>'Advanced heart care & surgery.'],
                ['icon'=>'fa-brain', 'title'=>'Neurology', 'desc'=>'Brain & spine specialists.'],
                ['icon'=>'fa-baby', 'title'=>'Pediatrics', 'desc'=>'Care for your little ones.'],
                ['icon'=>'fa-x-ray', 'title'=>'Radiology', 'desc'=>'Digital X-Ray & MRI Scanning.'],
            ];
            foreach($services as $s): 
            ?>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in">
                <div class="glass-panel p-4 h-100 hover-3d text-center bg-white">
                    <div class="icon-box bg-light text-primary rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fas <?php echo $s['icon']; ?> fa-2x"></i>
                    </div>
                    <h5 class="fw-bold"><?php echo $s['title']; ?></h5>
                    <p class="text-muted small"><?php echo $s['desc']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="bg-primary text-white py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Are you a Doctor?</h2>
        <p class="mb-4 text-white-50">Join our network and use our Smart Prescription System.</p>
        <a href="doctor/login.php" class="btn btn-light rounded-pill px-5 text-primary fw-bold">Staff Login</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>