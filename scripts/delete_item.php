<?php
require_once '../auth_check.php';
require_once '../config.php';

checkAdminAccess();

// Set JSON header first
header('Content-Type: application/json');

// Buffer output to prevent any accidental output
ob_start();

// Debug logging - use error_log only for API endpoints
error_log("DELETE ITEM REQUEST RECEIVED - Product ID: " . ($_POST['product_id'] ?? 'unknown'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    
    error_log("Delete request for product ID: " . $product_id);
    
    if (empty($product_id)) {
        error_log("DELETE FAILED: Product ID is empty");
        ob_end_clean(); // Clear any output
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        error_log("Database transaction started");
        
        // Delete from product_image first (due to foreign key constraints)
        $stmt = $pdo->prepare("DELETE FROM product_image WHERE product_id = ?");
        $stmt->execute([$product_id]);
        error_log("Deleted from product_image - Rows affected: " . $stmt->rowCount());
        
        // Delete from inventory
        $stmt = $pdo->prepare("DELETE FROM inventory WHERE product_id = ?");
        $stmt->execute([$product_id]);
        error_log("Deleted from inventory - Rows affected: " . $stmt->rowCount());
        
        // Delete from product
        $stmt = $pdo->prepare("DELETE FROM product WHERE product_id = ?");
        $stmt->execute([$product_id]);
        error_log("Deleted from product - Rows affected: " . $stmt->rowCount());
        
        // Delete from JSON
        error_log("Attempting to delete from JSON - Product ID: $product_id");
        $jsonDeleteResult = $jsonManager->deleteProduct($product_id);
        
        if ($jsonDeleteResult) {
            error_log("JSON deletion SUCCESSFUL");
        } else {
            error_log("JSON deletion FAILED - product may not exist in JSON");
        }
        
        $pdo->commit();
        error_log("Database transaction committed successfully");
        
        ob_end_clean(); // Clear any output
        echo json_encode([
            'success' => true, 
            'message' => 'Item deleted successfully from database and JSON',
            'json_deleted' => $jsonDeleteResult
        ]);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("DELETE OPERATION FAILED - Database error: " . $e->getMessage());
        ob_end_clean(); // Clear any output
        echo json_encode(['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()]);
    }
} else {
    error_log("DELETE FAILED: Invalid request method - " . $_SERVER['REQUEST_METHOD']);
    ob_end_clean(); // Clear any output
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>