<?php include 'includes/header.php'; ?>

<div class="bg-primary py-5 text-white text-center">
    <div class="container">
        <h1 class="fw-bold display-4">Our Departments</h1>
        <p class="lead text-white-50">State of the art facilities for every need.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php 
        $depts = [
            'Cardiology' => 'fa-heart',
            'Neurology' => 'fa-brain',
            'Orthopedics' => 'fa-bone',
            'Gynecology' => 'fa-female',
            'Dermatology' => 'fa-allergies',
            'General Surgery' => 'fa-scalpel',
            'Pharmacy' => 'fa-pills',
            'Laboratory' => 'fa-microscope'
        ];
        
        foreach($depts as $name => $icon):
        ?>
        <div class="col-md-4 col-lg-3" data-aos="fade-up">
            <div class="glass-panel p-4 text-center hover-3d h-100 bg-white">
                <i class="fas <?php echo $icon; ?> fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold"><?php echo $name; ?></h4>
                <p class="text-muted small">24/7 Available Specialists</p>
                <a href="contact.php" class="btn btn-outline-primary btn-sm rounded-pill mt-2">Book Appointment</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>