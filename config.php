<?php
// config.php - DB connection settings
$DB_HOST = '127.0.0.1';
$DB_NAME = 'mjiphil_db';
$DB_USER = 'root';
$DB_PASS = ''; // default XAMPP empty password; change if you set one

// PDO connection - exceptions mode
try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
