<?php
    require_once 'auth_check.php';
    
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
    $firstName = explode(' ', $userName)[0];
?>

<?php
    $title = "MjiPhil Dashboard";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Cutive+Mono&family=Monomaniac+One&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="./styles/components.css">
        <link rel="stylesheet" href="./styles/dashboard.css">
    </head>
    
    <body>
        <?php include './menu.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="hero-banner">
                    <img src="./assets/banner.png" alt="MjiPhil Construction Supplies" class="banner-image">
                    <div class="banner-overlay">
                        <div class="banner-content">
                            <h1>MJIPhil Construction Supplies</h1>
                            <p>Quality Tools and Materials for your construction needs</p>
                            <a href="catalog.php" class="btn-shop-now">Shop Now</a>
                        </div>
                    </div>
                </div>

                <div class="dashboard-content">
                    <div class="row">
                    <div class="col-lg-8">
                        <div class="welcome-section">
                            <h2>Welcome, <?php echo htmlspecialchars($firstName); ?></h2>
                            <p>What do you want to do today?</p>
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" class="form-control" placeholder="Search Products here...">
                            </div>
                        </div>

                        <div class="featured-products">
                            <h3>Featured Products</h3>
                            <div class="product-grid">

                                <div class="product-card">
                                    <img src="./assets/products/portland-cement.jpg" alt="Portland Cement" class="product-image">
                                    <h4>Portland Premium Cement (50kg)</h4>
                                    <p>High-quality cement for construction projects</p>
                                    <div class="product-footer">
                                        <span class="product-price">₱285.00</span>
                                        <button class="btn-view">View</button>
                                    </div>
                                </div>

                                <div class="product-card">
                                    <img src="./assets/products/drill.jpg" alt="Cordless Drill" class="product-image">
                                    <h4>VidMac Cordless Drill 20V</h4>
                                    <p>Powerful and lightweight drill with a 20V battery</p>
                                    <div class="product-footer">
                                        <span class="product-price">₱3,450.00</span>
                                        <button class="btn-view">View</button>
                                    </div>
                                </div>

                                <div class="product-card">
                                    <img src="./assets/products/steel-bars.jpg" alt="Steel Rebar" class="product-image">
                                    <h4>Steel Rebar 16mm (Grade 60)</h4>
                                    <p>High-grade reinforcement bars for concrete structures</p>
                                    <div class="product-footer">
                                        <span class="product-price">₱750.00</span>
                                        <button class="btn-view">View</button>
                                    </div>
                                </div>

                                <div class="product-card">
                                    <img src="./assets/products/hollow-blocks.jpg" alt="Hollow Blocks" class="product-image">
                                    <h4>BuildFill Hollow Blocks (4x8x16 in)</h4>
                                    <p>Standard concrete hollow blocks</p>
                                    <div class="product-footer">
                                        <span class="product-price">₱18.00</span>
                                        <button class="btn-view">View</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sidebar-section">
                            <h3>Your Orders</h3>
                            <div class="orders-list">
                                <div class="order-item">
                                    <div class="order-header">
                                        <span class="order-number">Order #1055</span>
                                        <span class="order-date">October 17, 2025</span>
                                    </div>
                                    <div class="order-details">Steel Rebar 16mm (Grade 60)</div>
                                    <div class="order-footer">
                                        <span class="order-price">₱750.00</span>
                                        <button class="btn-view-details">View Details</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="sidebar-section">
                            <h3>Your Cart</h3>
                            <div class="cart-empty">
                                <img src="./assets/icons/hard-hat.png" alt="Empty Cart">
                                <p>Your cart is currently empty.</p>
                                <a href="catalog.php" class="btn-shop-now-small">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
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
        </script>
    </body>
</html>
