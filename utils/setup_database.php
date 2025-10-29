<?php
// Database setup and initialization script
class DatabaseSetup {
    private $pdo;
    private $db_host = '127.0.0.1';
    private $db_user = 'root';
    private $db_pass = '';
    private $db_name = 'mjiphil_catalog';
    
    public function __construct() {
        try {
            // First connect without database to create it
            $this->pdo = new PDO("mysql:host={$this->db_host}", $this->db_user, $this->db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function setupDatabase() {
        try {
            // Create database
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
            $this->pdo->exec("USE {$this->db_name}");
            
            echo "Database created successfully.<br>";
            
            // Create tables
            $this->createTables();
            
            // Insert default data
            $this->insertDefaultData();
            
            // Sync JSON data
            $this->syncJsonData();
            
            echo "Database setup completed successfully!";
            
        } catch (PDOException $e) {
            die("Database setup failed: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS user (
                user_id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                phone VARCHAR(20),
                role ENUM('admin', 'regular') DEFAULT 'regular'
            )",
            
            "CREATE TABLE IF NOT EXISTS address (
                address_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                street VARCHAR(255) NOT NULL,
                city VARCHAR(100) NOT NULL,
                province VARCHAR(100) NOT NULL,
                postal_code VARCHAR(20) NOT NULL,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS category (
                category_id INT AUTO_INCREMENT PRIMARY KEY,
                category_name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT
            )",
            
            "CREATE TABLE IF NOT EXISTS product (
                product_id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                product_name VARCHAR(150) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (category_id) REFERENCES category(category_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS product_image (
                image_id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                image_url VARCHAR(255) NOT NULL,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS inventory (
                inventory_id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNIQUE NOT NULL,
                stock_quantity INT DEFAULT 0,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS order_status (
                order_status_id INT AUTO_INCREMENT PRIMARY KEY,
                status_name ENUM('Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled') NOT NULL
            )",
            
            "CREATE TABLE IF NOT EXISTS `order` (
                order_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_status_id INT NOT NULL,
                order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                total_amount DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (order_status_id) REFERENCES order_status(order_status_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS order_item (
                order_item_id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES `order`(order_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS payment_method (
                payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
                method_name ENUM('Cash', 'GCash', 'Bank Transfer', 'Credit Card') NOT NULL
            )",
            
            "CREATE TABLE IF NOT EXISTS payment (
                payment_id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                payment_method_id INT NOT NULL,
                amount_paid DECIMAL(10,2) NOT NULL,
                payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                payment_status ENUM('Pending', 'Confirmed', 'Refunded') DEFAULT 'Pending',
                FOREIGN KEY (order_id) REFERENCES `order`(order_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (payment_method_id) REFERENCES payment_method(payment_method_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS user_payment_method (
                user_payment_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                payment_method_id INT NOT NULL,
                account_identifier VARCHAR(100),
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (payment_method_id) REFERENCES payment_method(payment_method_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE
            )"
        ];
        
        foreach ($tables as $tableSql) {
            $this->pdo->exec($tableSql);
        }
        
        echo "Tables created successfully.<br>";
    }
    
    private function insertDefaultData() {
        // Insert admin users
        $admins = [
            ['Roel', 'Admin', 'roel@mjiphil.com', 'cm9lbDEyMw==', 'admin'],
            ['Kyle', 'Admin', 'kyle@mjiphil.com', 'a3lsZTEyMw==', 'admin'],
            ['Wilbert', 'Admin', 'wilbert@mjiphil.com', 'd2lsYmVydDEyMw==', 'admin'],
            ['Gwen', 'Admin', 'gwen@mjiphil.com', 'Z3dlbjEyMw==', 'admin'],
            ['JC', 'Admin', 'jc@mjiphil.com', 'amMxMjM=', 'admin'],
            ['Nikolai', 'Admin', 'nikolai@mjiphil.com', 'bmlrb2xhaTEyMw==', 'admin']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO user (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        foreach ($admins as $admin) {
            $stmt->execute($admin);
        }
        
        // Insert categories
        $categories = [
            ['Tools', 'Construction tools and equipment'],
            ['Materials', 'Building and construction materials'],
            ['Safety Gear', 'Personal protective equipment'],
            ['Essentials', 'Essential construction supplies'],
            ['Electrical', 'Electrical supplies and equipment'],
            ['Plumbing', 'Plumbing materials and fixtures'],
            ['Hardware', 'Hardware and fasteners'],
            ['Paint & Supplies', 'Paints, coatings, and application tools'],
            ['Concrete & Masonry', 'Concrete, cement, and masonry supplies'],
            ['Lumber & Wood', 'Lumber, plywood, and wood products']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO category (category_name, description) VALUES (?, ?)");
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
        
        // Insert order statuses
        $statuses = ['Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled'];
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO order_status (status_name) VALUES (?)");
        foreach ($statuses as $status) {
            $stmt->execute([$status]);
        }
        
        // Insert payment methods
        $methods = ['Cash', 'GCash', 'Bank Transfer', 'Credit Card'];
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO payment_method (method_name) VALUES (?)");
        foreach ($methods as $method) {
            $stmt->execute([$method]);
        }
        
        echo "Default data inserted successfully.<br>";
    }
    
    private function syncJsonData() {
        $jsonFile = __DIR__ . '/data/products.json';
        if (file_exists($jsonFile)) {
            $jsonData = json_decode(file_get_contents($jsonFile), true);
            
            if (isset($jsonData['products'])) {
                $productStmt = $this->pdo->prepare("INSERT IGNORE INTO product (category_id, product_name, description, price) VALUES (?, ?, ?, ?)");
                $inventoryStmt = $this->pdo->prepare("INSERT IGNORE INTO inventory (product_id, stock_quantity) VALUES (?, ?)");
                $imageStmt = $this->pdo->prepare("INSERT IGNORE INTO product_image (product_id, image_url) VALUES (?, ?)");
                
                foreach ($jsonData['products'] as $product) {
                    // Get category ID
                    $categoryStmt = $this->pdo->prepare("SELECT category_id FROM category WHERE category_name = ?");
                    $categoryStmt->execute([$product['category']]);
                    $category = $categoryStmt->fetch();
                    
                    if ($category) {
                        $productStmt->execute([
                            $category['category_id'],
                            $product['product_name'],
                            $product['description'],
                            $product['price']
                        ]);
                        
                        $product_id = $this->pdo->lastInsertId();
                        
                        if ($product_id) {
                            $inventoryStmt->execute([$product_id, $product['stock_quantity']]);
                            
                            if (!empty($product['image_url'])) {
                                $imageStmt->execute([$product_id, $product['image_url']]);
                            }
                        }
                    }
                }
                
                echo "JSON data synchronized successfully.<br>";
            }
        }
    }
}

// Run setup if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === 'setup_database.php') {
    $setup = new DatabaseSetup();
    $setup->setupDatabase();
}
?>