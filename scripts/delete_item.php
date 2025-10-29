<?php
require_once 'auth_check.php';
checkAdminAccess();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    
    if (empty($product_id)) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Delete from product_image first (due to foreign key constraints)
        $stmt = $pdo->prepare("DELETE FROM product_image WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        // Delete from inventory
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        // Delete from product
        $stmt = $pdo->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>