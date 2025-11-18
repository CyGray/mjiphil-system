<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link
            href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Cutive+Mono&family=Monomaniac+One&display=swap"
            rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="./styles/components.css">
        <link rel="stylesheet" href="./styles/catalog.css">
        <?php
            require_once 'auth_check.php';
            $title = "MjiPhil Catalog";
            require_once 'config.php';
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Not logged in']);
                exit;
            }

            $user_id = $_SESSION['user_id'];
            try {
                $query = "SELECT p.product_id, p.product_name, p.description, p.price, 
                                    c.category_name, c.category_id,
                                    pi.image_url,
                                    i.stock_quantity
                            FROM product p
                            INNER JOIN category c ON p.category_id = c.category_id
                            LEFT JOIN product_image pi ON p.product_id = pi.product_id
                            LEFT JOIN inventory i ON p.product_id = i.product_id
                            WHERE i.stock_quantity > 0
                            ORDER BY p.product_name";

                $stmt = $pdo->query($query);
                $products = $stmt->fetchAll();
                $categorized_products = [];
                foreach ($products as $product) {
                    $categorized_products[$product['category_name']][] = $product;
                }

            } catch (PDOException $e) {
                error_log("Error loading products: " . $e->getMessage());
                $categorized_products = [];
            }
            try {
                $stmt = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name");
                $categories = $stmt->fetchAll();
            } catch (PDOException $e) {
                error_log("Error loading categories: " . $e->getMessage());
                $categories = [];
            }
            include("utils/alert.php");
        ?>
    </head>

    <body>
        <?php include './menu.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center" style="gap:.5rem;">
                                    <input id="catalogSearch" class="form-control form-control-sm" style="min-width:220px;"
                                        placeholder="Search products here..." />
                                    <button class="btn btn-sm d-flex align-items-center gap-2 main-cart-btn"
                                        style="background-color: #6b0f0f; color: white;">
                                        <i class="bi bi-cart3"></i>
                                        <span>Cart</span>
                                        <span class="badge bg-light text-dark cart-count" style="display: none;">0</span>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="chip active" data-cat="all">All items</button>
                                    <button class="chip" data-cat="tools">Tools and Equipment</button>
                                    <button class="chip" data-cat="materials">Building Materials</button>
                                    <button class="chip" data-cat="plumbing">Plumbing Supplies</button>
                                    <button class="chip" data-cat="safety">Safety Gear</button>
                                </div>
                            </div>

                            <div class="row" id="productGrid">
                                <?php
                                try {
                                    $categoryMap = [
                                        'Tools' => 'tools',
                                        'Materials' => 'materials',
                                        'Safety Gear' => 'safety',
                                        'Essentials' => 'materials',
                                        'Electrical' => 'tools',
                                        'Plumbing' => 'materials',
                                        'Hardware' => 'tools',
                                        'Paint & Supplies' => 'materials',
                                        'Concrete & Masonry' => 'materials',
                                        'Lumber & Wood' => 'materials'
                                    ];

                                    foreach ($products as $p) {
                                        $catSlug = $categoryMap[$p['category_name']] ?? 'materials';
                                        $imageUrl = !empty($p['image_url']) ? $p['image_url'] : '';
                                        ?>
                                <div class="col-6 col-md-4 col-lg-3 mb-4 product-card"
                                    data-id="<?php echo $p['product_id']; ?>"
                                    data-name="<?php echo strtolower($p['product_name']); ?>"
                                    data-cat="<?php echo $catSlug; ?>" data-price="<?php echo $p['price']; ?>"
                                    data-desc="<?php echo htmlspecialchars($p['description']); ?>"
                                    data-image="<?php echo htmlspecialchars($imageUrl); ?>">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body d-flex flex-column">
                                            <div class="product-image-container mb-3"
                                                style="height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 8px; background: #f8f9fa;">
                                                <?php if (!empty($imageUrl)): ?>
                                                <img src="<?php echo htmlspecialchars($imageUrl); ?>"
                                                    alt="<?php echo htmlspecialchars($p['product_name']); ?>"
                                                    style="max-height: 100%; max-width: 100%; object-fit: contain;"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="product-placeholder"
                                                    style="display: none; flex-direction: column; align-items: center; justify-content: center; height: 100%; width: 100%;">
                                                    <i class="bi bi-box-seam" style="font-size:28px; color: #6c757d;"></i>
                                                    <small class="text-muted mt-1">No Image</small>
                                                </div>
                                                <?php else: ?>
                                                <div class="product-placeholder"
                                                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; width: 100%;">
                                                    <i class="bi bi-box-seam" style="font-size:28px; color: #6c757d;"></i>
                                                    <small class="text-muted mt-1">No Image</small>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="card-title mb-1" style="font-size:.98rem;">
                                                <?php echo htmlspecialchars($p['product_name']); ?>
                                            </h6>
                                            <p class="text-muted small mb-3">
                                                <?php echo htmlspecialchars($p['description']); ?>
                                            </p>
                                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                                <strong
                                                    class="text-dark">₱<?php echo number_format($p['price'], 2); ?></strong>
                                                <button class="btn btn-sm view-product"
                                                    style="background-color: #6b0f0f; color: white;">View</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    }

                                } catch (PDOException $e) {
                                    error_log("Error loading products: " . $e->getMessage());
                                    echo '<div class="col-12 text-center text-muted">Unable to load products at this time.</div>';
                                }
                                ?>
                            </div>
                            <div class="product-details-sidebar">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="m-0">Product Details</h5>
                                    <button class="btn btn-sm btn-outline-secondary close-sidebar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="product-info">
                                    <div class="d-flex gap-3 mb-4">
                                        <div class="main-product-image"
                                            style="height:200px; flex: 0 0 200px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                            <img id="mainProductImage" src="" alt="Product Image"
                                                style="max-height: 100%; max-width: 100%; object-fit: contain; display: none;">
                                            <div class="product-placeholder"
                                                style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                                <i class="bi bi-box-seam" style="font-size:48px; color: #6c757d;"></i>
                                                <small class="text-muted mt-2">No Image Available</small>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column gap-2" style="flex: 1;">
                                            <div class="thumbnail-placeholder img-thumbnail"
                                                style="width:80px; height:80px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                                <i class="bi bi-image" style="font-size:20px; color: #6c757d;"></i>
                                            </div>
                                            <div class="thumbnail-placeholder img-thumbnail"
                                                style="width:80px; height:80px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                                <i class="bi bi-image" style="font-size:20px; color: #6c757d;"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h4 id="productTitle" class="mb-1"></h4>
                                        <div class="text-muted small mb-2" id="productCode"></div>
                                    </div>

                                    <div class="mb-4">
                                        <h5 class="mb-2">Price</h5>
                                        <h3 id="productPrice" class="text-dark mb-0"></h3>
                                    </div>

                                    <div class="mb-4">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label small text-muted">Availability</label>
                                                <div class="text-success">In stock</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small text-muted">Weight</label>
                                                <div id="productWeight">N/A</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small text-muted">Brand</label>
                                                <div id="productBrand">MJI Phil</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small text-muted">Category</label>
                                                <div id="productCategory"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-2">Product Description</h6>
                                        <p id="productDesc" class="text-muted small"></p>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-2">Technical Specifications</h6>
                                        <div class="text-muted small" id="productSpecs">
                                            <div>• High-quality construction materials</div>
                                            <div>• Durable and long-lasting</div>
                                            <div>• Industry standard compliant</div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-outline-secondary rounded-circle decrease-quantity"
                                                style="width:32px; height:32px; padding:0;">-</button>
                                            <span class="quantity-display">1</span>
                                            <button class="btn btn-outline-secondary rounded-circle increase-quantity"
                                                style="width:32px; height:32px; padding:0;">+</button>
                                        </div>
                                        <button class="btn add-to-cart" style="background-color: #6b0f0f; color: white;">
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="overlay"></div>
                            <div class="shopping-cart-popup">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <h5 class="m-0">Shopping Cart</h5>
                                    <button class="btn btn-sm btn-outline-secondary close-cart">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div id="cartItems" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                                </div>
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold">Subtotal</span>
                                        <span class="fw-bold" id="cartSubtotal">₱0.00</span>
                                    </div>
                                    <button class="btn w-100 proceed-checkout-btn"
                                        style="background-color: #6b0f0f; color: white;" disabled>
                                        Proceed to Checkout
                                    </button>
                                </div>
                            </div>
                            <div class="checkout-popup">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                    <h5 class="m-0">Order Summary</h5>
                                    <button class="btn btn-sm btn-outline-secondary close-checkout">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="mb-4">
                                    <h6 class="mb-3">Order Items</h6>
                                    <div id="orderItemsList" style="max-height: 250px; overflow-y: auto;">
                                    </div>
                                </div>
                                <div class="border-top pt-3 mb-4">
                                    <h6 class="mb-3">Payment Summary</h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span id="checkoutSubtotal">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping Fee</span>
                                        <span id="checkoutShipping">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                        <span class="text-muted">Tax (12%)</span>
                                        <span id="checkoutTax">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="fw-bold fs-5">Total</span>
                                        <span class="fw-bold fs-5 text-danger" id="checkoutTotal">₱0.00</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary back-to-cart-btn" style="flex: 1;">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Cart
                                    </button>
                                    <button class="btn confirm-order-btn"
                                        style="background-color: #6b0f0f; color: white; flex: 2;">
                                        <i class="bi bi-check-circle me-2"></i>Confirm Order
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src='./scripts/catalog.js'></script>
    </body>
</html>