<?php
require_once '../auth_check.php';
require_once '../config.php';

checkAdminAccess();

// Set JSON header and buffer output
header('Content-Type: application/json');
ob_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$category_id = $_POST['category_id'] ?? '';
$product_name = trim($_POST['product_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? '';
$stock_quantity = $_POST['stock_quantity'] ?? '';
$image_url = trim($_POST['image_url'] ?? '');

// Validate required fields
if (empty($category_id) || empty($product_name) || empty($price) || empty($stock_quantity)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

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

    // Get category name for JSON
    $catStmt = $pdo->prepare("SELECT category_name FROM category WHERE category_id = ?");
    $catStmt->execute([$category_id]);
    $category = $catStmt->fetch();

    // Update JSON data
    $jsonProduct = [
        'product_id' => (int)$product_id,
        'product_name' => $product_name,
        'description' => $description,
        'price' => (float)$price,
        'stock_quantity' => (int)$stock_quantity,
        'category' => $category['category_name'],
        'image_url' => $uploaded_image_url ?: ''
    ];
    
    // Update JSON data using JsonDataManager
    $jsonUpdateResult = $jsonManager->addProduct($jsonProduct);

    $pdo->commit();
    
    ob_end_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Item added successfully!',
        'product_id' => $product_id
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error adding item: ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error uploading image: ' . $e->getMessage()]);
}

function handleImageUpload($file, $product_name) {
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Allowed: JPG, PNG, WebP, SVG');
    }

    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../assets/products/img/';
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
?>