<footer class="bg-dark text-light pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h3 class="fw-bold mb-3">CITY<span class="text-primary">HOSPITAL</span></h3>
                <p class="text-white-50">Experience the future of healthcare with our Smart Hospital System. Connected care, instant results, and world-class specialists.</p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold text-white mb-3">Quick Links</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-2"><a href="check_reports.php" class="text-decoration-none text-reset">Check Lab Results</a></li>
                    <li class="mb-2"><a href="doctor/login.php" class="text-decoration-none text-reset">Doctor Portal</a></li>
                    <li class="mb-2"><a href="departments.php" class="text-decoration-none text-reset">Our Specialties</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold text-white mb-3">Emergency Contact</h5>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger text-white p-3 rounded-circle me-3">
                        <i class="fas fa-ambulance fa-lg"></i>
                    </div>
                    <div>
                        <small class="d-block text-white-50">24/7 Ambulance</small>
                        <span class="fw-bold fs-5">1122 / 911</span>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="text-center text-white-50 small">
            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All Rights Reserved. | Powered by Arham Printers
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
</script>
<script src="../assets/smart-search.js"></script>
</body>
</html>