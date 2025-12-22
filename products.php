<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Arham Printers - Product Catalog</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Printing Services, Business Cards, Stationery, Marketing Materials, Full Catalog" name="keywords">
    <meta content="Explore the full range of professional printing services and product catalog from Arham Printers." name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
    <style>
        /* layout and styling */
        :root {
            --bs-primary: #f28b00; /* primary color*/
            --bs-secondary: #ff5722; /* secondary color */
            --bs-dark: #212529;
            --mobile-nav-height: 60px;
        }
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
        /* Ensure the product name in the button doesn't wrap on small screens */
.single-line-card-name {
    display: inline-block; /* allows span to take width */
    max-width: 85%; /* Reserve space for the icon and padding */
    white-space: nowrap; /* Prevents text wrap */
    overflow: hidden; /* Hides overflowing text */
    text-overflow: ellipsis; /* Displays '...' */
    vertical-align: middle;
}
.product-footer-overlay button {
    text-align: center; /* Ensures text aligns left inside the button */
    padding: 10px 15px !important; /* Adjust padding for better look */
    line-height: 1.2; /* Ensures vertical alignment is clean */
    font-size: 0.85rem;
}
.product-footer-overlay button i {
    margin-right: 5px; /* Spacing between icon and text */
}
        .final-price-box {
            background: var(--bs-primary);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1.5rem;
            text-align: center;
            display: none; 
        }
        .final-price-box h4 {
            color: white;
        }
        
     
        /* header */
       
        .page-header {
            /* mobile image */
            background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url(img/banners/productdesk.webp) center center no-repeat;
            background-size: cover;
            min-height: 250px;
        }

        @media (min-width: 768px) {
            .page-header {
                /* desktop image */
                background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url(img/banners/productdesk.webp) center center no-repeat;
                background-size: cover;
                min-height: 350px;
            }
        }
    /* desktop navigation*/
      
        .navbar-brand h1 {
            margin-top: 0;
            margin-bottom: 0;
        }

        /* Clickable Card Variations */
        .choice-card {
            cursor: pointer;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            text-align: center;
            transition: all 0.2s ease;
            flex-grow: 1;
            min-width: 120px;
            opacity: 1; 
        }
        .choice-card:hover {
            border-color: var(--bs-secondary);
            background-color: #fff8f8;
        }
        .choice-card.selected {
            border-color: var(--bs-primary);
            background-color: var(--bs-primary);
            color: white;
            box-shadow: 0 0 10px rgba(242, 139, 0, 0.3);
        }
            .choice-card.disabled {
            opacity: 0.5;
            pointer-events: none; 
            border-color: #eee;
            background-color: #f7f7f7;
        }
        .choice-card input[type="radio"] {
            display: none;
        }
        .choice-card .label-text {
            font-weight: 500;
            color: var(--bs-dark);
            display: block;
        }
        .choice-card.selected .label-text {
            color: white;
        }
        .product-option-group {
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .product-option-group label {
            font-weight: 600;
            color: var(--bs-dark);
            margin-bottom: 0.75rem;
            display: block;
            font-size: 1.1rem;
        }
        .search-wrapper {
            display: flex;
            border: 2px solid var(--bs-primary);
            border-radius: 50px; /
            overflow: hidden; 
            max-width: 100%;
            height: 50px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .search-input {
            flex-grow: 1;
            border: none !important; 
            padding: 0 20px;
            font-size: 1rem;
            height: 100%;
        }
        .search-input:focus {
            box-shadow: none !important; 
        }
        .search-button {
            width: 60px; 
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-primary);
            border: none; 
            border-radius: 0 50px 50px 0; 
            transition: background-color 0.2s;
            color: white;
            padding: 0; 
        }
        .search-button:hover {
            background-color: var(--bs-secondary); 
        }
             .search-input::placeholder {
            color: #aaa;
        }

      

        /* mobile navigation */
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
        
     
        #mobile-bottom-nav > a, 
        #mobile-bottom-nav > div {
            flex: 1 1 0%; 
        }
        .mobile-nav-link {
            flex: 1; 
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: var(--bs-dark);
            font-size: 0.6rem; 
            padding: 5px 0;
            transition: color 0.2s;
            position: relative;
            height: 100%;
            justify-content: center;
        }
        .mobile-nav-link:hover {
             color: var(--bs-primary);
        }
        .mobile-nav-link i {
            font-size: 1.1rem;
            margin-bottom: 2px;
            line-height: 1;
        }
        .mobile-nav-link.active {
            color: var(--bs-primary);
        }
        

#mobile-nav-logo {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative; 
}
#mobile-nav-logo a {
            background-color: var(--bs-primary);
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(242, 139, 0, 0.6);
            position: absolute; 
            top: -15px; 
            left: 50%;
            transform: translateX(-50%); 
}


#mobile-nav-logo a img {
     vertical-align: middle; 
 
    max-width: 80%; 
    max-height: 80%;
    height: auto !important; 
}

#mobile-nav-logo a i {
            font-size: 1.5rem;
}
            #shop-catalog-section, #product-detail-section, #quote-section, #contact-section, #cart-section, #checkout-section {
            padding-bottom: calc(var(--mobile-nav-height) + 15px); /* Add space for the bottom navbar */
        }
        
        .categories-item a {
            font-size: 0.75rem; 
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 0;
            line-height: 1.2;
        }
        .categories-item a i {
            font-size: 1.2rem; 
            margin-bottom: 5px !important;
        }
        

        #mobile-bottom-nav .dropdown-menu {
            position: absolute;
            bottom: var(--mobile-nav-height); 
            left: 50%;
            transform: translateX(-50%);
            width: 250px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.2);
            border-radius: 8px 8px 0 0;
            padding: 10px 0;
        }
        #mobile-bottom-nav .dropdown-item {
             font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="container">
    <div class="container-fluid bg-dark text-white-50 py-2 px-0 d-none d-lg-block">
        <div class="row gx-0 align-items-center">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="fa fa-phone-alt me-2"></small>
                    <small>+92 300 6238233</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="far fa-envelope-open me-2"></small>
                    <small>info@arhamprinters.pk</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <small class="far fa-clock me-2"></small>
                    <small>Sat - Thu : 09 AM - 09 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center">
                    <a class="text-white-50 ms-4" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="text-white-50 ms-4" href=""><i class="fab fa-twitter"></i></a>
                    <a class="text-white-50 ms-4" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="text-white-50 ms-4" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 d-none d-lg-block">
    
    <div class="d-flex w-100 justify-content-between align-items-center">
        
       <a href="https://arhamprinters.pk/" class="navbar-brand d-flex align-items-center me-4">
            <img src="img/logo2.webp" alt="Arham Printers Logo" style="height: 40px; margin-right: 1rem;">
                </a>

        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
    
        <div class="collapse navbar-collapse flex-grow-1" id="navbarCollapse">
            <div class="navbar-nav me-auto bg-light pe-4 py-3 py-lg-0 d-flex align-items-center">
    <a href="https://arhamprinters.pk/" class="nav-item nav-link">Home</a>

    <a href="#" class="nav-item nav-link active" onclick="showSection('shop-catalog')">Products</a>

    <a href="https://wedding.arhamprinters.pk/" class="nav-item nav-link">Wedding Cards</a>

    <a href="https://prints.arhamprinters.pk/" class="nav-item nav-link">Paper Printing</a>

    <div class="nav-item dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Shop</a>
        <div class="dropdown-menu bg-light border-0 m-0">
            <a href="#" class="dropdown-item" onclick="showSection('shop-catalog')">All Products</a>
            <a href="https://arhamprinters.pk/#quote-section" class="dropdown-item">Get Custom Quote</a>
        </div>
    </div>
    <a href="https://arhamprinters.pk/#contact-section" class="nav-item nav-link">Contact</a>
</div>
            
            <div class="h-100 d-lg-inline-flex align-items-center">
                <a class="btn-sm-square bg-white text-dark rounded-circle me-4 position-relative" href="#cart-section" onclick="showSection('cart')">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="desktop-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size: 0.6em; padding: 4px;">0</span>
                </a>
                <a href="#contact-section" class="btn btn-primary py-2 px-3 rounded-0 d-none d-lg-block" onclick="showSection('quote')">Get a Quote<i
                        class="fa fa-arrow-right ms-3"></i></a>
            </div>
        </div>
    </div>
</nav>

    <nav id="mobile-bottom-nav" class="d-lg-none">
    <a href="https://arhamprinters.pk/" class="mobile-nav-link" id="mobile-link-home">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="#" class="mobile-nav-link active" onclick="showSection('shop-catalog')" id="mobile-link-products">
        <i class="fas fa-th-list"></i>
        <span>Products</span>
    </a>
    <a href="https://wedding.arhamprinters.pk/" class="mobile-nav-link" id="mobile-link-wedding">
        <i class="fas fa-gift"></i>
        <span>Wedding Cards</span>
    </a> 
    <a href="https://prints.arhamprinters.pk/" class="mobile-nav-link" id="mobile-link-print">
        <i class="fas fa-print"></i>
        <span>Paper Prints</span>
    </a> 
    <a href="#cart-section" class="mobile-nav-link" onclick="showSection('cart')" id="mobile-link-cart">
        <i class="fas fa-shopping-cart"></i>
        <span id="cart-count" class="position-absolute translate-middle badge rounded-pill bg-primary" style="top: 8px; right: 10px; font-size: 0.7em;">0</span>
        <span>Cart</span>
    </a>
</nav>
<section id="shop-catalog-section" class="section active">
    
    <div class="container-fluid page-header py-5">
        

<div class="text-center">
    <h2 class="bg-white px-1 py-1 d-inline-block rounded-1">
        Products and Services
    </h2>
</div>
    </div>  
    <div class="container py-5">
        <div class="search-wrapper mb-4">
        <input type="text" id="search-input" class="form-control search-input" placeholder="Search products..." onkeyup="filterProducts()">
        <button class="search-button"><i class="fa fa-search"></i></button>
    </div>
    <div id="search-results-message" class="alert alert-info" style="display:none;"></div>

    <div class="row g-4" id="main-product-catalog">
        </div>
</div>
    </section>
    <section id="product-detail-section" class="section">
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-dark display-6" id="detail-product-name-header">Product Details</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#" onclick="showSection('shop-catalog')">Catalog</a></li>
                <li class="breadcrumb-item active text-dark">Details</li>
            </ol>
        </div>

        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-center">
                            <picture>
                                <source id="detail-product-image-webp" srcset="img/products/placeholder.webp" type="image/webp">
                                <img id="detail-product-image" src="img/products/placeholder.png" class="img-fluid rounded" alt="Product Image" style="max-height: 400px;">
                            </picture>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <h2 class="fw-bold mb-4" id="detail-product-name-body">Product Name</h2>
                        
                        <div id="custom-area-inputs" style="display: none;">
                            <div class="product-option-group" data-option-key="Dimensions">
                                <label>Wall/Piece Dimensions (Feet):</label>
                                <div class="d-flex gap-3">
                                    <input type="number" id="input-width" min="1" placeholder="Width" class="form-control" oninput="updateFinalPrice()">
                                    <span class="align-self-center">x</span>
                                    <input type="number" id="input-height" min="1" placeholder="Height" class="form-control" oninput="updateFinalPrice()">
                                </div>
                                <small class="text-muted mt-2 d-block" id="areaDisplay"></small>
                            </div>
                            
                            <div class="product-option-group" data-option-key="Quantity">
                                <label>Number of Pieces/Walls:</label>
                                <input type="number" id="input-custom-qty" min="1" value="1" class="form-control" oninput="updateFinalPrice()">
                            </div>
                            
                            <div class="alert alert-danger mt-3" id="areaConstraintWarning" style="display: none;"></div>
                        </div>

                        <div id="standard-options-fields">
                            </div>
                        
                        <div class="product-option-group mt-4" data-option-key="DeliveryType">
                            <label><i class="fas fa-truck me-2"></i> <strong>Choose Delivery Method:</strong></label>
                            <div class="d-flex flex-wrap gap-3">
                                <label class="choice-card selected" id="delivery-home" onclick="setDeliveryType('HomeDelivery')">
                                    <input type="radio" name="option-DeliveryType" value="HomeDelivery" checked>
                                    <span class="label-text">Home Delivery</span>
                                </label>
                                <label class="choice-card" id="delivery-pickup" onclick="setDeliveryType('SelfPickUp')">
                                    <input type="radio" name="option-DeliveryType" value="SelfPickUp">
                                    <span class="label-text">Self Pick Up (No Shipping)</span>
                                </label>
                            </div>
                        </div>
                        <div class="product-option-group" id="shipping-zone-selector">
                            <label><i class="fas fa-map-marker-alt me-2"></i> <strong>Check Delivery Charges:</strong></label>
                            
                            <div class="d-flex gap-2 mb-2">
                                <button class="btn btn-outline-primary w-50 rounded-0" onclick="detectLocation()">
                                    <i class="fas fa-search-location me-2"></i> Check City
                                </button>
                                <input type="text" id="input-city" placeholder="Enter Your City Name" class="form-control rounded-0 w-50" oninput="manualLocationCheck()">
                            </div>
                            
                            <small class="text-muted d-block mb-2" id="current-location-display">
                                Current Zone: <span id="zone-name" class="fw-bold text-dark">Jalalpur Jattan (Default)</span>
                            </small>
                        </div>
                        <div class="final-price-box" id="finalPriceBox">
                            <h4 class="mb-2">Total Price (PKR)</h4>
                            <h3 class="display-6 fw-bold" id="finalPriceDisplay">PKR 0</h3>
                            
                            <div class="alert alert-light mt-3 p-3 rounded-0" id="shipping-display-box" style="display: none;">
                                <h5 class="mb-1 text-success">Shipping: <span id="shipping-cost-display" class="fw-bold text-dark">PKR 0</span></h5>
                                <small id="shipping-note-display" class="d-block text-dark"></small>
                            </div>
                            <button class="btn btn-success py-2 px-4 w-100 mt-3" onclick="addItemToCart()">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                        
                        <div class="alert alert-info mt-3" id="selectionWarning" style="display: block;">
                            Please enter dimensions/quantity or select all options above to see the final price.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="cart-section" class="section">
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-dark display-6">Your Shopping Cart</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="#" onclick="showSection('shop-catalog')">Products</a></li>
                <li class="breadcrumb-item active text-dark">Cart</li>
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
                        
                        <button class="btn btn-secondary w-100 py-3 mt-4 rounded-0" onclick="showSection('shop-catalog')" id="continue-shopping-btn">
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
        </section>

        <section id="checkout-section" class="section">
            <div class="container-fluid page-header py-5">
                <h1 class="text-center text-dark display-6">Final Checkout</h1>
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a href="#" onclick="showSection('cart')">Cart</a></li>
                    <li class="breadcrumb-item active text-dark">Checkout</li>
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
            <h1 class="text-center text-dark display-6">Get a Custom Quote</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active text-dark">Quote</li>
            </ol>
        </div>

        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-8">
                        <div class="bg-light p-4 rounded">
                            <form id="quote-form" onsubmit="handleQuoteSubmission(event)">
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
                                            <input type="tel" class="form-control border-0" id="quote-phone" placeholder="Contact Number" required>
                                            <label for="quote-phone">Contact Number</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-0" id="quote-product" placeholder="Product Name (e.g., Visiting Cards, 3D Wallpaper)">
                                            <label for="quote-product">Product Name / Type</label>
                                        </div>
                                    </div>
                                     <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" class="form-control border-0" id="quote-quantity" placeholder="Quantity or Total Area (e.g., 500 cards, 100 sq ft)">
                                            <label for="quote-quantity">Quantity / Estimated Area</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control border-0" placeholder="Details of your inquiry (Size, Finish, Quality, Message)" id="quote-details" style="height: 150px" required></textarea>
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
        </section>
    <section id="contact-section" class="section">
        <div class="container-fluid page-header py-5">
            <h1 class="text-center text-white display-6">Contact Us</h1>
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
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
                            <form id="contact-form-whatsapp" onsubmit="handleContactSubmission(event)">
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
                                            <textarea class="form-control border-0" placeholder="Leave a message here" id="contact-message" style="height: 150px" required></textarea>
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
           <div class="container-fluid footer py-2">
            <div class="container py-2">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="rounded p-2 d-flex flex-column align-items-center">
                            
                            <h6 class="text-white mt-1 mb-1">Address</h6>
                            <p class="text-white mb-1 small text-left">Domela Chowk, Tanda Road, Jalalpur Jattan
                            </p>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="rounded p-2 d-flex flex-column align-items-center">
                            
                            <h6 class="text-white mt-1 mb-1">Mail Us</h6>
                            <p class="text-white mb-1 small text-center">info@arhamprinters.pk</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="rounded p-2 d-flex flex-column align-items-center">
                            <a href="https://wa.me/923006238233" target="_blank" class="text-decoration-none">
                                
                            </a>
                            <h6 class="text-white mt-1 mb-1">Direct Contact</h6>
                            <a href="https://wa.me/923006238233" target="_blank"
                                class="text-white-50 text-decoration-none">
                                <p class="text-white mb-1 small text-center">+92 300 6238233</p>
                            </a>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="rounded p-2 d-flex flex-column align-items-center">
                            
                            <h6 class="text-white mt-1 mb-1">Policies</h6>
                            
<div>
    <ul class="list-unstyled text-left">
         <li><a class="text-white" href="https://shipping.arhamprinters.pk/">Shipping Policy</a></li>
<li><a class="text-white" href="https://return.arhamprinters.pk/">Return Policy</a></li>
        
        </ul>
</div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid copyright py-2">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <span class="text-white small">
                                <i class="fas fa-copyright text-light me-1"></i>
                                Arham Printers, All rights reserved 2025.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
   // --- 1. CONFIGURATION & GLOBALS ---
    const PUBLIC_DATA_URL = 'all_prices.json';
    const WHATSAPP_NUMBER = '923006238233';
    const SUBMIT_ORDER_URL = 'submit_order.php';

    let allProductGroups = {};
    let currentProduct = null;
    let currentSelectedVariations = {};
    let customprintspricing = {};
    let currentShippingZone = 'Within City'; 
    let currentDeliveryType = 'HomeDelivery';

    const SHIPPING_RATES = {
        'small': { 'Within City': 100, 'Same Province': 347, 'Cross Province': 359 },
        'medium': { 'Within City': 100, 'Same Province': 529, 'Cross Province': 541 },
        'overland': { 'Within City': 150, 'Same Province': 1254, 'Cross Province': 1254 }
    };

    const CITY_TO_ZONE = {
        "jalalpur jattan": "Within City", "gujrat": "Same Province", "gujranwala": "Same Province",
        "lahore": "Same Province", "sialkot": "Same Province", "rawalpindi": "Same Province",
        "karachi": "Cross Province", "islamabad": "Cross Province"
    };

    const AREA_BASED_META = {
        "China": { rate: 35, min_area: 2 }, "Star": { rate: 65, min_area: 2 },
        "Backlit": { rate: 100, min_area: 2 }, "One Vision": { rate: 90, min_area: 2 }, 
        "Venyl Sticker": { rate: 90, min_area: 2 }, "3D Wallpaper": { rate: 30, min_area: 20 }
    };

    const variantDisplayNames = {
        'variant_1': 'Size', 'variant_2': 'Stock', 'variant_3': 'Type/Sides',
        'finish': 'Finish', 'shape': 'Shape', 'material': 'Material', 'ColorType': 'Print Color'
    };

    const DISPLAY_ORDER = ['Size', 'Material', 'Type/Sides', 'Finish', 'Shape', 'ColorType', 'GSM'];

    // --- 2. INITIALIZATION ---
    async function initialAppLoad() {
        try {
            const response = await fetch(PUBLIC_DATA_URL);
            const rawData = await response.json();
            initialProductSetup(rawData); 
            renderCatalog();             
            showSection('shop-catalog');
            
            document.getElementById('quote-form').addEventListener('submit', handleQuoteSubmission);
            document.getElementById('contact-form-whatsapp').addEventListener('submit', handleContactSubmission);
        } catch (e) { console.error("Data Load Error", e); }
    }

    function initialProductSetup(flatData) {
        Object.keys(flatData).forEach(catKey => {
            if (catKey === "B/W and Color Prints") {
                customprintspricing["Paper Prints"] = flatData[catKey];
                allProductGroups["Paper Prints"] = { baseName: "Paper Prints", imageFile: 'printer', mainCategory: catKey, isDigitalTiered: true };
                return;
            }
            Object.keys(flatData[catKey]).forEach(prodKey => {
                const data = flatData[catKey][prodKey];
                // Support both standard array format and bundle format
                const imgPath = Array.isArray(data) ? data[0].imageFile : data.imageFile;
                const extractImg = (path) => path ? path.split('/').pop().split('.')[0] : 'placeholder';

                allProductGroups[prodKey] = {
                    baseName: prodKey,
                    imageFile: extractImg(imgPath),
                    isAreaBased: !!AREA_BASED_META[prodKey],
                    mainCategory: catKey,
                    isBundleTiered: !!data.isBundleTiered,
                    bundles: data.bundles || null,
                    combinations: Array.isArray(data) ? data : []
                };
            });
        });
    }

    function renderCatalog() {
        const container = document.getElementById('main-product-catalog');
        let html = '';
        const cats = [...new Set(Object.values(allProductGroups).map(p => p.mainCategory))];
        cats.forEach(cat => {
            if (cat === "Special Occasions (Wedding)") return;
            html += `<h3 class="w-100 pt-4 border-bottom pb-2">${cat}</h3>`;
            Object.values(allProductGroups).filter(p => p.mainCategory === cat).forEach(p => {
                html += `
                <div class='col-lg-3 col-6 mb-4 product-item' data-name="${p.baseName}">
                    <div class='card h-100 shadow-sm border-0 product-card-hover-container'>   
                        <img src='img/products/${p.imageFile}.webp' onerror="this.src='img/products/placeholder.png'" class='w-100'>
                        <div class="product-footer-overlay"><button class="btn btn-primary w-100 rounded-0 p-3" onclick="showProductDetails('${p.baseName}')">${p.baseName}</button></div>
                    </div>
                </div>`;
            });
        });
        container.innerHTML = html;
    }

    // --- 3. VARIATION & DETAILS LOGIC ---
    function showProductDetails(name) {
        currentProduct = allProductGroups[name];
        currentSelectedVariations = {};
        document.getElementById('detail-product-name-body').textContent = name;
        document.getElementById('detail-product-image').src = `img/products/${currentProduct.imageFile}.png`;
        
        const stdFields = document.getElementById('standard-options-fields');
        const areaFields = document.getElementById('custom-area-inputs');
        areaFields.style.display = currentProduct.isAreaBased ? 'block' : 'none';
        stdFields.innerHTML = '';

        if (currentProduct.isDigitalTiered) { 
            showDigitalPrintingDetails(); 
        } else if (currentProduct.isBundleTiered) { 
            showBundleProductDetails(); 
        } else if (!currentProduct.isAreaBased) {
            const keys = sortAttributeKeys([...new Set(currentProduct.combinations.flatMap(c => Object.keys(c).filter(k => k!=='price' && k!=='imageFile' && k!=='FullName')))]);
            keys.forEach(k => {
                stdFields.innerHTML += `<div class="product-option-group"><label>${variantDisplayNames[k] || k}:</label><div class="d-flex flex-wrap gap-2" id="choices-${k}"></div></div>`;
            });
            stdFields.innerHTML += `<div class="product-option-group"><label>Quantity:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
            handleCardSelection(keys[0], [...new Set(currentProduct.combinations.map(c => c[keys[0]]))][0]);
        }
        showSection('product-detail');
        updateFinalPrice();
    }

    function handleCardSelection(key, value) {
        currentSelectedVariations[key] = value;
        
        if (currentProduct.isDigitalTiered) {
            renderDigitalOptions();
            return;
        }

        const allKeys = sortAttributeKeys([...new Set(currentProduct.combinations.flatMap(c => Object.keys(c).filter(k => k!=='price' && k!=='imageFile' && k!=='FullName')))]);
        
        allKeys.forEach(currentKey => {
            const container = document.getElementById(`choices-${currentKey}`);
            if (!container) return;
            const available = new Set();
            currentProduct.combinations.forEach(combo => {
                let match = true;
                for (let i = 0; i < allKeys.indexOf(currentKey); i++) {
                    if (combo[allKeys[i]] !== currentSelectedVariations[allKeys[i]]) match = false;
                }
                if (match) available.add(combo[currentKey]);
            });

            let html = '';
            [...new Set(currentProduct.combinations.map(c => c[currentKey]))].forEach(v => {
                const active = currentSelectedVariations[currentKey] === v;
                html += `<label class="choice-card ${active?'selected':''} ${!available.has(v)?'disabled':''}">
                    <input type="radio" name="${currentKey}" value="${v}" ${active?'checked':''} onchange="handleCardSelection('${currentKey}','${v}')">
                    <span class="label-text">${v}</span></label>`;
            });
            container.innerHTML = html;
            if (!available.has(currentSelectedVariations[currentKey])) {
                currentSelectedVariations[currentKey] = Array.from(available)[0];
            }
        });
        updateFinalPrice();
    }

    function showBundleProductDetails() {
        let h = `<div class="product-option-group"><label>Select Bundle:</label><div class="d-flex flex-wrap gap-2" id="choices-ProductBundle">`;
        Object.keys(currentProduct.bundles).forEach(b => {
            h += `<label class="choice-card"><input type="radio" name="ProductBundle" value="${b}" onchange="handleCardSelection('ProductBundle','${b}')"><span class="label-text">${b}</span></label>`;
        });
        document.getElementById('standard-options-fields').innerHTML = h + `</div></div><div class="product-option-group"><label>Quantity:</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
    }

    function showDigitalPrintingDetails() {
        const stdFields = document.getElementById('standard-options-fields');
        const data = customprintspricing["Paper Prints"];
        const keys = ['Size', 'ColorType', 'GSM'];
        
        keys.forEach(k => {
            stdFields.innerHTML += `<div class="product-option-group"><label>${variantDisplayNames[k] || k}:</label><div class="d-flex flex-wrap gap-2" id="choices-${k}"></div></div>`;
        });
        stdFields.innerHTML += `<div class="product-option-group"><label>Quantity (Pages):</label><input type="number" id="input-standard-qty" class="form-control" value="1" oninput="updateFinalPrice()"></div>`;
        
        // Init first level
        handleCardSelection('Size', Object.keys(data)[0]);
    }

    function renderDigitalOptions() {
        const data = customprintspricing["Paper Prints"];
        const keys = ['Size', 'ColorType', 'GSM'];
        
        keys.forEach((k, idx) => {
            const container = document.getElementById(`choices-${k}`);
            let options = [];
            if (idx === 0) options = Object.keys(data);
            else if (idx === 1) options = Object.keys(data[currentSelectedVariations['Size']] || {});
            else if (idx === 2) options = Object.keys(data[currentSelectedVariations['Size']][currentSelectedVariations['ColorType']] || {});

            let html = '';
            options.forEach(opt => {
                const active = currentSelectedVariations[k] === opt;
                html += `<label class="choice-card ${active?'selected':''}">
                    <input type="radio" name="${k}" value="${opt}" ${active?'checked':''} onchange="handleCardSelection('${k}','${opt}')">
                    <span class="label-text">${opt}</span></label>`;
            });
            container.innerHTML = html;
            if (!currentSelectedVariations[k]) currentSelectedVariations[k] = options[0];
        });
        updateFinalPrice();
    }

    // --- 4. PRICING MATH ---
    function calculateItemPrice() {
        if (!currentProduct) return { error: true };
        let qty = parseInt(document.getElementById('input-standard-qty')?.value || document.getElementById('input-custom-qty')?.value || 1);
        let basePrice = 0;

        if (currentProduct.isAreaBased) {
            const w = parseFloat(document.getElementById('input-width').value || 0);
            const h = parseFloat(document.getElementById('input-height').value || 0);
            const rate = AREA_BASED_META[currentProduct.baseName].rate;
            basePrice = Math.round(w * h * qty * rate);
        } else if (currentProduct.isDigitalTiered) {
            try {
                const tiers = customprintspricing["Paper Prints"][currentSelectedVariations.Size][currentSelectedVariations.ColorType][currentSelectedVariations.GSM];
                Object.keys(tiers).forEach(range => {
                    const [min, max] = range.split('-').map(n => parseInt(n));
                    if (qty >= min && (isNaN(max) || qty <= max)) basePrice = tiers[range] * qty;
                });
            } catch(e) {}
        } else if (currentProduct.isBundleTiered) {
            const b = currentSelectedVariations['ProductBundle'];
            if (b) basePrice = currentProduct.bundles[b] * qty;
        } else if (currentProduct.combinations.length) {
            const match = currentProduct.combinations.find(c => 
                Object.keys(currentSelectedVariations).every(k => c[k] === currentSelectedVariations[k])
            );
            if (match) basePrice = match.price * qty;
        }
        return basePrice > 0 ? { basePrice, productName: currentProduct.baseName } : { error: true };
    }

    function updateFinalPrice() {
        const res = calculateItemPrice();
        const box = document.getElementById('finalPriceBox');
        const warn = document.getElementById('selectionWarning');
        if (res.error) { box.style.display = 'none'; warn.style.display = 'block'; }
        else {
            box.style.display = 'block'; warn.style.display = 'none';
            document.getElementById('finalPriceDisplay').textContent = `PKR ${res.basePrice.toLocaleString()}`;
        }
    }

    // --- 5. UI UTILS ---
    function showSection(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.getElementById(id + '-section').classList.add('active');
        window.scrollTo(0,0);
    }
    function sortAttributeKeys(keys) {
        return keys.sort((a, b) => DISPLAY_ORDER.indexOf(a) - DISPLAY_ORDER.indexOf(b));
    }
    function filterProducts() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(i => i.style.display = i.dataset.name.toLowerCase().includes(q) ? '' : 'none');
    }
    function handleQuoteSubmission(e) {
        e.preventDefault();
        const msg = `*Quote Request*\nName: ${document.getElementById('quote-name').value}\nProduct: ${document.getElementById('quote-product').value}`;
        window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
    }
    function handleContactSubmission(e) {
        e.preventDefault();
        const msg = `*Contact Message*\nName: ${document.getElementById('contact-name').value}\nMsg: ${document.getElementById('contact-message').value}`;
        window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
    }

    document.addEventListener('DOMContentLoaded', initialAppLoad);
</script>
</div>
</body>


</html>