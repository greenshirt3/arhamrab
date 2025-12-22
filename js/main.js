(function ($) {
    "use strict";

    // --- GLOBAL CONFIGURATION ---
    const WHATSAPP_NUMBER = '923006238233'; 

    function generateOrderId() {
        var timestamp = new Date().getTime().toString().slice(-6);
        var random = Math.random().toString(36).substring(2, 5).toUpperCase();
        return 'P-' + timestamp + '-' + random;
    }

    // --- NAV LOGIC (Updated for PHP) ---
    function handleMobileNavClick(e) {
        let target = e.target.closest('.mobile-nav-link, .mobile-nav-link-center');
        if (target) {
            if (target.classList.contains('dropdown-toggle')) return;
            let linkId = target.id;
            if (linkId) {
                let linkType = linkId.replace('mobile-link-', '').replace('-center', '');
                // Allow default link behavior for page jumps
                if (['products', 'wedding', 'home', 'print'].includes(linkType)) return;
                setActiveMobileNav(linkType);
            }
        }
    }

    function setActiveMobileNav(sectionName) {
         document.querySelectorAll('#mobile-bottom-nav a.mobile-nav-link').forEach(link => {
             link.classList.remove('active');
         });
         
         if (sectionName === 'home' || sectionName === 'index') {
             document.getElementById('mobile-link-home')?.classList.add('active');
         } else if (['shop-catalog', 'products', 'product-detail'].includes(sectionName)) {
             document.getElementById('mobile-link-products')?.classList.add('active');
         } else if (sectionName === 'wedding') {
             document.getElementById('mobile-link-wedding')?.classList.add('active');
         } else if (sectionName === 'print') {
             document.getElementById('mobile-link-print')?.classList.add('active');
         }
    }

    // --- LAYOUT LOGIC ---
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) $('#spinner').removeClass('show');
        }, 1);
    };
    spinner(0);
    
    if (typeof WOW !== 'undefined') new WOW().init();

    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) $('.nav-bar').addClass('sticky-top shadow-sm');
        else $('.nav-bar').removeClass('sticky-top shadow-sm');
    });

    if ($.fn.owlCarousel) {
        $(".header-carousel").owlCarousel({
            items: 1, autoplay: true, smartSpeed: 2000, dots: false, loop: true, nav: true,
            navText : ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>']
        });
    }

    // Quantity Logic
    $('.quantity button').on('click', function () {
        var button = $(this);
        var oldValue = button.parent().parent().find('input').val();
        var newVal = button.hasClass('btn-plus') ? parseFloat(oldValue) + 1 : (oldValue > 0 ? parseFloat(oldValue) - 1 : 0);
        button.parent().parent().find('input').val(newVal);
    });

    // Back to top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) $('.back-to-top').fadeIn('slow');
        else $('.back-to-top').fadeOut('slow');
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

    // --- INIT ---
    $(document).ready(function() {
        $('#currentYear').text(new Date().getFullYear());
        document.getElementById('mobile-bottom-nav')?.addEventListener('click', handleMobileNavClick);
        
        // UPDATED: Check for PHP paths or clean URLs
        const path = window.location.pathname;
        if (path.includes('products')) setActiveMobileNav('products');
        else if (path.includes('wedding')) setActiveMobileNav('wedding');
        else if (path.includes('print-order')) setActiveMobileNav('print');
        else setActiveMobileNav('home');
        
        $('#quote-form').on('submit', handleQuoteSubmission);
        $('#contact-form-whatsapp').on('submit', handleContactSubmission);
    });

    function handleQuoteSubmission(e) {
        e.preventDefault();
        var name = $('#quote-name').val();
        var phone = $('#quote-phone').val();
        var product = $('#quote-product').val();
        var details = $('#quote-details').val();
        var msg = `*QUOTE REQUEST*\nName: ${name}\nPhone: ${phone}\nProduct: ${product}\nDetails: ${details}`;
        window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
        $('#quote-form').trigger('reset');
    }
    
    function handleContactSubmission(e) {
        e.preventDefault();
        var name = $('#contact-name').val();
        var msgVal = $('#contact-message').val();
        var msg = `*CONTACT*\nName: ${name}\nMsg: ${msgVal}`;
        window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
        $('#contact-form-whatsapp').trigger('reset');
    }

})(jQuery);