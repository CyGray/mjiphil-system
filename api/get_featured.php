<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';

if (!isset($pdo)) {
    die("Database connection failed");
}

header('Content-Type: application/json');

try {
    $featuredQuery = $pdo->prepare("
        SELECT DISTINCT 
            p.product_id,
            p.product_name,
            p.description,
            p.price,
            pi.image_url,
            COALESCE(SUM(oi.quantity), 0) as monthly_sales
        FROM product p
        LEFT JOIN product_image pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN order_item oi ON p.product_id = oi.product_id
        LEFT JOIN `order` o ON oi.order_id = o.order_id 
            AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        WHERE p.is_active = 1
        GROUP BY p.product_id, p.product_name, p.description, p.price, pi.image_url
        ORDER BY monthly_sales DESC, p.product_id ASC
        LIMIT 4
    ");
    
    $featuredQuery->execute();
    $featuredProducts = $featuredQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'products' => $featuredProducts
    ]);

} catch (PDOException $e) {
    error_log("Featured products error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load featured products',
        'error' => $e->getMessage()
    ]);
}
?>
