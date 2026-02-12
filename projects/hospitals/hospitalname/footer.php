<div class="container-fluid footer mt-5">
        <div class="container py-5">
            <div class="row g-5">
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Get In Touch</h5>
                    
                    <p class="mb-2">
                        <a href="<?php echo $info['map_link']; ?>" target="_blank">
                            <i class="fa fa-map-marker-alt me-3"></i><?php echo $info['address']; ?>
                        </a>
                    </p>
                    
                    <p class="mb-2">
                        <a href="tel:<?php echo $info['phone']; ?>">
                            <i class="fa fa-phone-alt me-3"></i><?php echo $info['phone']; ?>
                        </a>
                    </p>
                    
                    <p class="mb-2">
                        <a href="https://wa.me/<?php echo $info['whatsapp_clean']; ?>" target="_blank">
                            <i class="fab fa-whatsapp me-3"></i>Chat on WhatsApp
                        </a>
                    </p>

                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light rounded-circle me-2" href="#" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Our Services</h5>
                    <?php 
                    $count = 0;
                    foreach($hospital['services'] as $svc) {
                        if($count < 4) {
                            echo '<a class="d-block text-white-50 mb-2" href="#">'.$svc['title'].'</a>';
                            $count++;
                        }
                    }
                    ?>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Tech Partner</h5>
                    <p class="small text-white-50 mb-2">Designed & Developed by:</p>
                    <a href="https://arhamprinters.pk" target="_blank" class="d-block mb-3 text-decoration-none">
                        <strong class="text-white" style="font-family: 'Montserrat', sans-serif; letter-spacing: 1px; font-size: 1.1rem;">ARHAM PRINTERS</strong>
                    </a>
                    
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="https://arhamprinters.pk" class="small text-white-50">
                                <i class="fas fa-globe me-2 text-primary"></i> www.arhamprinters.pk
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/923006238233?text=I want a website like Surriya Saddique Hospital" class="small text-white-50">
                                <i class="fas fa-laptop-code me-2 text-success"></i> Get a website like this
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Emergency</h5>
                    <p>We are available 24/7 for any medical emergency.</p>
                    <h3 class="text-white mb-0">
                        <a href="tel:<?php echo $info['emergency_number']; ?>" class="text-white text-decoration-none">
                            <?php echo $info['emergency_number']; ?>
                        </a>
                    </h3>
                </div>
            </div>
        </div>
        <div class="container text-center border-top border-secondary py-4">
            <p class="m-0">&copy; <?php echo date('Y'); ?> <?php echo $info['name']; ?>. All Rights Reserved.</p>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</body>
</html>