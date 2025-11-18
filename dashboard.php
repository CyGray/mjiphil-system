<?php
require_once 'auth_check.php';
require_once './config.php';

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['name'] ?? 'User';
$firstName = explode(' ', $userName)[0];

$orderQuery = $pdo->prepare("
    SELECT o.order_id, o.order_date, o.total_amount, s.status_name,
           (SELECT p.product_name 
            FROM order_item oi 
            JOIN product p ON oi.product_id = p.product_id 
            WHERE oi.order_id = o.order_id LIMIT 1) AS first_item
    FROM `order` o
    JOIN order_status s ON o.order_status_id = s.order_status_id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
    LIMIT 5
");
$orderQuery->execute([$user_id]);
$orders = $orderQuery->fetchAll(PDO::FETCH_ASSOC);


$cartQuery = $pdo->prepare("
    SELECT p.product_name, p.price, ci.quantity
    FROM cart_item ci
    JOIN cart c ON ci.cart_id = c.cart_id
    JOIN product p ON ci.product_id = p.product_id
    WHERE c.user_id = ?
");
$cartQuery->execute([$user_id]);
$cartItems = $cartQuery->fetchAll(PDO::FETCH_ASSOC);

$cartSubtotal = 0;
foreach ($cartItems as $item) {
    $cartSubtotal += $item['price'] * $item['quantity'];
}
include("./utils/alert.php");
?>

<?php $title = "MJIPhil Dashboard"; ?>

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
        <style>
            a.btn-view {
            text-decoration: none !important;
            color: white !important;
            }
        </style>


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
                            <div class="product-grid" id="featuredProducts">
                                <!-- Products will be loaded here via JavaScript -->
                                <div class="loading-spinner">Loading featured products...</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sidebar-section">
                            <h3>Your Recent Orders</h3>
                            <div class="orders-list">
                                <?php if (empty($orders)): ?>
                                    <div class="text-muted small">No recent orders found.</div>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <div class="order-item mb-3 p-2 border rounded">
                                            <div class="order-header d-flex justify-content-between">
                                                <span class="fw-bold">Order #<?php echo $order['order_id']; ?></span>
                                                <span class="text-muted small">
                                                    <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                                </span>
                                            </div>
                                            <div class="order-details text-truncate small text-muted">
                                                <?php echo htmlspecialchars($order['first_item']); ?>
                                            </div>
                                            <div class="order-footer d-flex justify-content-between align-items-center mt-2">
                                                <span class="fw-bold text-danger">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                                <span class="badge bg-secondary"><?php echo $order['status_name']; ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Live Cart -->
                        <div class="sidebar-section mt-4">
                            <h3>Your Cart</h3>
                            <?php if (empty($cartItems)): ?>
                                <div class="cart-empty text-center">
                                    <img src="./assets/emptycart.png" alt="Empty Cart" width="80">
                                    <p class="text-muted mt-2 mb-1">Your cart is currently empty.</p>
                                    <a href="catalog.php" class="btn-shop-now-small btn btn-sm btn-dark mt-2">Shop Now</a>
                                </div>
                            <?php else: ?>
                                <div class="cart-items">
                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="cart-item d-flex justify-content-between align-items-center border-bottom py-2">
                                            <div>
                                                <strong class="d-block" style="font-size:0.9rem;">
                                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                                </strong>
                                                <small class="text-muted">
                                                    ₱<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?>
                                                </small>
                                            </div>
                                            <span class="fw-bold text-danger">
                                                ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="cart-total mt-3 d-flex justify-content-between">
                                        <span class="fw-bold">Subtotal:</span>
                                        <span class="fw-bold text-dark">₱<?php echo number_format($cartSubtotal, 2); ?></span>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="catalog.php" class="btn btn-sm btn-outline-dark">Continue Shopping</a>
                                        <a href="catalog.php#checkout" class="btn-view">Checkout</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="scripts/dashboard.js"></script>
    </body>
</html>
