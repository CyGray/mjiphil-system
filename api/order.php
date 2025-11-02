<?php
// ✅ 1. Setup logging IMMEDIATELY
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) mkdir($logDir, 0777, true);
$logFile = $logDir . '/order-api.log';

// Capture all PHP errors early
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($logFile) {
    file_put_contents($logFile, "[Error] $errstr in $errfile:$errline\n", FILE_APPEND);
});
set_exception_handler(function($e) use ($logFile) {
    file_put_contents($logFile, "[Exception] " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    exit;
});

// ✅ 2. Now continue with your app logic
require_once '../config.php';
header('Content-Type: application/json');
session_start();

// ✅ 3. Log basic request context for every call
file_put_contents($logFile, "\n=== Request @ " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "SESSION: " . print_r($_SESSION, true) . "\n\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    file_put_contents($logFile, "[Warn] Not logged in.\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}


$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        /**
         * 🧾 CREATE ORDER
         */
        case 'create':
            // Get cart items
            $cartQuery = $pdo->prepare("
                SELECT ci.product_id, ci.quantity, p.price
                FROM cart_item ci
                JOIN cart c ON c.cart_id = ci.cart_id
                JOIN product p ON ci.product_id = p.product_id
                WHERE c.user_id = ?
            ");
            $cartQuery->execute([$user_id]);
            $items = $cartQuery->fetchAll(PDO::FETCH_ASSOC);

            if (!$items) {
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                exit;
            }

            // Compute totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = $subtotal * 0.12;
            $shipping = 150;
            $total = $subtotal + $tax + $shipping;

            // Start transaction
            $pdo->beginTransaction();

            // Insert into `order`
            $stmt = $pdo->prepare("
                INSERT INTO `order` (user_id, order_status_id, total_amount)
                VALUES (?, (SELECT order_status_id FROM order_status WHERE status_name='Pending' LIMIT 1), ?)
            ");
            $stmt->execute([$user_id, $total]);
            $order_id = $pdo->lastInsertId();

            // Insert items
            $itemStmt = $pdo->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, unit_price, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");
            foreach ($items as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $itemSubtotal]);
            }

            // Clear cart
            $pdo->prepare("DELETE FROM cart_item WHERE cart_id = (SELECT cart_id FROM cart WHERE user_id = ?)")->execute([$user_id]);

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order_id
            ]);
            break;

        /**
         * 📋 GET USER ORDERS
         */
        case 'list':
            $stmt = $pdo->prepare("
                SELECT o.order_id, o.order_date, o.total_amount, s.status_name
                FROM `order` o
                JOIN order_status s ON o.order_status_id = s.order_status_id
                WHERE o.user_id = ?
                ORDER BY o.order_date DESC
            ");
            $stmt->execute([$user_id]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Include top item per order for preview
            foreach ($orders as &$order) {
                $itemStmt = $pdo->prepare("
                    SELECT p.product_name 
                    FROM order_item oi 
                    JOIN product p ON oi.product_id = p.product_id 
                    WHERE oi.order_id = ? 
                    LIMIT 1
                ");
                $itemStmt->execute([$order['order_id']]);
                $preview = $itemStmt->fetchColumn();
                $order['first_item'] = $preview ?: 'No items';
            }

            echo json_encode(['success' => true, 'orders' => $orders]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
