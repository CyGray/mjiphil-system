<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB connection settings
$DB_HOST = '127.0.0.1';
$DB_NAME = 'mjiphil_catalog';
$DB_USER = 'root';
$DB_PASS = '';

// Auto-setup database on first run
function setupDatabaseIfNeeded($host, $user, $pass, $dbname) {
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Database doesn't exist or can't connect, run setup
        if (strpos($e->getMessage(), 'Unknown database') !== false) {
            require_once './utils/setup_database.php';
            $setup = new DatabaseSetup();
            $setup->setupDatabase();
            
            // Try connecting again
            return new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        throw $e;
    }
}

// PDO connection - exceptions mode
try {
    $pdo = setupDatabaseIfNeeded($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Include JSON data manager
require_once 'data/JsonDataManager.php';
$jsonManager = new JsonDataManager();
?>