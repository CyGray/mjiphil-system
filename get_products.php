<?php
    header('Content-Type: application/json');
    require 'config.php';

    try {
        $stmt = $pdo->query('SELECT id, name, price, qty, category, created_at FROM products ORDER BY id DESC');
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
    }
?>

