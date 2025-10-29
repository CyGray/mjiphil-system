<?php
    require_once 'auth_check.php';
?>

<?php
    $title = "MjiPhil Catalog";
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
        <link rel="stylesheet" href="../components/components-style.css">
    </head>
    
    <body>
        <?php include '../components/menu.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="col-12">
                        <!-- Your catalog content goes here -->
                        <h1>Catalog Page</h1>
                       <!-- Catalog: search, category chips, product grid -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div class="d-flex align-items-center" style="gap:.5rem;">
                            <input id="catalogSearch" class="form-control form-control-sm" style="min-width:220px;" placeholder="Search products here..." />
                            <button class="btn btn-sm d-flex align-items-center gap-2 main-cart-btn" style="background-color: #6b0f0f; color: white;">
                              <i class="bi bi-cart3"></i>
                              <span>Cart</span>
                              <span class="badge bg-light text-dark cart-count" style="display: none;">0</span>
                            </button>
                          </div>
                        </div>

                        <style>
                          /* small local styles for the catalog look */
                          .chip { border-radius: .75rem; padding: .4rem .75rem; border:1px solid #e9e9e9; background:#fff; font-size:.9rem; transition: all 0.2s ease; }
                          .chip.active { background:#6b0f0f; color:#fff; border-color: #6b0f0f; }
                          .chip:hover:not(.active) { border-color: #6b0f0f; color: #6b0f0f; }
                          
                          /* Button success override */
                          .btn-success { background-color: #198754 !important; border-color: #198754 !important; }
                          .product-placeholder { height:120px; border-radius:8px; background:#f6f6f6; display:flex; align-items:center; justify-content:center; color:#bbb; }
                          
                          /* Product Details Sidebar */
                          .product-details-sidebar {
                            position: fixed;
                            top: 0;
                            right: -450px;
                            width: 450px;
                            height: 100vh;
                            background: #fff;
                            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
                            transition: right 0.3s ease;
                            z-index: 1000;
                            padding: 25px;
                            overflow-y: auto;
                          }
                          .product-details-sidebar h5 {
                            color: #6b0f0f;
                            font-weight: 600;
                          }
                          .product-details-sidebar .form-label {
                            margin-bottom: 4px;
                          }
                          .product-info {
                            font-size: 14px;
                          }
                          .product-details-sidebar.active {
                            right: 0;
                          }

                          /* Shopping Cart Popup */
                          .shopping-cart-popup {
                            position: fixed;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            width: 90%;
                            max-width: 600px;
                            background: #fff;
                            box-shadow: 0 0 20px rgba(0,0,0,0.15);
                            z-index: 1001;
                            padding: 20px;
                            display: none;
                            border-radius: 8px;
                          }
                          .shopping-cart-popup.active {
                            display: block;
                          }
                          .overlay {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0,0,0,0.5);
                            z-index: 1000;
                            display: none;
                          }
                          .overlay.active {
                            display: block;
                          }
                        </style>

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
                          // sample static products — backend will replace this later
                          $products = [
                            ['name'=>'ProBuild Premium Cement (50kg)','price'=>'₱280.00','desc'=>'High-grade cement for durable construction.','cat'=>'materials'],
                            ['name'=>'VoltMax Cordless Drill 20V','price'=>'₱3,450.00','desc'=>'Lightweight cordless drill with 2-speed settings.','cat'=>'tools'],
                            ['name'=>'Steel Rebar 16mm (Grade 60)','price'=>'₱750.00','desc'=>'Rust-resistant rebar for reinforced concrete.','cat'=>'materials'],
                            ['name'=>'BuildTuff Hollow Blocks (4x8x16 in)','price'=>'₱18.00','desc'=>'Pre-molded concrete for easy laying.','cat'=>'materials'],
                            ['name'=>'Duracore Safety Helmet — Yellow','price'=>'₱295.00','desc'=>'Impact-resistant hard hat with vents.','cat'=>'safety'],
                            ['name'=>'QuickSeal Waterproofing Paint','price'=>'₱295.00','desc'=>'Long-lasting waterproof finish for roofs and walls.','cat'=>'materials'],
                            ['name'=>'TorquePlus Angle Grinder 4” 750W','price'=>'₱295.00','desc'=>'Compact grinder for cutting and polishing.','cat'=>'tools'],
                            ['name'=>'CemX Ready-Mix Concrete (1m³)','price'=>'₱4,200.00','desc'=>'Pre-mixed concrete for ready pours.','cat'=>'materials'],
                          ];

                          foreach ($products as $p) {
                            ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-4 product-card" 
                                data-name="<?php echo strtolower($p['name']); ?>" 
                                data-cat="<?php echo $p['cat']; ?>"
                                data-price="<?php echo $p['price']; ?>"
                                data-desc="<?php echo $p['desc']; ?>">
                              <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                  <div class="product-placeholder mb-3">
                                    <!-- image placeholder (ignore actual images for now) -->
                                    <i class="bi bi-box-seam" style="font-size:28px;"></i>
                                  </div>
                                  <h6 class="card-title mb-1" style="font-size:.98rem;"><?php echo $p['name']; ?></h6>
                                  <p class="text-muted small mb-3"><?php echo $p['desc']; ?></p>
                                  <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <strong class="text-dark"><?php echo $p['price']; ?></strong>
                                    <button class="btn btn-sm view-product" style="background-color: #6b0f0f; color: white;">View</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          ?>
                        </div>

                        <script>
                          // simple client-side search + category filter
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
                        </script>

                        <!-- Product Details Sidebar -->
                        <div class="product-details-sidebar">
                          <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="m-0">Product Details</h5>
                            <button class="btn btn-sm btn-outline-secondary close-sidebar">
                              <i class="bi bi-x-lg"></i>
                            </button>
                          </div>
                          <div class="product-info">
                            <div class="d-flex gap-3 mb-4">
                              <div class="product-placeholder" style="height:200px; flex: 0 0 200px;">
                                <i class="bi bi-box-seam" style="font-size:48px;"></i>
                              </div>
                              <div class="d-flex flex-column gap-2" style="flex: 1;">
                                <img src="product-thumbnail-1.jpg" class="img-thumbnail" style="width:80px; height:80px; object-fit:cover;">
                                <img src="product-thumbnail-2.jpg" class="img-thumbnail" style="width:80px; height:80px; object-fit:cover;">
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
                                  <div id="productWeight"></div>
                                </div>
                                <div class="col-6">
                                  <label class="form-label small text-muted">Brand</label>
                                  <div id="productBrand"></div>
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
                              <div class="text-muted small" id="productSpecs"></div>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                              <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-outline-secondary rounded-circle decrease-quantity" style="width:32px; height:32px; padding:0;">-</button>
                                <span class="quantity-display">1</span>
                                <button class="btn btn-outline-secondary rounded-circle increase-quantity" style="width:32px; height:32px; padding:0;">+</button>
                              </div>
                              <button class="btn add-to-cart" style="background-color: #6b0f0f; color: white;">Add to Cart</button>
                            </div>
                          </div>
                        </div>

                        <!-- Shopping Cart Popup -->
                        <div class="overlay"></div>
                        <div class="shopping-cart-popup">
                          <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="m-0">Shopping Cart</h5>
                            <button class="btn btn-sm btn-outline-secondary close-cart">
                              <i class="bi bi-x-lg"></i>
                            </button>
                          </div>
                          <div id="cartItems" class="mb-4">
                            <!-- Cart items will be inserted here -->
                          </div>
                          <div class="cart-summary">
                            <div class="d-flex justify-content-between mb-2">
                              <span>Subtotal</span>
                              <span id="subtotal">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                              <span>Shipping</span>
                              <span id="shipping">₱150.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                              <span>Tax (12%)</span>
                              <span id="tax">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                              <strong>Total</strong>
                              <strong id="total">₱0.00</strong>
                            </div>
                            <div class="d-flex gap-2">
                              <button class="btn flex-grow-1" style="background-color: #6b0f0f; color: white;">Proceed to Checkout</button>
                              <button class="btn" style="border-color: #6b0f0f; color: #6b0f0f;">Request for Quotation</button>
                            </div>
                          </div>
                        </div>

                        <script>
                          // Product Details and Shopping Cart functionality
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
                                document.getElementById('productCategory').textContent = currentProduct.category.charAt(0).toUpperCase() + currentProduct.category.slice(1);
                                document.getElementById('productBrand').textContent = getBrandFromName(currentProduct.name);
                                document.getElementById('productCode').textContent = getProductCode(currentProduct.name);
                                document.getElementById('productWeight').textContent = getProductWeight(currentProduct.name);
                                document.getElementById('productSpecs').innerHTML = getTechnicalSpecs(currentProduct.name);
                                
                                sidebar.classList.add('active');
                              });
                            });

                            function getBrandFromName(name) {
                              const brandNames = {
                                'VoltMax': 'VoltMax Tools',
                                'ProBuild': 'ProBuild Construction',
                                'BuildTuff': 'BuildTuff Materials',
                                'Duracore': 'Duracore Safety',
                                'QuickSeal': 'QuickSeal Pro',
                                'TorquePlus': 'TorquePlus Tools',
                                'CemX': 'CemX Construction'
                              };
                              
                              for (const [brand, fullName] of Object.entries(brandNames)) {
                                if (name.includes(brand)) return fullName;
                              }
                              return 'Generic Brand';
                            }

                            function getProductCode(name) {
                              const productCodes = {
                                'ProBuild Premium Cement': 'PBC-5025',
                                'VoltMax Cordless Drill': 'VMT-2043',
                                'Steel Rebar': 'STL-1660',
                                'BuildTuff Hollow Blocks': 'BTH-4816',
                                'Duracore Safety Helmet': 'DSH-1095',
                                'QuickSeal Waterproofing Paint': 'QSW-3072',
                                'TorquePlus Angle Grinder': 'TPG-7504',
                                'CemX Ready-Mix Concrete': 'CRC-1003'
                              };
                              
                              for (const [productName, code] of Object.entries(productCodes)) {
                                if (name.includes(productName)) return `Product Code: #${code}`;
                              }
                              return 'Product Code: #GEN-1000';
                            }

                            function getProductWeight(name) {
                              const weights = {
                                'ProBuild Premium Cement': '50 kg',
                                'VoltMax Cordless Drill': '2.1 kg',
                                'Steel Rebar': '7.4 kg',
                                'BuildTuff Hollow Blocks': '12.7 kg',
                                'Duracore Safety Helmet': '0.4 kg',
                                'QuickSeal Waterproofing Paint': '4 kg',
                                'TorquePlus Angle Grinder': '1.8 kg',
                                'CemX Ready-Mix Concrete': '2400 kg/m³'
                              };
                              
                              for (const [productName, weight] of Object.entries(weights)) {
                                if (name.includes(productName)) return weight;
                              }
                              return '1 kg';
                            }

                            function getTechnicalSpecs(name) {
                              const specs = {
                                'ProBuild Premium Cement': `
                                  <div>Composition: Portland Type 1</div>
                                  <div>Compressive Strength: 28.5 MPa (28 days)</div>
                                  <div>Setting Time: Initial 45 mins</div>
                                  <div>Fineness: 3,250 cm²/g</div>
                                `,
                                'VoltMax Cordless Drill': `
                                  <div>Battery: 20V Li-ion, 4.0Ah</div>
                                  <div>Chuck Size: 13mm Keyless</div>
                                  <div>Speed: 0-450/0-1,800 RPM</div>
                                  <div>Torque Settings: 21+1</div>
                                  <div>LED Work Light: Yes</div>
                                `,
                                'Steel Rebar': `
                                  <div>Grade: 60 (420 MPa)</div>
                                  <div>Diameter: 16mm</div>
                                  <div>Length: 6 meters</div>
                                  <div>Yield Strength: 420 MPa min</div>
                                `,
                                'BuildTuff Hollow Blocks': `
                                  <div>Dimensions: 4" x 8" x 16"</div>
                                  <div>Compression Strength: 350 psi</div>
                                  <div>Water Absorption: <12%</div>
                                  <div>Shell Thickness: 25mm</div>
                                `,
                                'Duracore Safety Helmet': `
                                  <div>Material: High-density polyethylene</div>
                                  <div>Impact Rating: Type I, Class E</div>
                                  <div>Suspension: 4-point</div>
                                  <div>Certification: ANSI Z89.1-2014</div>
                                `,
                                'QuickSeal Waterproofing Paint': `
                                  <div>Coverage: 25-30 m² per 4kg</div>
                                  <div>Drying Time: 2-4 hours</div>
                                  <div>Recoat Time: 6-8 hours</div>
                                  <div>VOC Content: <50 g/L</div>
                                `,
                                'TorquePlus Angle Grinder': `
                                  <div>Power: 750W</div>
                                  <div>Disc Size: 4" (100mm)</div>
                                  <div>No-Load Speed: 12,000 RPM</div>
                                  <div>Spindle Thread: M10</div>
                                `,
                                'CemX Ready-Mix Concrete': `
                                  <div>Strength Class: C25/30</div>
                                  <div>Slump: S3 (100-150mm)</div>
                                  <div>Maximum Aggregate: 20mm</div>
                                  <div>Cement Content: 350 kg/m³</div>
                                `
                              };
                              
                              for (const [productName, specification] of Object.entries(specs)) {
                                if (name.includes(productName)) return specification;
                              }
                              return '<div>Specifications not available</div>';
                            }

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
                        </script>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>