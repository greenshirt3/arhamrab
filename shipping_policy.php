<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Arham Printers - Shipping Policy</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link href="srcs/css/bootstrap.min.css" rel="stylesheet">
    <link href="srcs/css/all.min.css" rel="stylesheet">
    <link href="srcs/lib/animate/animate.min.css" rel="stylesheet">
    <link href="srcs/css/agency.css" rel="stylesheet">
    <?php include 'seo/seo.php'; ?>

    <style>
        .policy-header {
            /* Reusing the Theme Gradient but shorter height */
            background: radial-gradient(circle at 100% 0%, #06b6d4 0%, #0f172a 50%, #020617 100%);
            padding-top: 140px;
            padding-bottom: 60px;
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }
        .policy-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 40px;
            border: 1px solid #f1f5f9;
        }
        .policy-icon {
            color: var(--brand-cyan);
            margin-right: 10px;
        }
        .table-custom th {
            background-color: var(--brand-blue);
            color: white;
        }
        .alert-custom {
            border-left: 5px solid var(--brand-cyan);
            background-color: #ecfeff;
            color: #0e7490;
        }
        /* Force the Heading and Subtext to a specific color */
.policy-header h1, 
.policy-header p {
    color: #ffffff !important; /* Replace #ffffff with any color code */
}
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <h2 class="m-0">Arham<span style="color: var(--brand-cyan);">Printers</span></h2>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fas fa-bars text-dark"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="index.php#catalog" class="nav-item nav-link">Catalog</a>
                    <a href="#" class="nav-item nav-link" onclick="openGeneralContact()">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="policy-header">
        <div class="container">
            <h1 class="fw-bold animate__animated animate__fadeInDown">Shipping & Order Policy</h1>
            <p class="lead animate__animated animate__fadeInUp">Guide to Ordering, Production, and Delivery</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="policy-card">
                    
                    <div class="mb-5">
                        <h3 class="text-dark fw-bold mb-3"><i class="fas fa-hand-pointer policy-icon"></i> 1. Placing Your Order</h3>
                        <p class="text-muted">For custom printing, our process is designed to prevent mistakes since the products cannot be resold.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-custom">
                                <thead>
                                    <tr>
                                        <th>Requirement</th>
                                        <th>Why this is critical</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Proof Approval is Final</strong><br>You must review and approve the digital proof before printing.</td>
                                        <td>We are not liable for errors (typos, wrong numbers) that were visible on the approved proof.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>100% Payment Upfront</strong><br>Full payment required to start production.</td>
                                        <td>Custom items cannot be resold, so we cannot accept Cash on Delivery (COD).</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Color Disclaimer</strong><br>Screen colors (RGB) differ from Ink colors (CMYK).</td>
                                        <td>By approving the proof, you accept standard industry color variance.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h3 class="text-dark fw-bold mb-3"><i class="fas fa-clock policy-icon"></i> 2. Production & Timelines</h3>
                        <p class="text-muted">Total Wait Time = <strong>Production Time</strong> (Our Work) + <strong>Shipping Time</strong> (Courier).</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong style="color: var(--brand-blue);">Production Time:</strong> 5-7 Business Days (Starts after Payment & Approval).
                            </li>
                            <li class="list-group-item">
                                <strong style="color: var(--brand-blue);">Shipping Time:</strong> 1-3 Business Days (Depends on Courier).
                            </li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-dark fw-bold mb-3"><i class="fas fa-truck-loading policy-icon"></i> 3. Delivery Options</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h5 style="color: var(--brand-cyan); font-weight: 700;">Local Pickup</h5>
                                    <p class="small text-muted mb-0">Collect directly from our Jalalpur Jattan shop. Bring your Order ID.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h5 style="color: var(--brand-cyan); font-weight: 700;">Domestic Shipping</h5>
                                    <p class="small text-muted mb-0">Delivered via TCS/CallCourier. Fees added to final invoice.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-custom mt-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Risk Note:</strong> Once handed to the courier, we are not responsible for transit delays or damage.
                    </div>

                    <div class="text-center mt-5">
                        <a href="#" onclick="openGeneralContact()" class="btn btn-view-details px-5 py-3 rounded-pill" style="font-size: 1rem;">
                            Contact Us About Shipping <i class="fab fa-whatsapp ms-2"></i>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <a href="index.php" class="footer-logo">Arham Printers</a>
                    <p class="mb-4">Your #1 source for premium printing & advertising in Jalalpur Jattan.</p>
                    
                    <div class="mb-4">
                        <h6 class="text-white mb-2" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">Opening Hours</h6>
                        <p class="mb-1 text-white-50"><i class="fas fa-clock me-2 text-info"></i> Sat - Thu: 09:00 AM - 08:00 PM</p>
                        <p class="text-danger"><i class="fas fa-times-circle me-2"></i> Friday: Closed</p>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/arhamprinters.pk"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5>Shop</h5>
                    <a href="index.php#catalog">All Products</a>
                    <a href="index.php#catalog">Business Cards</a>
                    <a href="index.php#catalog">Marketing</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Support</h5>
                    <a href="#">Shipping Policy</a>
                    <a href="return_policy.php">Return Policy</a>
                    <a href="#" onclick="openGeneralContact()">Help Center</a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled mt-3">
                        <li class="mb-3 d-flex align-items-center">
                            <a href="https://maps.app.goo.gl/r7peyma84JdVSecPA" target="_blank" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                </div>
                            </a>
                            <span class="text-white-50">Tanda Road, Jalalpur Jattan.</span>
                        </li>

                        <li class="mb-3 d-flex align-items-center">
                            <a href="tel:+923006238233" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-phone-alt text-success"></i>
                                </div>
                            </a>
                            <span class="text-white-50">+92 300 6238233</span>
                        </li>

                        <li class="d-flex align-items-center">
                            <a href="mailto:info@arhamprinters.pk" class="me-3 text-decoration-none">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                            </a>
                            <span class="text-white-50">info@arhamprinters.pk</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p class="m-0">&copy; 2025 Arham Printers. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <a href="#" onclick="openGeneralContact()" class="whatsapp-float">
        <i class="fab fa-whatsapp fa-lg"></i> Chat
    </a>

    <script src="srcs/js/bootstrap.bundle.min.js"></script>
    <script src="srcs/js/wow.min.js"></script>
    <script>
        const WHATSAPP_NUMBER = "923006238233";
        function openGeneralContact() { window.open(`https://wa.me/${WHATSAPP_NUMBER}`, '_blank'); }
    </script>
</body>
</html>