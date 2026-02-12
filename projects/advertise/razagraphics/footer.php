<footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <h3 class="text-white mb-4"><?php echo $info['logo_text']; ?> <span style="color:var(--gold);"><?php echo $info['logo_accent']; ?></span></h3>
                    <p class="text-secondary">Your one-stop destination for cinematic video production and premium commercial printing in Jalalpur Jattan.</p>
                    <div class="mt-4">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="text-white mb-4">Quick Contact</h5>
                    <p class="text-secondary mb-2"><i class="fa fa-map-marker-alt me-2 text-warning"></i> <?php echo $info['address']; ?></p>
                    <p class="text-secondary mb-2"><i class="fa fa-phone me-2 text-warning"></i> <?php echo $info['phone']; ?></p>
                    <p class="text-secondary mb-2"><i class="fa fa-envelope me-2 text-warning"></i> <?php echo $info['email']; ?></p>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary bg-darker">
                        <h6 class="text-secondary text-uppercase mb-2 small">Powered By</h6>
                        <a href="https://arhamprinters.pk" target="_blank" class="text-decoration-none">
                            <h4 class="text-white m-0" style="font-family: 'Montserrat', sans-serif;">ARHAM <span class="text-primary">PRINTERS</span></h4>
                        </a>
                        <p class="text-secondary small mt-2 mb-0">Web Solutions & Tech Partner</p>
                        <hr class="border-secondary my-3">
                        <a href="https://arhamprinters.pk" class="text-secondary small text-decoration-none"><i class="fa fa-globe me-1"></i> arhamprinters.pk</a>
                    </div>
                </div>
            </div>
            <div class="row mt-5 pt-4 border-top border-dark">
                <div class="col-md-6 text-center text-md-start text-secondary small">
                    &copy; 2025 Raza Graphics. All Rights Reserved.
                </div>
                <div class="col-md-6 text-center text-md-end text-secondary small">
                    Designed by <a href="#" class="text-warning text-decoration-none">Arham Printers</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.2.0/js/glightbox.min.js"></script>
    <script>
        new WOW().init();
        const lightbox = GLightbox({ selector: '.glightbox' });
    </script>
</body>
</html>