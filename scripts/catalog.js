class CatalogManager {
    constructor() {
        this.products = [];
        this.filteredProducts = [];
        this.currentCategory = '';
        this.currentSort = 'name';
        this.searchTerm = '';
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];

        this.init();
    }

    init() {
        this.loadProducts();
        this.setupEventListeners();
        this.updateCartBadge();
    }

    async loadProducts() {
        try {
            this.showLoading(true);
            const response = await fetch('./api/get_products.php');
            const data = await response.json();

            if (data.success) {
                this.products = data.products;
                this.filterAndRender();
            } else {
                throw new Error(data.message || 'Failed to load products');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError('Failed to load products');
        } finally {
            this.showLoading(false);
        }
    }

    filterAndRender() {
        // Filter by search term
        this.filteredProducts = this.products.filter(product => {
            const matchesSearch = !this.searchTerm || 
                product.product_name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                product.description.toLowerCase().includes(this.searchTerm.toLowerCase());
            
            const matchesCategory = !this.currentCategory || 
                product.category_name === this.currentCategory;

            return matchesSearch && matchesCategory;
        });

        // Sort products
        this.sortProducts();

        // Render products
        this.renderProducts();
    }

    sortProducts() {
        switch (this.currentSort) {
            case 'price_low':
                this.filteredProducts.sort((a, b) => a.price - b.price);
                break;
            case 'price_high':
                this.filteredProducts.sort((a, b) => b.price - a.price);
                break;
            case 'name':
            default:
                this.filteredProducts.sort((a, b) => a.product_name.localeCompare(b.product_name));
                break;
        }
    }

    renderProducts() {
        const container = document.getElementById('productsContainer');
        
        if (this.filteredProducts.length === 0) {
            document.getElementById('noProducts').style.display = 'block';
            container.innerHTML = '';
            return;
        }

        document.getElementById('noProducts').style.display = 'none';

        container.innerHTML = this.filteredProducts.map(product => `
            <div class="product-card" data-product-id="${product.product_id}">
                <div class="product-image">
                    ${product.image_url ? 
                        `<img src="${product.image_url}" alt="${product.product_name}" onerror="this.style.display='none'">` : 
                        '<div class="no-image"><i class="fas fa-box"></i></div>'
                    }
                    ${product.stock_quantity === 0 ? '<div class="out-of-stock">Out of Stock</div>' : ''}
                </div>
                <div class="product-info">
                    <h3 class="product-name">${product.product_name}</h3>
                    <p class="product-description">${product.description}</p>
                    <div class="product-meta">
                        <span class="product-price">₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        <span class="product-stock">Stock: ${product.stock_quantity}</span>
                    </div>
                    <div class="product-actions">
                        <button class="btn-view" onclick="catalogManager.viewProduct(${product.product_id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-cart ${product.stock_quantity === 0 ? 'disabled' : ''}" 
                                onclick="catalogManager.addToCart(${product.product_id})" 
                                ${product.stock_quantity === 0 ? 'disabled' : ''}>
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    viewProduct(productId) {
        const product = this.products.find(p => p.product_id === productId);
        if (!product) return;

        document.getElementById('modalProductName').textContent = product.product_name;
        document.getElementById('modalProductDescription').textContent = product.description;
        document.getElementById('modalProductPrice').textContent = `₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('modalProductStock').textContent = `Stock: ${product.stock_quantity}`;
        
        const imageElement = document.getElementById('modalProductImage');
        if (product.image_url) {
            imageElement.src = product.image_url;
            imageElement.style.display = 'block';
        } else {
            imageElement.style.display = 'none';
        }

        document.getElementById('quantity').value = 1;
        document.getElementById('quantity').max = product.stock_quantity;

        // Update add to cart button
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (product.stock_quantity === 0) {
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Out of Stock';
        } else {
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Add to Cart';
            addToCartBtn.onclick = () => this.addToCartFromModal(productId);
        }

        const modal = new bootstrap.Modal(document.getElementById('productModal'));
        modal.show();
    }

    addToCart(productId) {
        this.addToCartAction(productId, 1);
    }

    addToCartFromModal(productId) {
        const quantity = parseInt(document.getElementById('quantity').value);
        this.addToCartAction(productId, quantity);
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
        modal.hide();
    }

    addToCartAction(productId, quantity) {
        const product = this.products.find(p => p.product_id === productId);
        if (!product || product.stock_quantity === 0) return;

        const existingItem = this.cart.find(item => item.product_id === productId);
        
        if (existingItem) {
            if (existingItem.quantity + quantity > product.stock_quantity) {
                alert(`Cannot add more than available stock. Available: ${product.stock_quantity}`);
                return;
            }
            existingItem.quantity += quantity;
        } else {
            this.cart.push({
                product_id: productId,
                product_name: product.product_name,
                price: product.price,
                image_url: product.image_url,
                quantity: quantity,
                max_quantity: product.stock_quantity
            });
        }

        this.saveCart();
        this.updateCartBadge();
        this.showToast('Product added to cart!');
    }

    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
    }

    updateCartBadge() {
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        // You can update a cart badge element here if you have one
        console.log('Cart updated:', totalItems, 'items');
    }

    showToast(message) {
        // Simple toast notification - you can enhance this with a proper toast library
        alert(message); // Replace with proper toast implementation
    }

    showLoading(show) {
        document.getElementById('loadingSpinner').style.display = show ? 'flex' : 'none';
    }

    showError(message) {
        // You can implement a proper error display
        console.error(message);
        alert(message);
    }

    setupEventListeners() {
        // Search input
        document.getElementById('searchInput').addEventListener('input', (e) => {
            this.searchTerm = e.target.value;
            this.filterAndRender();
        });

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', (e) => {
            this.currentCategory = e.target.value;
            this.filterAndRender();
        });

        // Sort filter
        document.getElementById('sortFilter').addEventListener('change', (e) => {
            this.currentSort = e.target.value;
            this.filterAndRender();
        });
    }
}

// Initialize catalog manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.catalogManager = new CatalogManager();
});