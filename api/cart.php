<?php
require_once '../config.php';

header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Log to file for debugging
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) mkdir($logDir, 0777, true);
$logFile = $logDir . '/cart-api.log';

// Capture all PHP errors
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($logFile) {
    $msg = "[Error] [$errno] $errstr in $errfile:$errline\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
});
set_exception_handler(function($e) use ($logFile) {
    $msg = "[Exception] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal Server Error',
        'error' => $e->getMessage()
    ]);
    exit;
});

if (session_status() === PHP_SESSION_NONE) session_start();
file_put_contents($logFile, "SESSION DATA: " . print_r($_SESSION, true) . "\n", FILE_APPEND);


// Log incoming request
file_put_contents($logFile, "=== New Request at " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents($logFile, "GET: " . print_r($_GET, true) . "\n\n", FILE_APPEND);


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
// Auto-create cart for user if not exists
$stmt = $pdo->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch();

if (!$cart) {
    $pdo->prepare("INSERT INTO cart (user_id) VALUES (?)")->execute([$user_id]);
    $cart_id = $pdo->lastInsertId();
} else {
    $cart_id = $cart['cart_id'];
}

// Handle actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        $stmt = $pdo->prepare("
            SELECT ci.cart_item_id, p.product_id, p.product_name, p.price, ci.quantity
            FROM cart_item ci
            JOIN product p ON ci.product_id = p.product_id
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cart_id]);
        echo json_encode(['success' => true, 'cart' => $stmt->fetchAll()]);
        break;

    case 'add':
        $product_id = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
            exit;
        }

        // Check if exists
        $stmt = $pdo->prepare("SELECT quantity FROM cart_item WHERE cart_id = ? AND product_id = ?");
        $stmt->execute([$cart_id, $product_id]);
        $item = $stmt->fetch();

        if ($item) {
            // Update existing
            $newQty = $item['quantity'] + $quantity;
            $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND product_id = ?")
                ->execute([$newQty, $cart_id, $product_id]);
        } else {
            // Insert new
            $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, quantity) VALUES (?, ?, ?)")
                ->execute([$cart_id, $product_id, $quantity]);
        }

        echo json_encode(['success' => true, 'message' => 'Item added']);
        break;

    case 'update':
        $product_id = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }
        $pdo->prepare("UPDATE cart_item SET quantity = ? WHERE cart_id = ? AND product_id = ?")
            ->execute([$quantity, $cart_id, $product_id]);
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);
        break;

    case 'delete':
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
            exit;
        }
        $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ? AND product_id = ?")
            ->execute([$cart_id, $product_id]);
        echo json_encode(['success' => true, 'message' => 'Item removed']);
        break;

    case 'clear':
        $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ?")->execute([$cart_id]);
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        break;

    case 'sync':
        $jsonCart = json_decode($_POST['cart'] ?? '[]', true);
        if (!is_array($jsonCart)) {
            echo json_encode(['success' => false, 'message' => 'Invalid cart format']);
            exit;
        }

        // Clear old items
        $pdo->prepare("DELETE FROM cart_item WHERE cart_id = ?")->execute([$cart_id]);

        // Reinsert new ones
        $stmt = $pdo->prepare("INSERT INTO cart_item (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($jsonCart as $item) {
            if (!empty($item['id']) && !empty($item['quantity'])) {
                $stmt->execute([$cart_id, $item['id'], $item['quantity']]);
            }
        }

        echo json_encode(['success' => true, 'message' => 'Cart synced']);
        break;



    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
} catch (Throwable $e) {
    file_put_contents($logFile, "[Catch] " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}

?>
