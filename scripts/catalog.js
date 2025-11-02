// Catalog Management Module
const CatalogManager = (() => {
    // DOM Elements
    const elements = {
        sidebar: document.querySelector('.product-details-sidebar'),
        cartPopup: document.querySelector('.shopping-cart-popup'),
        checkoutPopup: document.querySelector('.checkout-popup'),
        overlay: document.querySelector('.overlay'),
        quantityDisplay: document.querySelector('.quantity-display'),
        addToCartBtn: document.querySelector('.add-to-cart'),
        increaseBtn: document.querySelector('.increase-quantity'),
        decreaseBtn: document.querySelector('.decrease-quantity'),
        mainCartBtn: document.querySelector('.main-cart-btn'),
        cartCountBadge: document.querySelector('.cart-count'),
        searchInput: document.getElementById('catalogSearch'),
        chips: Array.from(document.querySelectorAll('.chip')),
        productCards: Array.from(document.querySelectorAll('.product-card'))
    };

    // State
    let currentProduct = null;
    let currentQuantity = 1;
    let cart = [];

    // Constants
    const SHIPPING_COST = 150;
    const TAX_RATE = 0.12;
    const CART_STORAGE_KEY = 'mjiphil_cart';

    // === Cart Persistence ===
    async function loadCartFromDatabase() {
        const res = await cartRequest('get');
        if (res.success && Array.isArray(res.cart)) {
            cart = res.cart.map(item => ({
            id: item.product_id,
            name: item.product_name,
            price: item.price,
            quantity: parseInt(item.quantity)
            }));
        } else {
            cart = []; // fallback to empty array
        }

        updateCartDisplay();
        updateCartCount();
    }


    function saveCartToStorage() {
        try {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
        } catch (e) {
            console.error('Error saving cart:', e);
        }
    }

    // === Utility Functions ===
    function parsePrice(priceStr) {
        return parseFloat(priceStr.replace('₱', '').replace(/,/g, ''));
    }

    function formatPrice(price) {
        return `₱${price.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function sanitizeText(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // === Cart Management ===
    function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        elements.cartCountBadge.textContent = totalItems;
        elements.cartCountBadge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }

    async function addToCart(product, quantity) {
        console.log('[addToCart] sending product', product);
        const res = await cartRequest('add', {
            product_id: product.id,
            quantity
        });
        if (res.success) {
            await loadCartFromDatabase();
            showAddToCartFeedback();
        } else {
            alert(res.message);
        }
    }


    async function removeFromCart(productId) {
        const res = await cartRequest('delete', { product_id: productId });
        if (res.success) {
            await loadCartFromDatabase();
        } else {
            alert(res.message);
        }
    }


    async function updateCartCount() {
    // Compute total items
    const totalItems = Array.isArray(cart)
        ? cart.reduce((sum, item) => sum + (item.quantity || 0), 0)
        : 0;

    elements.cartCountBadge.textContent = totalItems;
    elements.cartCountBadge.style.display = totalItems > 0 ? "inline-block" : "none";

    // Sync to DB (optional)
    try {
        // Replace cart contents in DB with current version
        // (simpler than incremental sync)
        await cartRequest('sync', { cart: JSON.stringify(cart) });
    } catch (err) {
        console.error("Failed to sync cart count:", err);
    }
    }


    function calculateCartTotals() {
        const subtotal = cart.reduce((sum, item) => {
            return sum + (parsePrice(item.price) * item.quantity);
        }, 0);
        
        const shipping = subtotal > 0 ? SHIPPING_COST : 0;
        const tax = subtotal * TAX_RATE;
        const total = subtotal + shipping + tax;

        return { subtotal, shipping, tax, total };
    }

    function updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        
        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cart-x" style="font-size: 48px;"></i>
                    <p class="mt-3">Your cart is empty</p>
                    <p class="small">Add items to your cart to continue shopping</p>
                </div>
            `;
            document.getElementById('cartSubtotal').textContent = '₱0.00';
            document.querySelector('.proceed-checkout-btn').disabled = true;
            return;
        }

        let cartHTML = '';
        cart.forEach(item => {
            const price = parsePrice(item.price);
            const itemTotal = price * item.quantity;

            cartHTML += `
                <div class="cart-item mb-3 p-3 border rounded">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="product-placeholder" style="width:70px; height:70px; flex-shrink: 0;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${sanitizeText(item.name)}</h6>
                            <div class="text-muted small mb-2">${formatPrice(price)} each</div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary decrease-cart-item"
                                        data-id="${item.id}"
                                        style="width:28px; height:28px; padding:0; font-size:14px;">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="fw-bold" style="min-width:30px; text-align:center;">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary increase-cart-item"
                                        data-id="${item.id}"
                                        style="width:28px; height:28px; padding:0; font-size:14px;">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <div class="fw-bold text-dark">${formatPrice(itemTotal)}</div>
                            <button class="btn btn-sm btn-link text-danger p-0 remove-cart-item"
                                    data-id="${item.id}"
                                    title="Remove item"
                                    style="font-size: 18px; line-height: 1;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItems.innerHTML = cartHTML;

        // Calculate and display subtotal
        const { subtotal } = calculateCartTotals();
        document.getElementById('cartSubtotal').textContent = formatPrice(subtotal);
        document.querySelector('.proceed-checkout-btn').disabled = false;

        // Attach event handlers
        attachCartItemHandlers();
    }

    function attachCartItemHandlers() {
        // Remove item
        document.querySelectorAll('.remove-cart-item').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.currentTarget.dataset.id;
                if (confirm(`Remove this item from cart?`)) {
                    await removeFromCart(id);
                }
            });
        });

        // Increase quantity
        document.querySelectorAll('.increase-cart-item').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.currentTarget.dataset.id;
                const item = cart.find(i => i.id == id);
                if (item) {
                    await updateItemQuantity(id, item.quantity + 1);
                }
            });
        });

        // Decrease quantity
        document.querySelectorAll('.decrease-cart-item').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.currentTarget.dataset.id;
                const item = cart.find(i => i.id == id);
                if (item && item.quantity > 1) {
                    await updateItemQuantity(id, item.quantity - 1);
                }
            });
        });
    }


    async function updateItemQuantity(productId, newQuantity) {
        console.log(`[Cart] Updating product ${productId} → qty ${newQuantity}`);
        
        const res = await cartRequest('update', {
            product_id: productId,
            quantity: newQuantity
        });

        if (res.success) {
            await loadCartFromDatabase(); // Reload DB state
        } else {
            console.error("[Cart] Update failed:", res.message);
            alert(res.message);
        }
    }


    function showAddToCartFeedback() {
        const btn = elements.addToCartBtn;
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Added to Cart!';
        btn.style.backgroundColor = '#198754';
        btn.disabled = true;
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.backgroundColor = '#6b0f0f';
            btn.disabled = false;
        }, 2000);
    }

    // === Checkout Functions ===
    function proceedToCheckout() {
        if (cart.length === 0) return;

        // Close cart popup
        elements.cartPopup.classList.remove('active');
        
        // Show checkout popup
        updateCheckoutSummary();
        elements.checkoutPopup.classList.add('active');
    }

    function updateCheckoutSummary() {
        const { subtotal, shipping, tax, total } = calculateCartTotals();
        
        // Update order items list
        const orderItemsList = document.getElementById('orderItemsList');
        let itemsHTML = '';
        
        cart.forEach(item => {
            const price = parsePrice(item.price);
            const itemTotal = price * item.quantity;
            itemsHTML += `
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <div>
                        <div class="fw-medium">${sanitizeText(item.name)}</div>
                        <div class="text-muted small">${formatPrice(price)} × ${item.quantity}</div>
                    </div>
                    <div class="fw-bold">${formatPrice(itemTotal)}</div>
                </div>
            `;
        });
        
        orderItemsList.innerHTML = itemsHTML;

        // Update summary totals
        document.getElementById('checkoutSubtotal').textContent = formatPrice(subtotal);
        document.getElementById('checkoutShipping').textContent = formatPrice(shipping);
        document.getElementById('checkoutTax').textContent = formatPrice(tax);
        document.getElementById('checkoutTotal').textContent = formatPrice(total);
    }

    async function confirmOrder() {
        try {
            const response = await fetch('./api/order.php', {
                method: 'POST',
                credentials: 'include',
                body: new URLSearchParams({ action: 'create' })
            });

            const data = await response.json();
            console.log("[Order] Response:", data);

            if (data.success) {
                showOrderSuccessModal();
                await loadCartFromDatabase();
            } else {
                alert(data.message || "Failed to create order.");
            }
        } catch (err) {
            console.error("Order creation failed:", err);
            alert("Order creation failed. Check console for details.");
        }
    }


    function showOrderSuccessModal() {
        // Create success modal
        const modal = document.createElement('div');
        modal.className = 'order-success-modal';
        modal.innerHTML = `
            <div class="order-success-content">
                <div class="success-animation">
                    <div class="confetti">🎉</div>
                    <div class="confetti">🎊</div>
                    <div class="confetti">✨</div>
                    <div class="confetti">🌟</div>
                    <div class="confetti">💫</div>
                    <div class="confetti">⭐</div>
                </div>
                <div class="checkmark-circle">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 class="mt-4 mb-2">Order Placed Successfully! 🎉</h3>
                <p class="text-muted mb-4">Thank you for your order. We'll process it shortly and keep you updated!</p>
                <button class="btn btn-lg w-100 ok-btn" 
                        style="background-color: #6b0f0f; color: white;">
                    OK
                </button>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Trigger animation
        setTimeout(() => modal.classList.add('show'), 10);
        
        // OK button handler
        modal.querySelector('.ok-btn').addEventListener('click', () => {
            modal.classList.remove('show');
            setTimeout(() => modal.remove(), 300);
        });
    }

    function closeCheckout() {
        elements.checkoutPopup.classList.remove('active');
        elements.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // === Product Details ===
    function showProductDetails(productCard) {
        const id = parseInt(productCard.dataset.id); // ✅ new line
        const name = productCard.querySelector('.card-title')?.textContent?.trim() || 'Unknown Product';
        const price = productCard.querySelector('strong')?.textContent?.trim() || '₱0.00';
        const desc = productCard.querySelector('.text-muted')?.textContent?.trim() || 'No description available';
        const category = productCard.dataset.cat || 'uncategorized';

        // ✅ include id in currentProduct
        currentProduct = { id, name, price, desc, category };
        currentQuantity = 1;

        // Update sidebar content
        document.getElementById('productTitle').textContent = name;
        document.getElementById('productPrice').textContent = price;
        document.getElementById('productDesc').textContent = desc;

        const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
        document.getElementById('productCategory').textContent = categoryDisplay;

        document.getElementById('productBrand').textContent = 'N/A';
        document.getElementById('productCode').textContent = 'N/A';
        document.getElementById('productWeight').textContent = 'N/A';
        document.getElementById('productSpecs').innerHTML = '<div class="text-muted">No specifications available</div>';

        elements.quantityDisplay.textContent = '1';
        elements.sidebar.classList.add('active');
    }


    function closeSidebar() {
        elements.sidebar.classList.remove('active');
        currentProduct = null;
        currentQuantity = 1;
    }

    // === Quantity Controls ===
    function increaseQuantity() {
        currentQuantity++;
        elements.quantityDisplay.textContent = currentQuantity;
    }

    function decreaseQuantity() {
        if (currentQuantity > 1) {
            currentQuantity--;
            elements.quantityDisplay.textContent = currentQuantity;
        }
    }

    // === Cart Popup ===
    function openCart() {
        updateCartDisplay();
        elements.cartPopup.classList.add('active');
        elements.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCart() {
        elements.cartPopup.classList.remove('active');
        elements.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    async function cartRequest(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);
        for (let key in data) formData.append(key, data[key]);

        const response = await fetch('./api/cart.php', {
            method: 'POST',
            body: formData
        });

        return response.json();
    }


    // === Search and Filter ===
    function applyFilters() {
        const searchTerm = (elements.searchInput.value || '').trim().toLowerCase();
        const activeCategory = elements.chips.find(c => c.classList.contains('active'))?.dataset.cat || 'all';

        elements.productCards.forEach(card => {
            const productName = (card.dataset.name || '').toLowerCase();
            const productCategory = card.dataset.cat || '';
            
            const matchesSearch = !searchTerm || productName.includes(searchTerm);
            const matchesCategory = activeCategory === 'all' || activeCategory === productCategory;
            
            card.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }

    function setActiveCategory(chip) {
        elements.chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        applyFilters();
    }

    // === Event Listeners ===
    function initEventListeners() {
        // Product view buttons
        document.querySelectorAll('.view-product').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const card = e.target.closest('.product-card');
                if (card) showProductDetails(card);
            });
        });

        // Sidebar controls
        document.querySelector('.close-sidebar')?.addEventListener('click', closeSidebar);
        elements.increaseBtn?.addEventListener('click', increaseQuantity);
        elements.decreaseBtn?.addEventListener('click', decreaseQuantity);
        
        elements.addToCartBtn?.addEventListener('click', () => {
            if (currentProduct) {
                addToCart(currentProduct, currentQuantity);
                currentQuantity = 1;
                elements.quantityDisplay.textContent = '1';
            }
        });

        // Cart controls
        elements.mainCartBtn?.addEventListener('click', openCart);
        document.querySelector('.close-cart')?.addEventListener('click', closeCart);
        
        // Checkout controls
        document.querySelector('.proceed-checkout-btn')?.addEventListener('click', proceedToCheckout);
        document.querySelector('.close-checkout')?.addEventListener('click', closeCheckout);
        document.querySelector('.confirm-order-btn')?.addEventListener('click', confirmOrder);
        document.querySelector('.back-to-cart-btn')?.addEventListener('click', () => {
            closeCheckout();
            openCart();
        });
        
        // Overlay closes popups
        elements.overlay?.addEventListener('click', () => {
            closeCart();
            closeCheckout();
            closeSidebar();
        });

        // Search and filter
        elements.searchInput?.addEventListener('input', applyFilters);
        elements.chips.forEach(chip => {
            chip.addEventListener('click', () => setActiveCategory(chip));
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCart();
                closeCheckout();
                closeSidebar();
            }
        });
    }

    // === Initialization ===
    async function init() {
        await loadCartFromDatabase();
        initEventListeners();
        updateCartCount();
        
        // Show welcome message if cart has items
        if (cart.length > 0) {
            console.log(`Welcome back! You have ${cart.length} item(s) in your cart.`);
        }
    }

    // Public API
    return {
        loadCartFromDatabase,
        init,
        getCart: () => [...cart],

        clearCart: async () => {
            try {
            const res = await cartRequest('clear');
            if (res.success) {
                cart = [];
                updateCartDisplay();
                updateCartCount();
                console.log('Cart cleared.');
            } else {
                console.error('Error clearing cart:', res.message);
            }
            } catch (err) {
            console.error('Failed to clear cart:', err);
            }
        }
    };
})();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', CatalogManager.init);
    document.addEventListener('DOMContentLoaded', () => {
        CatalogManager.loadCartFromDatabase();
    });

} else {
    CatalogManager.init();
    document.addEventListener('DOMContentLoaded', () => {
        CatalogManager.loadCartFromDatabase();
    });
}