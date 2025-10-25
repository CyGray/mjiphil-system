<?php
require_once 'auth_check.php';
checkAdminAccess(); // This will redirect non-admin users to catalog.php

require_once 'config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        $category_id = $_POST['category_id'] ?? '';
        $product_name = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? '';
        $stock_quantity = $_POST['stock_quantity'] ?? '';
        $image_url = trim($_POST['image_url'] ?? '');

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Insert into product table
            $stmt = $pdo->prepare("INSERT INTO product (category_id, product_name, description, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category_id, $product_name, $description, $price]);
            $product_id = $pdo->lastInsertId();

            // Insert into inventory table
            $stmt = $pdo->prepare("INSERT INTO inventory (product_id, stock_quantity) VALUES (?, ?)");
            $stmt->execute([$product_id, $stock_quantity]);

            // Insert image if provided
            if (!empty($image_url)) {
                $stmt = $pdo->prepare("INSERT INTO product_image (product_id, image_url) VALUES (?, ?)");
                $stmt->execute([$product_id, $image_url]);
            }

            $pdo->commit();
            $_SESSION['success'] = "Item added successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error adding item: " . $e->getMessage();
        }
        
        header("Location: inventory.php");
        exit;
    }
}

// Get categories for dropdown
$categories = ["Tools", "Materials"];
try {
    $stmt = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error loading categories: " . $e->getMessage();
}

// Get inventory items with search, filter, and sort
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$type_filter = $_GET['type'] ?? '';
$sort_by = $_GET['sort'] ?? 'product_id';
$sort_order = $_GET['order'] ?? 'asc';

// Build query
$query = "SELECT p.product_id, p.product_name, p.description, p.price, 
                 c.category_name, i.stock_quantity,
                 pi.image_url,
                 COALESCE(SUM(oi.quantity), 0) as monthly_sales
          FROM product p
          LEFT JOIN category c ON p.category_id = c.category_id
          LEFT JOIN inventory i ON p.product_id = i.product_id
          LEFT JOIN product_image pi ON p.product_id = pi.product_id
          LEFT JOIN order_item oi ON p.product_id = oi.product_id
          LEFT JOIN `order` o ON oi.order_id = o.order_id AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
          WHERE 1=1";

$params = [];

// Add search filter
if (!empty($search)) {
    $query .= " AND (p.product_name LIKE ? OR p.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

// Add category filter
if (!empty($category_filter)) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

// Add type filter (if you have a type field)
if (!empty($type_filter)) {
    $query .= " AND c.category_name = ?";
    $params[] = $type_filter;
}

// Group by and order by
$query .= " GROUP BY p.product_id ORDER BY $sort_by $sort_order";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $inventory_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error loading inventory: " . $e->getMessage();
    $inventory_items = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - MJI PHIL Construction</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="./styles/inventory.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo">MJI</div>
            <span class="company-name">MJIPHIL CONSTRUCTION</span>
        </div>
        <div class="header-right">
            <i class="fas fa-bars menu-icon"></i>
            <form method="GET" action="inventory.php" class="search-header">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search Products here" id="headerSearch" value="<?php echo htmlspecialchars($search); ?>">
            </form>
            <div class="filter-icon" onclick="toggleFilterModal()">
                <i class="fas fa-filter"></i>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <div class="sidebar">
            <div class="sidebar-item">
                <i class="fas fa-th-large"></i>
                <span><a href="catalog.php" style="color: inherit; text-decoration: none;">CATALOG</a></span>
            </div>
            <div class="sidebar-item active">
                <i class="fas fa-box"></i>
                <span>INVENTORY</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-lock"></i>
                <span>PRIVACY</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-cog"></i>
                <span>SERVICES</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-info-circle"></i>
                <span>ABOUT US</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-file-alt"></i>
                <span>INFORMATION</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-share-alt"></i>
                <span>SOCIAL MEDIA</span>
            </div>
            <div class="logout-section">
                <div class="logout-item" onclick="logout()">
                    <i class="fas fa-user-circle"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <div class="content-area">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <h1 class="page-title">INVENTORY</h1>

            <div class="controls-bar">
                <div class="controls-left">
                    <form method="GET" action="inventory.php" class="d-flex gap-2">
                        <select name="category" class="btn btn-outline" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php echo ($category_filter == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="type" class="btn btn-outline" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Tools" <?php echo ($type_filter == 'Tools') ? 'selected' : ''; ?>>Tools</option>
                            <option value="Materials" <?php echo ($type_filter == 'Materials') ? 'selected' : ''; ?>>Materials</option>
                            <option value="Safety Gear" <?php echo ($type_filter == 'Safety Gear') ? 'selected' : ''; ?>>Safety Gear</option>
                        </select>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
                <div class="controls-right">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                    <button class="btn btn-outline" onclick="openBulkUpload()">
                        <i class="fas fa-upload"></i> Bulk Upload
                    </button>
                    <div class="view-toggle">
                        <button class="active">
                            <i class="fas fa-list"></i>
                        </button>
                        <button>
                            <i class="fas fa-th"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th onclick="sortTable('product_id')">ID <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('product_name')">Item Name <i class="fas fa-sort"></i></th>
                            <th>Image</th>
                            <th onclick="sortTable('price')">Price <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('stock_quantity')">Quantity <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('monthly_sales')">Monthly Sales <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('category_name')">Category <i class="fas fa-sort"></i></th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php if (empty($inventory_items)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No items found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventory_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>
                                        <div class="item-image">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <i class="fas fa-image"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>PHP <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['stock_quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($item['monthly_sales']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                    <td>
                                        <div class="action-icons">
                                            <i class="fas fa-pen" onclick="editItem(<?php echo $item['product_id']; ?>)"></i>
                                            <i class="fas fa-trash-alt" onclick="deleteItem(<?php echo $item['product_id']; ?>)"></i>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal" id="addItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add an Item</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addItemForm">
                    <input type="hidden" name="add_item" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" name="product_name" placeholder="Product name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" placeholder="Product description" rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" placeholder="Quantity" min="0" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image URL (optional)</label>
                            <input type="text" class="form-control" name="image_url" placeholder="Image URL">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Add Item
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="./scripts/inventory.js"></script>
    <script>
        function sortTable(column) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentOrder = url.searchParams.get('order');
            
            let newOrder = 'asc';
            if (currentSort === column && currentOrder === 'asc') {
                newOrder = 'desc';
            }
            
            url.searchParams.set('sort', column);
            url.searchParams.set('order', newOrder);
            window.location.href = url.toString();
        }

        function openAddModal() {
            document.getElementById('addItemModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('addItemModal').classList.remove('show');
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addItemModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Auto-submit search when typing stops
        let searchTimeout;
        document.getElementById('headerSearch').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    </script>
</body>
</html>