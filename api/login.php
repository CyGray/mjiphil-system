<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $decodedPassword = base64_decode($user['password']);

    if ($decodedPassword === $password) {
        // ✅ Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['first_name'];

        // ✅ Debug log
        $log = __DIR__ . '/../logs/login.log';
        file_put_contents($log, "[" . date('Y-m-d H:i:s') . "] Login OK: {$user['email']} (ID={$user['user_id']})\n", FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'role' => $user['role'],
            'user_id' => $user['user_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
