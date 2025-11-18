          document.querySelector('.search-box input').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const products = document.querySelectorAll('.product-card');
                
                products.forEach(product => {
                    const title = product.querySelector('h4').textContent.toLowerCase();
                    const description = product.querySelector('p').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });

            document.querySelectorAll('.btn-view').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productTitle = this.closest('.product-card').querySelector('h4').textContent;
                    window.location.href = 'catalog.php';
                });
            });

            document.querySelectorAll('.btn-view-details').forEach(btn => {
                btn.addEventListener('click', function() {
                    const orderNumber = this.closest('.order-item').querySelector('.order-number').textContent;
                    window.location.href = 'order-history.php';
                });
            });

            // Load featured products
async function loadFeaturedProducts() {
    try {
        const response = await fetch('./api/get_featured.php');
        const data = await response.json();
        
        const productsContainer = document.getElementById('featuredProducts');
        
        if (data.success && data.products.length > 0) {
            productsContainer.innerHTML = data.products.map(product => `
                <div class="product-card">
                    <img src="${product.image_url || './assets/products/placeholder.jpg'}" 
                         alt="${product.product_name}" 
                         class="product-image"
                         onerror="this.src='./assets/products/placeholder.jpg'">
                    <h4>${product.product_name}</h4>
                    <p>${product.description || 'No description available'}</p>
                    <div class="product-footer">
                        <span class="product-price">₱${parseFloat(product.price).toFixed(2)}</span>
                        <button class="btn-view" onclick="viewProduct(${product.product_id})">View</button>
                    </div>
                </div>
            `).join('');
        } else {
            productsContainer.innerHTML = `
                <div class="col-12 text-center">
                    <p class="text-muted">No featured products available at the moment.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
        document.getElementById('featuredProducts').innerHTML = `
            <div class="col-12 text-center">
                <p class="text-danger">Failed to load featured products.</p>
            </div>
        `;
    }
}

        // View product function
        function viewProduct(productId) {
            window.location.href = `product-details.php?id=${productId}`;
        }

        // Load featured products when page loads
        document.addEventListener('DOMContentLoaded', loadFeaturedProducts);