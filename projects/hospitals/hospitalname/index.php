<?php include 'header.php'; ?>

<div class="container-fluid hero-header mb-5">
    <div class="container py-5">
        <div class="row justify-content-start">
            <div class="col-lg-8 text-center text-lg-start">
                <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5 border-primary" style="border-color: rgba(256, 256, 256, .3) !important;"><?php echo $hospital['hero_section']['subtitle']; ?></h5>
                <h1 class="display-1 text-white mb-md-4 animated slideInDown"><?php echo $hospital['hero_section']['title']; ?></h1>
                <div class="pt-2">
                    <a href="doctors.php" class="btn btn-light rounded-pill py-md-3 px-md-5 mx-2 animated slideInRight">Find Doctor</a>
                    <a href="appointment.php" class="btn btn-primary rounded-pill py-md-3 px-md-5 mx-2 animated slideInLeft">Appointment</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-5" id="about">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                <div class="d-flex flex-column">
                    <img class="img-fluid rounded w-100 align-self-end" src="<?php echo $hospital['hero_section']['image']; ?>" alt="">
                </div>
            </div>
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                <p class="d-inline-block border-bottom border-5 border-primary text-primary">About Us</p>
                <h1 class="mb-4">Why Choose <?php echo $info['name']; ?>?</h1>
                <p><?php echo $hospital['hero_section']['description']; ?></p>
                
                <div class="row g-3 pt-3">
                    <?php foreach($hospital['stats'] as $stat): ?>
                    <div class="col-sm-3 col-6">
                        <div class="bg-light text-center rounded-circle py-4 h-100 d-flex flex-column justify-content-center align-items-center">
                            <i class="fa fa-3x <?php echo $stat['icon']; ?> text-primary mb-3"></i>
                            <h6 class="mb-0"><?php echo $stat['label']; ?></h6>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-5" id="services">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 500px;">
            <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Services</h5>
            <h1 class="display-4">Excellent Medical Services</h1>
        </div>
        <div class="row g-4">
            <?php foreach($hospital['services'] as $service): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item bg-light rounded p-5">
                    <div class="service-icon mb-4"><i class="fa fa-2x <?php echo $service['icon']; ?>"></i></div>
                    <h4 class="mb-3"><?php echo $service['title']; ?></h4>
                    <p class="mb-0"><?php echo $service['description']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>