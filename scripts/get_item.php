<?php
require_once '../auth_check.php';
require_once '../config.php';

checkAdminAccess();

// Set JSON header and buffer output
header('Content-Type: application/json');
ob_start();

$product_id = $_GET['product_id'] ?? '';

if (empty($product_id)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    // Get product details with category and inventory
    $query = "SELECT p.product_id, p.product_name, p.description, p.price, 
                     p.category_id, c.category_name,
                     i.stock_quantity, pi.image_url
              FROM product p
              LEFT JOIN category c ON p.category_id = c.category_id
              LEFT JOIN inventory i ON p.product_id = i.product_id
              LEFT JOIN product_image pi ON p.product_id = pi.product_id
              WHERE p.product_id = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$product_id]);
    $item = $stmt->fetch();
    
    if ($item) {
        ob_end_clean();
        echo json_encode([
            'success' => true, 
            'item' => $item
        ]);
    } else {
        ob_end_clean();
        echo json_encode([
            'success' => false, 
            'message' => 'Item not found'
        ]);
    }
    
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false, 
        'message' => 'Error loading item: ' . $e->getMessage()
    ]);
}
?>