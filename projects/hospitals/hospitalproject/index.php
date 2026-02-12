<?php include 'header.php'; ?>

<div class="container-fluid py-5 bg-white mb-5" style="background-image: url('https://img.freepik.com/free-photo/blur-hospital_1203-7972.jpg'); background-size: cover; background-blend-mode: overlay; background-color: rgba(255,255,255,0.9);">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-primary px-3 py-2 mb-3">World Class Facility</span>
                <h1 class="display-3 fw-bold text-dark mb-4">Advanced Medicine,<br>Trusted Care.</h1>
                <p class="lead text-secondary mb-4">Leading the way in medical excellence with state-of-the-art technology and compassionate care.</p>
                <div class="d-flex gap-3">
                    <a href="appointment.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow">Book Visit</a>
                    <a href="doctors.php" class="btn btn-outline-dark btn-lg rounded-pill px-5">View Specialists</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded-3 shadow-lg border-top border-5 border-primary">
                    <h4 class="mb-3">Quick Appointment</h4>
                    <form action="appointment.php" method="GET">
                        <select class="form-select mb-3" name="dept">
                            <option>Select Department</option>
                            <?php foreach($data['departments'] as $dept) echo "<option value='{$dept['id']}'>{$dept['name']}</option>"; ?>
                        </select>
                        <button class="btn btn-dark w-100">Proceed <i class="fa fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="text-center mb-5">
        <h6 class="text-primary text-uppercase fw-bold">Centers of Excellence</h6>
        <h2 class="fw-bold">Our Departments</h2>
    </div>
    <div class="row g-4">
        <?php foreach($data['departments'] as $dept): ?>
        <div class="col-md-4 col-lg-3">
            <div class="dept-card p-4 text-center h-100">
                <div class="icon-box mb-3 text-primary">
                    <i class="fa <?php echo $dept['icon']; ?> fa-3x"></i>
                </div>
                <h5 class="fw-bold"><?php echo $dept['name']; ?></h5>
                <p class="text-muted small"><?php echo $dept['desc']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>