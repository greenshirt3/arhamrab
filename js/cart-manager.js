const Cart = (() => {
    // CONFIG
    const WHATSAPP_NUMBER = '923006238233'; 
    const STORAGE_KEY = 'arham_cart_v2'; // New key for fresh start

    // STATE
    let cartItems = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    let currentDeliveryType = 'HomeDelivery';

    // SAVE & LOAD
    const saveCart = () => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cartItems));
        updateCartDisplay();
    };

    // CORE FUNCTIONS
    const addItem = (item) => {
        cartItems.push(item);
        saveCart();
        // Use the global showAlert if available, or fallback to alert
        if(window.showAlert) window.showAlert(`${item.productName} added!`, 'success');
        else alert(`${item.productName} added!`);
    };

    const removeItem = (index) => {
        cartItems.splice(index, 1);
        saveCart();
    };

    const updateQuantity = (index, change) => {
        if(cartItems[index]) {
            cartItems[index].quantity += change;
            if(cartItems[index].quantity < 1) cartItems[index].quantity = 1;
            
            // Recalculate price if basePrice was per-unit. 
            // Note: Keep your existing logic here if it's complex.
            saveCart();
        }
    };

    // UI UPDATES
    const updateCartDisplay = () => {
        // Update Count Badges
        const count = cartItems.length;
        document.querySelectorAll('.cart-count, #desktop-cart-count').forEach(el => el.textContent = count);

        // Render Cart List (Only if on a page with #cart-items-list)
        const list = document.getElementById('cart-items-list');
        if(list) {
            list.innerHTML = cartItems.map((item, i) => `
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div>
                        <h6>${item.productName}</h6>
                        <small>${item.options ? Object.values(item.options).join(', ') : ''}</small>
                    </div>
                    <div>
                        <button onclick="Cart.updateQuantity(${i}, -1)">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button onclick="Cart.updateQuantity(${i}, 1)">+</button>
                        <button onclick="Cart.removeItem(${i})" class="text-danger ms-2">&times;</button>
                    </div>
                </div>
            `).join('');
            
            // Update Totals (Simple version)
            const total = cartItems.reduce((sum, item) => sum + (item.basePrice || 0), 0);
            const totalEl = document.getElementById('cart-grand-total');
            if(totalEl) totalEl.textContent = `PKR ${total.toLocaleString()}`;
        }
    };

    // CHECKOUT
    const handleCheckoutSubmission = (e) => {
        e.preventDefault();
        // Construct WhatsApp Message
        let msg = `*NEW ORDER*\n`;
        cartItems.forEach(item => {
            msg += `- ${item.productName} (x${item.quantity}): ${item.basePrice}\n`;
        });
        window.open(`https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(msg)}`, '_blank');
        
        // Clear Cart
        cartItems = [];
        saveCart();
        window.location.href = 'index.php'; // Redirect home
    };

    // INIT
    document.addEventListener('DOMContentLoaded', updateCartDisplay);

    return { addItem, removeItem, updateQuantity, handleCheckoutSubmission };
})();