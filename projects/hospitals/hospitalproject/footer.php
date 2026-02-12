<footer class="footer">
        <div class="container pb-5">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h3 class="fw-bold mb-4"><?php echo $info['name']; ?></h3>
                    <p class="text-white-50">Providing advanced medical care with a focus on patient safety and comfort.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="#" class="btn btn-light rounded-circle btn-sm"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-light rounded-circle btn-sm"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-light rounded-circle btn-sm"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4">Contact Info</h5>
                    <p><i class="fa fa-map-marker-alt me-2 text-info"></i> <?php echo $info['address']; ?></p>
                    <p><i class="fa fa-phone me-2 text-info"></i> <?php echo $info['phone']; ?></p>
                    <p><i class="fa fa-envelope me-2 text-info"></i> <?php echo $info['email']; ?></p>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4">Tech Partner</h5>
                    <div class="bg-dark p-3 rounded border border-secondary">
                        <a href="https://arhamprinters.pk" target="_blank" class="text-decoration-none">
                            <h5 class="text-white mb-1">ARHAM <span class="text-info">PRINTERS</span></h5>
                        </a>
                        <small class="text-muted d-block mb-2">Web Solutions & Printing</small>
                        <a href="https://arhamprinters.pk" class="text-white-50 small"><i class="fa fa-globe me-1"></i> arhamprinters.pk</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center py-4 border-top border-secondary text-white-50">
            &copy; 2025 <?php echo $info['name']; ?>. All Rights Reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>