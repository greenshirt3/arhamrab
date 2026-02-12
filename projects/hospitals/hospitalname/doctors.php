<?php include 'header.php'; ?>

<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5" style="max-width: 500px;">
            <h5 class="d-inline-block text-primary text-uppercase border-bottom border-5">Our Doctors</h5>
            <h1 class="display-4">Qualified Healthcare Professionals</h1>
        </div>
        <div class="row g-4">
            <?php foreach($hospital['doctors'] as $doc): ?>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="team-item position-relative rounded overflow-hidden shadow-sm">
                    <div class="overflow-hidden">
                        <img class="img-fluid w-100" src="<?php echo $doc['image']; ?>" alt="" style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="team-text bg-light text-center p-4">
                        <h5><?php echo $doc['name']; ?></h5>
                        <p class="text-primary"><?php echo $doc['specialty']; ?></p>
                        <div class="d-grid">
                            <a class="btn btn-outline-primary" href="appointment.php?doc=<?php echo urlencode($doc['name']); ?>">Book Appointment</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>