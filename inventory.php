<?php
require_once 'auth_check.php';
checkAdminAccess();
require_once 'config.php';

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

            // Handle file upload
            $uploaded_image_url = $image_url; // Default to manual URL input
            
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded_image_url = handleImageUpload($_FILES['product_image'], $product_name);
            }

            // Insert into product table
            $stmt = $pdo->prepare("INSERT INTO product (category_id, product_name, description, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$category_id, $product_name, $description, $price]);
            $product_id = $pdo->lastInsertId();

            // Insert into inventory table
            $stmt = $pdo->prepare("INSERT INTO inventory (product_id, stock_quantity) VALUES (?, ?)");
            $stmt->execute([$product_id, $stock_quantity]);

            // Insert image if provided
            if (!empty($uploaded_image_url)) {
                $stmt = $pdo->prepare("INSERT INTO product_image (product_id, image_url) VALUES (?, ?)");
                $stmt->execute([$product_id, $uploaded_image_url]);
            }

            // Get category name for JSON (Placeholder for JSON logic)
            $catStmt = $pdo->prepare("SELECT category_name FROM category WHERE category_id = ?");
            $catStmt->execute([$category_id]);
            $category = $catStmt->fetch();

            // Save to JSON (Assuming $jsonManager is defined elsewhere and handles JSON operations)
            // $jsonProduct = [
            //     'product_name' => $product_name,
            //     'description' => $description,
            //     'price' => (float)$price,
            //     'stock_quantity' => (int)$stock_quantity,
            //     'category' => $category['category_name'],
            //     'image_url' => $uploaded_image_url
            // ];
            // $jsonManager->addProduct($jsonProduct); 

            $pdo->commit();
            $_SESSION['success'] = "Item added successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error adding item: " . $e->getMessage();
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error uploading image: " . $e->getMessage();
        }
        
        header("Location: inventory.php");
        exit;
    }

    // In the edit section of inventory.php, update this part:

if (isset($_POST['edit_item'])) {
    $product_id = $_POST['product_id'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $product_name = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock_quantity = $_POST['stock_quantity'] ?? '';
    $image_url = trim($_POST['image_url'] ?? '');

    // Debug logging
    error_log("EDIT OPERATION STARTED - Product ID: $product_id");
    echo "<script>console.log('EDIT OPERATION STARTED - Product ID: $product_id');</script>";

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Handle file upload
        $uploaded_image_url = $image_url;
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded_image_url = handleImageUpload($_FILES['product_image'], $product_name);
            error_log("File uploaded: $uploaded_image_url");
        }

        // Update product table
        $stmt = $pdo->prepare("UPDATE product SET category_id = ?, product_name = ?, description = ?, price = ? WHERE product_id = ?");
        $stmt->execute([$category_id, $product_name, $description, $price, $product_id]);
        error_log("Database product updated - Rows affected: " . $stmt->rowCount());

        // Update inventory table
        $stmt = $pdo->prepare("UPDATE inventory SET stock_quantity = ? WHERE product_id = ?");
        $stmt->execute([$stock_quantity, $product_id]);
        error_log("Database inventory updated - Rows affected: " . $stmt->rowCount());

        // Update or insert image
        if (!empty($uploaded_image_url)) {
            // Check if image already exists
            $checkStmt = $pdo->prepare("SELECT image_id FROM product_image WHERE product_id = ?");
            $checkStmt->execute([$product_id]);
            $existingImage = $checkStmt->fetch();
            
            if ($existingImage) {
                // Update existing image
                $stmt = $pdo->prepare("UPDATE product_image SET image_url = ? WHERE product_id = ?");
                $stmt->execute([$uploaded_image_url, $product_id]);
                error_log("Database image updated");
            } else {
                // Insert new image
                $stmt = $pdo->prepare("INSERT INTO product_image (product_id, image_url) VALUES (?, ?)");
                $stmt->execute([$product_id, $uploaded_image_url]);
                error_log("Database image inserted");
            }
        }

        // Get category name for JSON
        $catStmt = $pdo->prepare("SELECT category_name FROM category WHERE category_id = ?");
        $catStmt->execute([$category_id]);
        $category = $catStmt->fetch();
        error_log("Category retrieved: " . ($category['category_name'] ?? 'unknown'));

        // Update JSON - FIXED: Use JsonDataManager to update JSON data
        $jsonProduct = [
            'product_id' => (int)$product_id, // Ensure product_id is included
            'product_name' => $product_name,
            'description' => $description,
            'price' => (float)$price,
            'stock_quantity' => (int)$stock_quantity,
            'category' => $category['category_name'],
            'image_url' => $uploaded_image_url ?: '' // Use uploaded image or empty string
        ];
                
        // Update JSON data using JsonDataManager
        $jsonUpdateResult = $jsonManager->updateProduct($product_id, $jsonProduct);
        
        if ($jsonUpdateResult) {
            error_log("JSON update SUCCESSFUL");
            echo "<script>console.log('JSON update SUCCESSFUL for product ID: $product_id');</script>";
        } else {
            error_log("JSON update FAILED");
            echo "<script>console.log('JSON update FAILED for product ID: $product_id');</script>";
        }

        $pdo->commit();
        $_SESSION['success'] = "Item updated successfully!";
        error_log("EDIT OPERATION COMPLETED SUCCESSFULLY");
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating item: " . $e->getMessage();
        error_log("EDIT OPERATION FAILED - Database error: " . $e->getMessage());
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error uploading image: " . $e->getMessage();
        error_log("EDIT OPERATION FAILED - Image error: " . $e->getMessage());
    }
    
    header("Location: inventory.php");
    exit;
}
}

function handleImageUpload($file, $product_name) {
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Allowed: JPG, PNG, WebP, SVG');
    }

    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/assets/products/img/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Generate safe filename
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Use product name for filename, fallback to original name
    $base_name = !empty($product_name) ? $product_name : $original_name;
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base_name);
    $filename = $safe_name . '.' . $extension;
    
    $file_path = $upload_dir . $filename;

    // Ensure unique filename
    $counter = 1;
    while (file_exists($file_path)) {
        $filename = $safe_name . '_' . $counter . '.' . $extension;
        $file_path = $upload_dir . $filename;
        $counter++;
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    // Return relative path for database storage
    return './assets/products/img/' . $filename;
}

// Get categories for dropdown
$categories = [];
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

// Add type filter
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
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/components.css">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="./styles/inventory_f.css" rel="stylesheet">
</head>
<body>
    <div class="app-container">
        
        <?php include 'menu.php'; ?>

        <main class="main-content">
            
            <div class="top-bar-new">
               <div class="search-actions-container">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Search by name or ID..." value="<?php echo htmlspecialchars($search); ?>">
                        
                        <button class="btn-filter-icon" onclick="toggleFilterModal()">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                    
                    <div class="top-bar-actions">
                        <button class="btn-add" onclick="openAddModal()">
                            <i class="fas fa-plus"></i>
                            Add Item
                        </button>
                        <button class="btn-upload" onclick="openBulkUpload()">
                            <i class="fas fa-upload"></i>
                            Bulk Upload
                        </button>
                    </div>
                </div>
            </div> 
            
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

            <div class="controls-wrapper-new">
                
               <div class="top-controls-row">
                    <div class="filters-row-new">
                        <select class="select-filter-new" name="type" onchange="applyFilter(this, 'type')">
                            <option value="">Type: All</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                    <?php echo ($type_filter == $category['category_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="select-filter-new">
                            <option>Low Stock</option>
                            <option>High Stock</option>
                        </select>
                        <select class="select-filter-new">
                            <option>High Demand</option>
                            <option>Low Demand</option>
                        </select>
                    </div>

                    <div class="view-switcher-new">
                        <button class="view-btn active">
                            <i class="fas fa-list"></i>
                        </button>
                        <button class="view-btn">
                            <i class="fas fa-th-large"></i>
                       </button>
                    </div>
                </div>

                <div class="tabs-new">
                    <button class="tab-new active">All</button>
                    <button class="tab-new">Brgy. Bata</button>
                </div>

            </div> 
            
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th onclick="sortTable('product_id')">ID <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('product_name')">ITEM NAME <i class="fas fa-sort"></i></th>
                            <th>IMAGE </th>
                            <th onclick="sortTable('price')">PRICE <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('stock_quantity')">QUANTITY <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('monthly_sales')">MONTHLY SALES <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('category_name')">TYPE <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('description')">DESCRIPTION <i class="fas fa-sort"></i></th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventory_items)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem; color: #999;">No items found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventory_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>
                                        <div class="table-image">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                            <?php else: ?>
                                                <i class="fas fa-image"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>PHP <?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['stock_quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($item['monthly_sales']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($item['description'], 0, 30)) . (strlen($item['description']) > 30 ? '...' : ''); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action" title="Edit Item" onclick="editItem(<?php echo $item['product_id']; ?>)">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <button class="btn-action" title="Delete Item" onclick="deleteItem(<?php echo $item['product_id']; ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div class="modal" id="addItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add an Item</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="addItemForm" enctype="multipart/form-data">
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
                    </div>

                    <div class="form-group">
                        <label class="form-label">Product Image</label>
                        <div class="file-upload-container">
                            <div class="file-upload-area" id="fileUploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <span>PNG, JPG, JPEG, WebP, SVG (Max: 5MB)</span>
                                <input type="file" id="product_image" name="product_image" accept=".png,.jpg,.jpeg,.webp,.svg" style="display: none;">
                            </div>
                            <div id="filePreview" class="file-preview" style="display: none;">
                                <img id="previewImage" src="" alt="Preview">
                                <button type="button" onclick="removeImage()" class="btn-remove-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Or enter Image URL (optional)</label>
                        <input type="text" class="form-control" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Add Item
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal" id="editItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Edit Item</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="editItemForm" enctype="multipart/form-data">
                    <input type="hidden" name="edit_item" value="1">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    
                    <div class="form-group">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" name="product_name" id="edit_product_name" placeholder="Product name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" placeholder="Product description" rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" id="edit_price" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" id="edit_stock_quantity" placeholder="Quantity" min="0" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select class="form-control" name="category_id" id="edit_category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Product Image</label>
                        <div class="file-upload-container">
                            <div class="file-upload-area" id="editFileUploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload or drag and drop</p>
                                <span>PNG, JPG, JPEG, WebP, SVG (Max: 5MB)</span>
                                <input type="file" id="edit_product_image" name="product_image" accept=".png,.jpg,.jpeg,.webp,.svg" style="display: none;">
                            </div>
                            <div id="editFilePreview" class="file-preview" style="display: none;">
                                <img id="editPreviewImage" src="" alt="Preview">
                                <button type="button" onclick="removeEditImage()" class="btn-remove-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Or enter Image URL (optional)</label>
                        <input type="text" class="form-control" name="image_url" id="edit_image_url" placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="form-buttons">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
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

        function applyFilter(select, filterType) {
            const url = new URL(window.location.href);
            url.searchParams.set(filterType, select.value);
            window.location.href = url.toString();
        }

        function openAddModal() {
            document.getElementById('addItemModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('addItemModal').classList.remove('show');
        }

        function openBulkUpload() {
            alert('Bulk upload functionality');
        }

        function toggleFilterModal() {
            alert('Filter modal functionality');
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
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', this.value);
                window.location.href = url.toString();
            }, 500);
        });
    </script>
</body>
</html>