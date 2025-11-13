<?php
session_start();
require_once '../config.php'; // your PDO connection

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

    // Decode stored base64 password
    $decodedPassword = base64_decode($user['password']);

    if ($decodedPassword === $password) {
        // ✅ Store user info in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['first_name'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'role' => $user['role']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
