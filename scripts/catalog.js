(function() {
    const sidebar = document.querySelector('.product-details-sidebar');
    const cartPopup = document.querySelector('.shopping-cart-popup');
    const overlay = document.querySelector('.overlay');
    const quantityDisplay = document.querySelector('.quantity-display');
    const addToCartBtn = document.querySelector('.add-to-cart');
    const increaseBtn = document.querySelector('.increase-quantity');
    const decreaseBtn = document.querySelector('.decrease-quantity');
    const mainCartBtn = document.querySelector('.main-cart-btn');
    const cartCountBadge = document.querySelector('.cart-count');
    let currentProduct = null;
    let cart = [];
    let currentQuantity = 1;

    // Function to update cart count badge
    function updateCartCount() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountBadge.textContent = totalItems;
        cartCountBadge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }

    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = '';
        
        let subtotal = 0;
        cart.forEach(item => {
            const price = parseFloat(item.price.replace('₱', '').replace(',', ''));
            subtotal += price * item.quantity;
            
            cartItems.innerHTML += `
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <div class="product-placeholder" style="width:60px; height:60px;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">${item.name}</h6>
                                <div class="text-muted small">₱${price.toLocaleString()} x ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold mb-1">₱${(price * item.quantity).toLocaleString()}</div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-name="${item.name}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        const shipping = 120;
        const tax = subtotal * 0.12;
        const total = subtotal + shipping + tax;

        document.getElementById('subtotal').textContent = `₱${subtotal.toLocaleString()}`;
        document.getElementById('shipping').textContent = `₱${shipping.toLocaleString()}`;
        document.getElementById('tax').textContent = `₱${tax.toLocaleString()}`;
        document.getElementById('total').textContent = `₱${total.toLocaleString()}`;

        // Add remove functionality
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const name = e.currentTarget.dataset.name;
                cart = cart.filter(item => item.name !== name);
                updateCart();
            });
        });
    }

    // Quantity control handlers
    increaseBtn.addEventListener('click', () => {
        currentQuantity++;
        quantityDisplay.textContent = currentQuantity.toString();
    });

    decreaseBtn.addEventListener('click', () => {
        if (currentQuantity > 1) {
            currentQuantity--;
            quantityDisplay.textContent = currentQuantity.toString();
        }
    });

    function updateCartDisplay() {
        const cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = '';
        
        let subtotal = 0;
        cart.forEach(item => {
            const price = parseFloat(item.price.replace('₱', '').replace(',', ''));
            subtotal += price * item.quantity;
            
            cartItems.innerHTML += `
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <div class="product-placeholder" style="width:60px; height:60px;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">${item.name}</h6>
                                <div class="text-muted small">₱${price.toLocaleString()} x ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold mb-1">₱${(price * item.quantity).toLocaleString()}</div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-name="${item.name}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        const shipping = 150;
        const tax = subtotal * 0.12;
        const total = subtotal + shipping + tax;

        document.getElementById('subtotal').textContent = `₱${subtotal.toLocaleString()}`;
        document.getElementById('shipping').textContent = `₱${shipping.toLocaleString()}`;
        document.getElementById('tax').textContent = `₱${tax.toLocaleString()}`;
        document.getElementById('total').textContent = `₱${total.toLocaleString()}`;

        // Add remove functionality
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const name = e.currentTarget.dataset.name;
                cart = cart.filter(item => item.name !== name);
                updateCartDisplay();
            });
        });
    }

    // Product Details
    document.querySelectorAll('.view-product').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.product-card');
            currentProduct = {
                name: card.querySelector('.card-title').textContent,
                price: card.querySelector('strong').textContent,
                desc: card.querySelector('.text-muted').textContent,
                category: card.dataset.cat,
                quantity: 1
            };
            
            // Reset and show controls
            currentQuantity = 1;
            quantityDisplay.textContent = '1';
            addToCartBtn.style.display = 'block';
            
            document.getElementById('productTitle').textContent = currentProduct.name;
            document.getElementById('productPrice').textContent = currentProduct.price;
            document.getElementById('productDesc').textContent = currentProduct.desc;
            
            // Set category with fallback
            document.getElementById('productCategory').textContent = currentProduct.category ? 
                currentProduct.category.charAt(0).toUpperCase() + currentProduct.category.slice(1) : 
                '<None>';
            
            // Set brand as <None> (not in database)
            document.getElementById('productBrand').textContent = '<None>';
            
            // Set product code as <None> (not in database)
            document.getElementById('productCode').textContent = '<None>';
            
            // Set weight as <None> (not in database)
            document.getElementById('productWeight').textContent = '<None>';
            
            // Set technical specs as <None> (not in database)
            document.getElementById('productSpecs').innerHTML = '<div><None></div>';
            
            sidebar.classList.add('active');
        });
    });

    // Close sidebar
    document.querySelector('.close-sidebar').addEventListener('click', () => {
        sidebar.classList.remove('active');
    });

    // Add to cart
    document.querySelector('.add-to-cart').addEventListener('click', () => {
        if (currentProduct) {
            const existingItem = cart.find(item => item.name === currentProduct.name);
            if (existingItem) {
                existingItem.quantity += currentQuantity;
            } else {
                cart.push({...currentProduct, quantity: currentQuantity});
            }

            // Show success feedback
            const originalText = addToCartBtn.textContent;
            addToCartBtn.textContent = 'Added to Cart!';
            addToCartBtn.classList.add('btn-success');
            setTimeout(() => {
                addToCartBtn.textContent = originalText;
                addToCartBtn.classList.remove('btn-success');
            }, 2000);

            // Update cart, badge and reset quantity
            updateCart();
            updateCartCount();
            currentQuantity = 1;
            quantityDisplay.textContent = '1';
        }
    });

    // View cart from main button
    mainCartBtn.addEventListener('click', () => {
        updateCart();
        cartPopup.classList.add('active');
        overlay.classList.add('active');
    });

    // Close cart
    document.querySelector('.close-cart').addEventListener('click', () => {
        cartPopup.classList.remove('active');
        overlay.classList.remove('active');
    });

    // Function to handle item removal
    function removeFromCart(name) {
        cart = cart.filter(item => item.name !== name);
        updateCart();
        updateCartCount();
        
        // If cart is empty, close the popup
        if (cart.length === 0) {
            cartPopup.classList.remove('active');
            overlay.classList.remove('active');
        }
    }

    function updateCart() {
        const cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = '';

        let subtotal = 0;
        cart.forEach(item => {
            const price = parseFloat(item.price.replace('₱', '').replace(',', ''));
            subtotal += price * item.quantity;

            cartItems.innerHTML += `
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-3">
                            <div class="product-placeholder" style="width:60px; height:60px;">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">${item.name}</h6>
                                <div class="text-muted small">₱${price.toLocaleString()} x ${item.quantity}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold mb-1">₱${(price * item.quantity).toLocaleString()}</div>
                            <button class="btn btn-sm btn-outline-danger remove-item" data-name="${item.name}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        const shipping = 150;
        const tax = subtotal * 0.12;
        const total = subtotal + shipping + tax;

        document.getElementById('subtotal').textContent = `₱${subtotal.toLocaleString()}`;
        document.getElementById('shipping').textContent = `₱${shipping.toLocaleString()}`;
        document.getElementById('tax').textContent = `₱${tax.toLocaleString()}`;
        document.getElementById('total').textContent = `₱${total.toLocaleString()}`;

        // Update cart count badge to keep badge in sync
        updateCartCount();

        // Add remove functionality (use removeFromCart to keep behavior consistent)
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const name = e.currentTarget.dataset.name;
                removeFromCart(name);
            });
        });
    }
})();

(function(){
    const search = document.getElementById('catalogSearch');
    const chips = Array.from(document.querySelectorAll('.chip'));
    const cards = Array.from(document.querySelectorAll('.product-card'));

    function applyFilter() {
        const q = (search.value||'').trim().toLowerCase();
        const activeCat = chips.find(c=>c.classList.contains('active'))?.dataset.cat || 'all';
        cards.forEach(card => {
            const name = card.dataset.name || '';
            const cat = card.dataset.cat || '';
            const matchName = !q || name.indexOf(q) !== -1;
            const matchCat = activeCat === 'all' || activeCat === cat;
            card.style.display = (matchName && matchCat) ? '' : 'none';
        });
    }

    search.addEventListener('input', applyFilter);
    chips.forEach(c => {
        c.addEventListener('click', () => {
            chips.forEach(x=>x.classList.remove('active'));
            c.classList.add('active');
            applyFilter();
        });
    });
})();