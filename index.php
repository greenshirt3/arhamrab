<?php
$pageTitle = "Arham Printers - Printing & Advertising Service";
require_once 'includes/config.php';
include 'includes/header.php';
?>        
        <section id="home-section" class="section active">
            <div class="container-fluid hero-header bg-light py-5 hero-banner-responsive"> 
                <div class="container py-5">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-6">
                            <h2 class="display-4 mb-3">The Future of <span
                                    class="text-primary">Printing</span> is Here</h2>
                            <p>Premium quality, sharp designs and reliable service—Arham
                                Printers brings your ideas to life.</p>
                            <a href="products.php" class="btn btn-primary py-3 px-5">Explore
                                Products</a>
                        </div>
                        <div class="col-lg-6">
                            <div class="owl-carousel header-carousel">
                                <div class="owl-carousel-item">
                                    <img class="img-fluid" src="img/carousel-1.webp" alt="Printing Services">
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
                                    <img src="img/banners/wedding.webp" class="card-img-top"
                                        alt="Wedding Invitation Cards">
                                    <div class="card-footer bg-primary text-white text-center rounded-0">Special Occasions (Wedding Cards)
                                    </div>
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
                            </div>
                            
                            <button class="btn btn-secondary w-100 py-3 mt-4 rounded-0" onclick="showSection('home')" id="continue-shopping-btn">
                                 <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                            </button>
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

                                 <div class="product-option-group" id="checkout-shipping-zone-selector">
                                    <label><i class="fas fa-map-marker-alt me-2"></i> Shipping Zone:</label>
                                    <input type="text" id="checkout-input-city" placeholder="Enter City for Shipping" class="form-control rounded-0 mb-2" oninput="Cart.manualLocationCheck()">
                                    <small class="text-muted d-block" id="checkout-current-location-display">
                                        Zone: <span id="checkout-zone-name" class="fw-bold text-dark">Jalalpur Jattan (Within City)</span>
                                    </small>
                                </div>

                                <button class="btn btn-primary w-100 py-3 mt-2 rounded-0" onclick="showSection('checkout')">
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
                                                <span class="label-text"><i class="fas fa-university me-2"></i> Bank Transfer (Pre-Payment)</span>
                                            </label>
                                            <label class="choice-card" id="payment-raast" onclick="Cart.setPaymentMethod('RaastQR')">
                                                <input type="radio" name="option-PaymentMethod" value="RaastQR">
                                                <span class="label-text"><i class="fas fa-qrcode me-2"></i> Raast QR (Instant Pay)</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div id="raast-qr-display" class="product-option-group mt-4 p-4 text-center bg-white border border-success" style="display: none;">
                                        <h4><i class="fas fa-qrcode me-2 text-success"></i> Instant Payment via Raast</h4>
                                        <p class="text-muted">Scan the QR code with any Raast-enabled banking app to pay the exact amount instantly. The Grand Total is automatically embedded.</p>
                                        
                                        <div class="d-flex justify-content-center">
                                            <img src="img/qr.webp" id="raast-qr-image" alt="Raast QR Code" style="width: 200px; height: 200px; border: 5px solid #28a745;">
                                        </div>
                                        
                                        <p class="fw-bold mt-3">Grand Total: <span id="raast-qr-amount" class="text-primary">PKR 0</span></p>
                                        <small class="text-success d-block">This payment method requires instant confirmation via WhatsApp after scanning.</small>
                                    </div>
                                    <div class="alert alert-info mt-4 rounded-0">
                                        <strong>Order Total:</strong> <span class="fw-bold text-primary" id="checkout-grand-total">PKR 0</span>.
                                        By clicking "Confirm Order", we will open WhatsApp for final confirmation and processing.
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
                                                <input type="text" class="form-control border-0" id="quote-name"
                                                    placeholder="Your Name" required>
                                                <label for="quote-name">Your Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control border-0" id="quote-phone"
                                                    placeholder="WhatsApp Phone" required>
                                                <label for="quote-phone">WhatsApp Phone</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="quote-product"
                                                    placeholder="Product Name (e.g., Visiting Cards, 3D Wallpaper)">
                                                <label for="quote-product">Product Name / Type</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="number" class="form-control border-0" id="quote-quantity"
                                                    placeholder="Quantity or Total Area (e.g., 500 cards, 100 sq ft)">
                                                <label for="quote-quantity">Quantity / Estimated Area</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control border-0"
                                                    placeholder="Details of your inquiry (Size, Finish, Quality, Message)"
                                                    id="quote-details" style="height: 150px" required></textarea>
                                                <label for="quote-details">Inquiry Message / Specifications</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 py-3" type="submit">Submit Quote via
                                                WhatsApp</button>
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
                                    <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa fa-map-marker-alt text-primary fs-4"></i>
                                    </div>
                                    <div class="ms-4">
                                        <h5 class="mb-2">Our Location</h5>
                                        <p class="mb-0">Domela Chowk,Tanda Road, Jalalpur Jattan</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-4">
                                    <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="fa fa-phone-alt text-primary fs-4"></i>
                                    </div>
                                    <div class="ms-4">
                                        <h5 class="mb-2">Call Us</h5>
                                        <p class="mb-0">+92 300 6238233</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center border border-primary rounded-circle mb-3"
                                        style="width: 60px; height: 60px;">
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
                                                <input type="text" class="form-control border-0" id="contact-name"
                                                    placeholder="Your Name" required>
                                                <label for="contact-name">Your Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="contact-subject"
                                                    placeholder="Subject" required>
                                                <label for="contact-subject">Subject</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control border-0"
                                                    placeholder="Leave a message here" id="contact-message"
                                                    style="height: 150px" required></textarea>
                                                <label for="contact-message">Message</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 py-3" type="submit">Send Message via
                                                WhatsApp</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php include 'includes/footer.php'; ?>

        <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>
             <script>
            // --- Configuration ---
            const WHATSAPP_NUMBER = '923006238233';
            const SUBMIT_ORDER_URL = 'submit_order.php'; 

            // --- UI/Navigation Functions ---
            function showSection(sectionName) {
                // Hide all sections
                document.querySelectorAll('.section').forEach(section => {
                    section.classList.remove('active');
                });

                // Show the selected section
                const sectionElement = document.getElementById(sectionName + '-section');
                if (sectionElement) {
                    sectionElement.classList.add('active');
                }

                // Update active desktop nav link
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                
                // Set the active desktop link based on section name
                const desktopLink = document.querySelector(`.navbar-nav a[onclick="showSection('${sectionName}')"]`);
                if (desktopLink) {
                    desktopLink.classList.add('active');
                } else if (sectionName === 'home') {
                     // Default home active state
                     document.querySelector(`.navbar-nav a[href="index.php"]`)?.classList.add('active');
                } else if (sectionName === 'cart' || sectionName === 'checkout') {
                    // Cart/Checkout sections don't have a direct nav link to be 'active'
                }

                // Set the active mobile link (calls centralized logic in main.js)
                if (typeof setActiveMobileNav === 'function') {
                    setActiveMobileNav(sectionName);
                }
                
                window.scrollTo(0, 0);

                // Reload cart display on showing cart section
                if (sectionName === 'cart' && typeof Cart !== 'undefined') {
                    Cart.updateCartDisplay();
                }
            }
            
            // Function for the Home Page category links that navigate to products.php
            function handleCategoryLink(category) {
                 window.location.href = `products.php?category=${encodeURIComponent(category)}`;
            }

            // Function for the Home Page Contact/Quote Buttons
            function handleQuoteContact(sectionName) {
                showSection(sectionName);
            }

            // Replaced custom showAlert with global Cart.showAlert
            function showAlert(message, type) {
                if (typeof Cart !== 'undefined' && typeof Cart.showAlert === 'function') {
                    Cart.showAlert(message, type);
                }
            }
            
            // Add WhatsApp handlers for Quote and Contact forms
            document.getElementById('quote-form').addEventListener('submit', function(e) {
                 e.preventDefault();
                 const name = document.getElementById('quote-name').value;
                 const phone = document.getElementById('quote-phone').value;
                 const product = document.getElementById('quote-product').value;
                 const quantity = document.getElementById('quote-quantity').value;
                 const details = document.getElementById('quote-details').value;
                 
                 let whatsappMessage = `*NEW QUOTE REQUEST (WEB - HOME)*\n`;
                 whatsappMessage += `*Customer:* ${name}\n`;
                 whatsappMessage += `*Phone (Contact):* ${phone}\n`;
                 whatsappMessage += `*Product Type:* ${product}\n`;
                 whatsappMessage += `*Quantity/Area:* ${quantity}\n`;
                 whatsappMessage += `*Specifications:* ${details}`;
                 
                 window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`, '_blank');
                 showAlert('Quote submitted via WhatsApp! We will contact you shortly.', 'success');
                 this.reset();
            });
            
            document.getElementById('contact-form-whatsapp').addEventListener('submit', function(e) {
                 e.preventDefault();
                 const name = document.getElementById('contact-name').value;
                 const subject = document.getElementById('contact-subject').value;
                 const message = document.getElementById('contact-message').value;

                 let whatsappMessage = `*NEW CONTACT MESSAGE (WEB - HOME)*\n`;
                 whatsappMessage += `*Sender:* ${name}\n`;
                 whatsappMessage += `*Subject:* ${subject}\n`;
                 whatsappMessage += `*Message:* ${message}`;
                 
                 window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`, '_blank');
                 showAlert('Message sent via WhatsApp!', 'success');
                 this.reset();
            });

            document.addEventListener('DOMContentLoaded', () => {
                 // Initial active state is handled by main.js after DOMContentLoaded
            });
        </script>

