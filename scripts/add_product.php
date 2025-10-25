<?php
header('Content-Type: application/json');

// only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST allowed']);
    exit;
}

// read JSON body
$raw = file_get_contents('php://input');
$body = json_decode($raw, true);

if (!$body) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON body']);
    exit;
}

$name = trim($body['name'] ?? '');
$price = $body['price'] ?? null;
$qty = $body['qty'] ?? null;
$category = trim($body['category'] ?? '');

if ($name === '' || $category === '' || !is_numeric($price) || !is_numeric($qty)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields']);
    exit;
}

require 'config.php';

try {
    $stmt = $pdo->prepare('INSERT INTO products (name, price, qty, category, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $price, $qty, $category]);

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
