<?php
require_once '../auth_check.php';
require_once '../config.php';

checkAdminAccess();

header('Content-Type: application/json');
ob_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = $_POST['product_id'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$product_name = trim($_POST['product_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? '';
$stock_quantity = $_POST['stock_quantity'] ?? '';
$image_url = trim($_POST['image_url'] ?? '');

if (empty($product_id) || empty($category_id) || empty($product_name) || empty($price) || empty($stock_quantity)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

try {
    $pdo->beginTransaction();

    $uploaded_image_url = $image_url;
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $uploaded_image_url = handleImageUpload($_FILES['product_image'], $product_name);
    }

    $stmt = $pdo->prepare("UPDATE product SET category_id = ?, product_name = ?, description = ?, price = ? WHERE product_id = ?");
    $stmt->execute([$category_id, $product_name, $description, $price, $product_id]);

    $stmt = $pdo->prepare("UPDATE inventory SET stock_quantity = ? WHERE product_id = ?");
    $stmt->execute([$stock_quantity, $product_id]);

    if (!empty($uploaded_image_url)) {
        $checkStmt = $pdo->prepare("SELECT image_id FROM product_image WHERE product_id = ?");
        $checkStmt->execute([$product_id]);
        $existingImage = $checkStmt->fetch();
        
        if ($existingImage) {
            $stmt = $pdo->prepare("UPDATE product_image SET image_url = ? WHERE product_id = ?");
            $stmt->execute([$uploaded_image_url, $product_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO product_image (product_id, image_url) VALUES (?, ?)");
            $stmt->execute([$product_id, $uploaded_image_url]);
        }
    }

    $catStmt = $pdo->prepare("SELECT category_name FROM category WHERE category_id = ?");
    $catStmt->execute([$category_id]);
    $category = $catStmt->fetch();

    $jsonProduct = [
        'product_id' => (int)$product_id,
        'product_name' => $product_name,
        'description' => $description,
        'price' => (float)$price,
        'stock_quantity' => (int)$stock_quantity,
        'category' => $category['category_name'],
        'image_url' => $uploaded_image_url ?: ''
    ];
    
    $jsonUpdateResult = $jsonManager->updateProduct($product_id, $jsonProduct);

    $pdo->commit();
    
    ob_end_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Item updated successfully!'
    ]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error uploading image: ' . $e->getMessage()]);
}

function handleImageUpload($file, $product_name) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Allowed: JPG, PNG, WebP, SVG');
    }

    $upload_dir = __DIR__ . '/../assets/products/img/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    $base_name = !empty($product_name) ? $product_name : $original_name;
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base_name);
    $filename = $safe_name . '.' . $extension;
    
    $file_path = $upload_dir . $filename;

    $counter = 1;
    while (file_exists($file_path)) {
        $filename = $safe_name . '_' . $counter . '.' . $extension;
        $file_path = $upload_dir . $filename;
        $counter++;
    }

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to move uploaded file');
    }

    return './assets/products/img/' . $filename;
}
?>
