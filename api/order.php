<?php
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) mkdir($logDir, 0777, true);
$logFile = $logDir . '/order-api.log';

set_error_handler(function($errno, $errstr, $errfile, $errline) use ($logFile) {
    file_put_contents($logFile, "[Error] $errstr in $errfile:$errline\n", FILE_APPEND);
});

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

file_put_contents($logFile, "\n=== ORDER API Request @ " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "SESSION user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    file_put_contents($logFile, "[ERROR] User not logged in\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

file_put_contents($logFile, "Processing action: $action for user_id: $user_id\n", FILE_APPEND);

try {
    switch ($action) {
        case 'create':
            $cartStmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
            $cartStmt->execute([$user_id]);
            $cart = $cartStmt->fetch();
            
            if (!$cart) {
                file_put_contents($logFile, "[ERROR] No cart found for user $user_id\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => 'No cart found']);
                exit;
            }
            
            $cart_id = $cart['cart_id'];
            file_put_contents($logFile, "Found cart_id: $cart_id for user $user_id\n", FILE_APPEND);
            
            $cartQuery = $pdo->prepare("
                SELECT ci.product_id, ci.quantity, p.price, p.product_name, 
                       COALESCE(i.stock_quantity, 0) as stock_quantity
                FROM cart_item ci
                JOIN product p ON ci.product_id = p.product_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE ci.cart_id = ?
            ");
            $cartQuery->execute([$cart_id]);
            $items = $cartQuery->fetchAll(PDO::FETCH_ASSOC);

            file_put_contents($logFile, "Cart items found: " . count($items) . "\n", FILE_APPEND);
            file_put_contents($logFile, "Items: " . print_r($items, true) . "\n", FILE_APPEND);

            if (empty($items)) {
                file_put_contents($logFile, "[ERROR] Cart is empty\n", FILE_APPEND);
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                exit;
            }

            foreach ($items as $item) {
                if ($item['stock_quantity'] < $item['quantity']) {
                    $errorMsg = "Insufficient stock for {$item['product_name']}. Available: {$item['stock_quantity']}, Requested: {$item['quantity']}";
                    file_put_contents($logFile, "[ERROR] $errorMsg\n", FILE_APPEND);
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = $subtotal * 0.12;
            $shipping = 150;
            $total = $subtotal + $tax + $shipping;

            file_put_contents($logFile, "Totals - Subtotal: $subtotal, Tax: $tax, Shipping: $shipping, Total: $total\n", FILE_APPEND);

            $pdo->beginTransaction();

            try {
                $orderStmt = $pdo->prepare("
                    INSERT INTO `order` (user_id, order_status_id, total_amount)
                    VALUES (?, (SELECT order_status_id FROM order_status WHERE status_name='Pending' LIMIT 1), ?)
                ");
                $orderStmt->execute([$user_id, $total]);
                $order_id = $pdo->lastInsertId();
                
                file_put_contents($logFile, "Order created with ID: $order_id\n", FILE_APPEND);

                $itemStmt = $pdo->prepare("
                    INSERT INTO order_item (order_id, product_id, quantity, unit_price, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $inventoryStmt = $pdo->prepare("
                    UPDATE inventory SET stock_quantity = stock_quantity - ? 
                    WHERE product_id = ?
                ");

                foreach ($items as $item) {
                    $itemSubtotal = $item['price'] * $item['quantity'];
                    file_put_contents($logFile, "Adding order item: Product {$item['product_id']}, Qty {$item['quantity']}, Price {$item['price']}\n", FILE_APPEND);
                    
                    $itemStmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $itemSubtotal]);
                    
                    file_put_contents($logFile, "Updating inventory for product {$item['product_id']}: reducing by {$item['quantity']}\n", FILE_APPEND);
                    $inventoryStmt->execute([$item['quantity'], $item['product_id']]);
                    
                    if ($inventoryStmt->rowCount() === 0) {
                        file_put_contents($logFile, "[WARNING] Inventory update affected 0 rows for product {$item['product_id']}\n", FILE_APPEND);
                    }
                }

                $clearStmt = $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ?");
                $clearStmt->execute([$cart_id]);
                $deletedRows = $clearStmt->rowCount();
                file_put_contents($logFile, "Cleared cart: $deletedRows items removed\n", FILE_APPEND);

                $pdo->commit();
                file_put_contents($logFile, "ORDER SUCCESSFULLY CREATED - Order ID: $order_id\n", FILE_APPEND);

                echo json_encode([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order_id' => $order_id
                ]);

            } catch (Exception $e) {
                $pdo->rollBack();
                file_put_contents($logFile, "[ERROR] Transaction failed: " . $e->getMessage() . "\n", FILE_APPEND);
                throw $e;
            }
            break;

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
            file_put_contents($logFile, "[ERROR] Invalid action: $action\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Throwable $e) {
    file_put_contents($logFile, "[FATAL ERROR] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}
?>
