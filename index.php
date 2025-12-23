<?php
// Main Configuration
require_once 'includes/config.php';
$pageTitle = "Arham Printers - Printing & Advertising Service";
include 'includes/header.php';
?>
<style>
    :root {
        --bs-primary: #f28b00; /* Your Brand Orange */
        --bs-secondary: #ff5722; 
        --bs-dark: #212529;
        --mobile-nav-height: 60px;
    }

    /* 1. Hide all sections by default */
    .section {
        display: none;
    }

    /* 2. Only show the section that has the 'active' class */
    .section.active {
        display: block;
        animation: fadeIn 0.4s ease-in-out;
    }

    /* 3. Mobile Padding (So bottom nav doesn't cover content) */
    @media (max-width: 991.98px) {
        body {
            padding-bottom: 80px; /* Space for the bottom nav */
        }
    }

    /* 4. Active State for Mobile Nav Icons */
    .mobile-nav-link.active i, 
    .mobile-nav-link.active span {
        color: var(--bs-primary) !important;
        font-weight: bold;
    }

    /* Choice Cards for Checkout */
    .choice-card {
        cursor: pointer;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
        text-align: center;
        flex: 1;
        min-width: 120px;
        transition: all 0.2s;
    }
    .choice-card:hover { background-color: #fff8f8; border-color: var(--bs-secondary); }
    .choice-card.selected { border-color: var(--bs-primary); background-color: var(--bs-primary); color: white; }
    .choice-card input { display: none; }

    /* Simple Fade In Animation */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Mobile Bottom Nav Styling */
    #mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: var(--mobile-nav-height);
        background: white;
        border-top: 1px solid #ddd;
        box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.1);
        z-index: 1030;
        display: flex;
        align-items: center;
        justify-content: space-around;
    }
    .mobile-nav-link {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        color: var(--bs-dark);
        font-size: 0.7rem;
        padding: 5px 0;
    }
</style>

<section id="home-section" class="section active">
    <div class="container-fluid hero-header bg-light py-5 hero-banner-responsive"> 
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-4 mb-3">The Future of <span class="text-primary">Printing</span> is Here</h2>
                    <p class="mb-4">Premium quality, sharp designs and reliable service—Arham Printers brings your ideas to life.</p>
                    <a href="products.php" class="btn btn-primary py-3 px-5 rounded-0">Explore Products</a>
                </div>
                <div class="col-lg-6">
                    <div class="owl-carousel header-carousel">
                        <div class="owl-carousel-item">
                            <img class="img-fluid" src="img/carousel-1.webp" alt="Printing Services" onerror="this.src='img/products/placeholder.png'">
                        </div>
                       </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid services-categories py-5" id="services-categories">
        <div class="container py-5">
            <div class="text-center mx-auto mb-5">
                <h3 class="display-5 mb-3">Printing & Advertising Solutions</h3>
            </div>
            <div class="row g-4 justify-content-center" id="index-categories-list">
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/office.webp" class="card-img-top" alt="Business & Office Essentials">
                            <div class="card-footer bg-primary text-white text-center rounded-0">Business & Office Essentials</div>
                        </div>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/promotional.webp" class="card-img-top" alt="Marketing & Promotional Items">
                            <div class="card-footer bg-primary text-white text-center rounded-0">Marketing & Promotional Items</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/digital.webp" class="card-img-top" alt="Digital Media Marketing">
                            <div class="card-footer bg-primary text-white text-center rounded-0">Digital Media Marketing</div>
                        </div>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="products.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/print.webp" class="card-img-top" alt="Custom & Large Format">
                            <div class="card-footer bg-primary text-white text-center rounded-0">Custom & Large Format</div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="prints.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/prints.webp" class="card-img-top" alt="B/W and Color Prints">
                            <div class="card-footer bg-primary text-white text-center rounded-0">B/W and Color Prints</div>
                        </div>
                    </a>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="wedding.php" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-0">
                            <img src="img/banners/wedding.webp" class="card-img-top" alt="Wedding Invitation Cards">
                            <div class="card-footer bg-primary text-white text-center rounded-0">Special Occasions (Wedding Cards)</div>
                        </div>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</section>

<section id="cart-section" class="section">
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Your Shopping Cart</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#" onclick="showSection('home')">Home</a></li>
            <li class="breadcrumb-item active text-white">Cart</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="bg-light p-4 rounded-0 shadow-sm">
                        <h3 class="mb-4">Items in Cart (<span id="cart-item-count">0</span>)</h3>
                        <div id="cart-items-list" class="list-group">
                            <p class="text-muted text-center p-5" id="empty-cart-message">Your cart is empty.</p>
                        </div>
                        
                        <button class="btn btn-secondary w-100 py-3 mt-4 rounded-0" onclick="showSection('home')" id="continue-shopping-btn">
                             <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="bg-white p-4 rounded-0 shadow-lg sticky-top" style="top: 100px;">
                        <h3 class="mb-4 text-primary">Order Summary</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span class="fw-bold" id="cart-subtotal">PKR 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 border-bottom pb-3">
                            <span>Delivery:</span>
                            <span class="fw-bold text-success" id="cart-delivery-cost">PKR 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <h4 class="mb-0">Grand Total:</h4>
                            <h4 class="mb-0 text-primary" id="cart-grand-total">PKR 0</h4>
                        </div>
                        
                        <div class="product-option-group" data-option-key="CheckoutDeliveryType">
                            <label><i class="fas fa-truck me-2"></i> Delivery Method:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <label class="choice-card selected" id="checkout-delivery-home" onclick="Cart.setDeliveryType('HomeDelivery')">
                                    <input type="radio" name="option-CheckoutDeliveryType" value="HomeDelivery" checked>
                                    <span class="label-text">Home Delivery</span>
                                </label>
                                <label class="choice-card" id="checkout-delivery-pickup" onclick="Cart.setDeliveryType('SelfPickUp')">
                                    <input type="radio" name="option-CheckoutDeliveryType" value="SelfPickUp">
                                    <span class="label-text">Self Pick Up</span>
                                </label>
                            </div>
                        </div>

                         <div class="product-option-group mt-3" id="checkout-shipping-zone-selector">
                            <label><i class="fas fa-map-marker-alt me-2"></i> Shipping Zone:</label>
                            <input type="text" id="checkout-input-city" placeholder="Enter City for Shipping" class="form-control rounded-0 mb-2" oninput="Cart.manualLocationCheck()">
                            <small class="text-muted d-block" id="checkout-current-location-display">
                                Zone: <span id="checkout-zone-name" class="fw-bold text-dark">Jalalpur Jattan (Within City)</span>
                            </small>
                        </div>

                        <button class="btn btn-primary w-100 py-3 mt-4 rounded-0" onclick="showSection('checkout')">
                            <i class="fas fa-money-check-alt me-2"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="checkout-section" class="section">
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Final Checkout</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#" onclick="showSection('cart')">Cart</a></li>
            <li class="breadcrumb-item active text-white">Checkout</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-10">
                    <div class="bg-light p-4 rounded-0 shadow-lg">
                        <h3 class="mb-4">Contact & Payment</h3>
                        <form id="checkout-form" onsubmit="Cart.handleCheckoutSubmission(event)">
                            
                            <div class="product-option-group">
                                <h4>1. Customer Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-0 rounded-0" id="checkout-name" placeholder="Your Name" required>
                                            <label for="checkout-name">Your Full Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control border-0 rounded-0" id="checkout-phone" placeholder="WhatsApp Number" required>
                                            <label for="checkout-phone">WhatsApp/Contact Number</label>
                                        </div>
                                    </div>
                                    <div class="col-12" id="checkout-address-group">
                                        <div class="form-floating">
                                            <textarea class="form-control border-0 rounded-0" placeholder="Full Address" id="checkout-address" style="height: 80px" required></textarea>
                                            <label for="checkout-address">Full Shipping/Collection Address</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control border-0 rounded-0" id="checkout-email" placeholder="Email (Optional)">
                                            <label for="checkout-email">Email (Optional)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-option-group mt-4">
                                <h4>2. Payment Method</h4>
                                <div class="d-flex flex-wrap gap-3">
                                    <label class="choice-card selected" id="payment-cod" onclick="Cart.setPaymentMethod('CashOnDelivery')">
                                        <input type="radio" name="option-PaymentMethod" value="CashOnDelivery" checked>
                                        <span class="label-text"><i class="fas fa-hand-holding-usd me-2"></i> Cash on Delivery (COD)</span>
                                    </label>
                                    <label class="choice-card" id="payment-bank" onclick="Cart.setPaymentMethod('BankTransfer')">
                                        <input type="radio" name="option-PaymentMethod" value="BankTransfer">
                                        <span class="label-text"><i class="fas fa-university me-2"></i> Bank Transfer</span>
                                    </label>
                                    <label class="choice-card" id="payment-raast" onclick="Cart.setPaymentMethod('RaastQR')">
                                        <input type="radio" name="option-PaymentMethod" value="RaastQR">
                                        <span class="label-text"><i class="fas fa-qrcode me-2"></i> Raast QR</span>
                                    </label>
                                </div>
                            </div>

                            <div id="raast-qr-display" class="product-option-group mt-4 p-4 text-center bg-white border border-success" style="display: none;">
                                <h4><i class="fas fa-qrcode me-2 text-success"></i> Instant Payment via Raast</h4>
                                <p class="text-muted">Scan with your banking app.</p>
                                <div class="d-flex justify-content-center">
                                    <img src="img/qr.webp" id="raast-qr-image" alt="Raast QR Code" style="width: 200px; height: 200px; border: 5px solid #28a745;">
                                </div>
                                <p class="fw-bold mt-3">Grand Total: <span id="raast-qr-amount" class="text-primary">PKR 0</span></p>
                            </div>

                            <div class="alert alert-info mt-4 rounded-0">
                                <strong>Order Total:</strong> <span class="fw-bold text-primary" id="checkout-grand-total">PKR 0</span>.
                                Order confirmation will be sent via WhatsApp.
                            </div>

                            <button class="btn btn-success w-100 py-3 mt-2 rounded-0" type="submit">
                                <i class="fas fa-check-circle me-2"></i> Confirm Order via WhatsApp
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="quote-section" class="section">
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Get a Custom Quote</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#" onclick="showSection('home')">Home</a></li>
            <li class="breadcrumb-item active text-white">Quote</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-light p-4 rounded">
                        <form id="quote-form">
                            <h3 class="mb-4">Quick Inquiry</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="quote-name" placeholder="Your Name" required>
                                        <label for="quote-name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control border-0" id="quote-phone" placeholder="WhatsApp Phone" required>
                                        <label for="quote-phone">WhatsApp Phone</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="quote-product" placeholder="Product Name">
                                        <label for="quote-product">Product Name / Type</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" class="form-control border-0" id="quote-quantity" placeholder="Quantity">
                                        <label for="quote-quantity">Quantity / Estimated Area</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control border-0" placeholder="Details" id="quote-details" style="height: 150px" required></textarea>
                                        <label for="quote-details">Inquiry Message / Specifications</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Submit Quote via WhatsApp</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact-section" class="section">
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Contact Us</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="#" onclick="showSection('home')">Home</a></li>
            <li class="breadcrumb-item active text-white">Contact</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="bg-light p-4 rounded">
                        <div class="d-flex align-items-center mb-4">
                            <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-map-marker-alt text-primary fs-4"></i>
                            </div>
                            <div class="ms-4">
                                <h5 class="mb-2">Our Location</h5>
                                <p class="mb-0">Domela Chowk, Tanda Road, Jalalpur Jattan</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-4">
                            <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-phone-alt text-primary fs-4"></i>
                            </div>
                            <div class="ms-4">
                                <h5 class="mb-2">Call Us</h5>
                                <p class="mb-0">+92 300 6238233</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-envelope text-primary fs-4"></i>
                            </div>
                            <div class="ms-4">
                                <h5 class="mb-2">Email Us</h5>
                                <p class="mb-0">info@arhamprinters.pk</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="bg-light p-4 rounded">
                        <form id="contact-form-whatsapp">
                            <h3 class="mb-4">Send a Direct Message</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="contact-name" placeholder="Your Name" required>
                                        <label for="contact-name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0" id="contact-subject" placeholder="Subject" required>
                                        <label for="contact-subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control border-0" placeholder="Message" id="contact-message" style="height: 150px" required></textarea>
                                        <label for="contact-message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Send Message via WhatsApp</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<nav id="mobile-bottom-nav" class="d-lg-none">
    <a class="mobile-nav-link active" id="mobile-nav-home" href="#" onclick="showSection('home'); return false;">
        <i class="fa fa-home"></i> <span>Home</span>
    </a>
    <a class="mobile-nav-link" id="mobile-nav-quote" href="#" onclick="showSection('quote'); return false;">
        <i class="fas fa-file-invoice"></i> <span>Quote</span>
    </a>
    <a class="mobile-nav-link position-relative" id="mobile-nav-cart" href="#" onclick="showSection('cart'); return false;">
        <i class="fa fa-shopping-cart"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="mobile-cart-badge" style="font-size: 9px; margin-left: -20px; margin-top: 5px;">0</span>
        <span>Cart</span>
    </a>
    <a class="mobile-nav-link" id="mobile-nav-contact" href="#" onclick="showSection('contact'); return false;">
        <i class="fas fa-headset"></i> <span>Contact</span>
    </a>
</nav>

<?php include 'includes/footer.php'; ?>

<a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/counterup/counterup.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script src="js/cart-manager.js"></script>

<script>
    // --- Configuration ---
    const WHATSAPP_NUMBER = '923006238233';

    // --- UI/Navigation Functions ---
    function showSection(sectionName) {
        // 1. Hide all sections
        document.querySelectorAll('.section').forEach(section => {
            section.classList.remove('active');
        });

        // 2. Show the selected section
        const sectionElement = document.getElementById(sectionName + '-section');
        if (sectionElement) {
            sectionElement.classList.add('active');
        }

        // 3. Update Bottom Mobile Navigation Styles
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.classList.remove('active');
        });
        const mobileLink = document.getElementById('mobile-nav-' + sectionName);
        if(mobileLink) {
            mobileLink.classList.add('active');
        }

        // 4. Update Desktop Active Link
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        const desktopLink = document.querySelector(`.navbar-nav a[onclick="showSection('${sectionName}')"]`);
        if (desktopLink) desktopLink.classList.add('active');

        // 5. Scroll to top
        window.scrollTo(0, 0);

        // 6. Reload cart if needed
        if (sectionName === 'cart' && typeof Cart !== 'undefined') {
            Cart.updateCartDisplay();
        }
    }
    
    // Connect Alert System to Cart
    function showAlert(message, type) {
        if (typeof Cart !== 'undefined' && typeof Cart.showAlert === 'function') {
            Cart.showAlert(message, type);
        } else {
            alert(message);
        }
    }
    
    // WhatsApp Quote Form
    document.getElementById('quote-form').addEventListener('submit', function(e) {
         e.preventDefault();
         const name = document.getElementById('quote-name').value;
         const phone = document.getElementById('quote-phone').value;
         const product = document.getElementById('quote-product').value;
         const quantity = document.getElementById('quote-quantity').value;
         const details = document.getElementById('quote-details').value;
         
         let whatsappMessage = `*NEW QUOTE REQUEST (WEB)*\n`;
         whatsappMessage += `*Customer:* ${name}\n`;
         whatsappMessage += `*Phone:* ${phone}\n`;
         whatsappMessage += `*Product:* ${product}\n`;
         whatsappMessage += `*Qty:* ${quantity}\n`;
         whatsappMessage += `*Details:* ${details}`;
         
         window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`, '_blank');
         this.reset();
    });
    
    // WhatsApp Contact Form
    document.getElementById('contact-form-whatsapp').addEventListener('submit', function(e) {
         e.preventDefault();
         const name = document.getElementById('contact-name').value;
         const subject = document.getElementById('contact-subject').value;
         const message = document.getElementById('contact-message').value;

         let whatsappMessage = `*NEW MESSAGE (WEB)*\n`;
         whatsappMessage += `*Name:* ${name}\n`;
         whatsappMessage += `*Subject:* ${subject}\n`;
         whatsappMessage += `*Message:* ${message}`;
         
         window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`, '_blank');
         this.reset();
    });

    document.addEventListener('DOMContentLoaded', () => {
         showSection('home');
    });
</script>
</body>
</html>