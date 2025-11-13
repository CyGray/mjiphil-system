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
            $this->consoleLog("Database connection failed: " . $e->getMessage(), "error");
            die();
        }
    }
    
    public function setupDatabase() {
        try {
            // Create database
            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
            $this->pdo->exec("USE {$this->db_name}");
            
            $this->consoleLog("Database created successfully", "success");
            
            // Create tables
            $this->createTables();
            
            // Insert default data
            $this->insertDefaultData();
            
            // Sync JSON data
            $this->syncJsonData();
            
            $this->consoleLog("Database setup completed successfully!", "success");
            
        } catch (PDOException $e) {
            $this->consoleLog("Database setup failed: " . $e->getMessage(), "error");
            die();
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
                role ENUM('admin', 'regular') DEFAULT 'regular',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS address (
                address_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                street VARCHAR(255) NOT NULL,
                city VARCHAR(100) NOT NULL,
                province VARCHAR(100) NOT NULL,
                postal_code VARCHAR(20) NOT NULL,
                is_default BOOLEAN DEFAULT FALSE,
                address_type ENUM('billing', 'shipping', 'both') DEFAULT 'both',
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_user_id (user_id)
            )",
            
            "CREATE TABLE IF NOT EXISTS category (
                category_id INT AUTO_INCREMENT PRIMARY KEY,
                category_name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS product (
                product_id INT AUTO_INCREMENT PRIMARY KEY,
                category_id INT NOT NULL,
                product_name VARCHAR(150) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES category(category_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                INDEX idx_category_id (category_id),
                INDEX idx_is_active (is_active)
            )",
            
            "CREATE TABLE IF NOT EXISTS product_image (
                image_id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                image_url VARCHAR(255) NOT NULL,
                is_primary BOOLEAN DEFAULT FALSE,
                alt_text VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_product_id (product_id),
                INDEX idx_is_primary (is_primary)
            )",
            
            "CREATE TABLE IF NOT EXISTS inventory (
                inventory_id INT AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNIQUE NOT NULL,
                stock_quantity INT DEFAULT 0,
                low_stock_threshold INT DEFAULT 10,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS cart (
                cart_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_user_id (user_id)
            )",
            
            "CREATE TABLE IF NOT EXISTS cart_item (
                cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
                cart_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL CHECK (quantity > 0),
                added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (cart_id) REFERENCES cart(cart_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY unique_cart_product (cart_id, product_id),
                INDEX idx_cart_id (cart_id),
                INDEX idx_product_id (product_id)
            )",
            
            "CREATE TABLE IF NOT EXISTS order_status (
                order_status_id INT AUTO_INCREMENT PRIMARY KEY,
                status_name ENUM('Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled') NOT NULL UNIQUE,
                description VARCHAR(255)
            )",
            
            "CREATE TABLE IF NOT EXISTS `order` (
                order_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_status_id INT NOT NULL,
                order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                total_amount DECIMAL(10,2) NOT NULL,
                shipping_address_id INT,
                billing_address_id INT,
                notes TEXT,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (order_status_id) REFERENCES order_status(order_status_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                FOREIGN KEY (shipping_address_id) REFERENCES address(address_id)
                    ON DELETE SET NULL ON UPDATE CASCADE,
                FOREIGN KEY (billing_address_id) REFERENCES address(address_id)
                    ON DELETE SET NULL ON UPDATE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_order_date (order_date),
                INDEX idx_order_status (order_status_id)
            )",
            
            "CREATE TABLE IF NOT EXISTS order_item (
                order_item_id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL CHECK (quantity > 0),
                unit_price DECIMAL(10,2) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES `order`(order_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product(product_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                INDEX idx_order_id (order_id),
                INDEX idx_product_id (product_id)
            )",
            
            "CREATE TABLE IF NOT EXISTS payment_method (
                payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
                method_name ENUM('Cash', 'GCash', 'Bank Transfer', 'Credit Card') NOT NULL UNIQUE,
                description VARCHAR(255)
            )",
            
            "CREATE TABLE IF NOT EXISTS payment (
                payment_id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                payment_method_id INT NOT NULL,
                amount_paid DECIMAL(10,2) NOT NULL,
                payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                payment_status ENUM('Pending', 'Confirmed', 'Refunded') DEFAULT 'Pending',
                transaction_reference VARCHAR(100),
                notes TEXT,
                FOREIGN KEY (order_id) REFERENCES `order`(order_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (payment_method_id) REFERENCES payment_method(payment_method_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                INDEX idx_order_id (order_id),
                INDEX idx_payment_date (payment_date)
            )",
            
            "CREATE TABLE IF NOT EXISTS user_payment_method (
                user_payment_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                payment_method_id INT NOT NULL,
                account_identifier VARCHAR(100),
                is_default BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (payment_method_id) REFERENCES payment_method(payment_method_id)
                    ON DELETE RESTRICT ON UPDATE CASCADE,
                INDEX idx_user_id (user_id),
                UNIQUE KEY unique_user_payment (user_id, payment_method_id, account_identifier)
            )"
        ];
        
        foreach ($tables as $tableSql) {
            $this->pdo->exec($tableSql);
        }
        
        $this->consoleLog("Tables created successfully", "success");
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
        
        // Insert order statuses with descriptions
        $statuses = [
            ['Pending', 'Order has been placed but not yet paid'],
            ['Paid', 'Payment has been confirmed'],
            ['Shipped', 'Order has been shipped to customer'],
            ['Completed', 'Order has been delivered and completed'],
            ['Cancelled', 'Order has been cancelled']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO order_status (status_name, description) VALUES (?, ?)");
        foreach ($statuses as $status) {
            $stmt->execute($status);
        }
        
        // Insert payment methods with descriptions
        $methods = [
            ['Cash', 'Cash on delivery or pickup'],
            ['GCash', 'GCash mobile payment'],
            ['Bank Transfer', 'Bank transfer or deposit'],
            ['Credit Card', 'Credit card payment']
        ];
        
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO payment_method (method_name, description) VALUES (?, ?)");
        foreach ($methods as $method) {
            $stmt->execute($method);
        }
        
        $this->consoleLog("Default data inserted successfully", "success");
    }
    
    private function syncJsonData() {
    $jsonFile = __DIR__ . '/../data/products.json';
    $this->consoleLog("Looking for JSON file at: " . $jsonFile, "info");
    
    if (file_exists($jsonFile)) {
        $this->consoleLog("JSON file found", "success");
        $jsonContent = file_get_contents($jsonFile);
        $jsonData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->consoleLog("JSON decode error: " . json_last_error_msg(), "error");
            return;
        }
        
        if (isset($jsonData['products'])) {
            $this->consoleLog("Found " . count($jsonData['products']) . " products to sync", "info");
            
            // Map JSON category names to database category names
            $categoryMap = [
                'materials' => 'Materials',
                'tools' => 'Tools', 
                'safety' => 'Safety Gear'
            ];
            
            // Prepare statements
            $checkProductStmt = $this->pdo->prepare("SELECT product_id FROM product WHERE product_name = ?");
            $productStmt = $this->pdo->prepare("
                INSERT INTO product (category_id, product_name, description, price) 
                VALUES (?, ?, ?, ?)
            ");
            $updateProductStmt = $this->pdo->prepare("
                UPDATE product SET category_id = ?, description = ?, price = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE product_name = ?
            ");
            $inventoryStmt = $this->pdo->prepare("
                INSERT INTO inventory (product_id, stock_quantity, low_stock_threshold) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE stock_quantity = ?, updated_at = CURRENT_TIMESTAMP
            ");
            $imageStmt = $this->pdo->prepare("
                INSERT INTO product_image (product_id, image_url, is_primary, alt_text) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE image_url = ?, is_primary = ?, alt_text = ?
            ");
            
            $successCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            
            foreach ($jsonData['products'] as $index => $product) {
                $this->consoleLog("Processing product {$index}: {$product['product_name']}", "info");
                
                // Map the category
                $jsonCategory = strtolower($product['category']);
                $dbCategoryName = isset($categoryMap[$jsonCategory]) ? $categoryMap[$jsonCategory] : $jsonCategory;
                
                $this->consoleLog("Looking for category: {$dbCategoryName}", "debug");
                
                // Get category ID
                $categoryStmt = $this->pdo->prepare("SELECT category_id FROM category WHERE category_name = ?");
                $categoryStmt->execute([$dbCategoryName]);
                $category = $categoryStmt->fetch();
                
                if ($category) {
                    $this->consoleLog("Category found: ID {$category['category_id']}", "success");
                    
                    // Check if product already exists with exact name match
                    $checkProductStmt->execute([$product['product_name']]);
                    $existingProduct = $checkProductStmt->fetch();
                    
                    if ($existingProduct) {
                        $this->consoleLog("Product already exists with ID: {$existingProduct['product_id']} - updating", "warning");
                        
                        // Update existing product
                        $updateProductStmt->execute([
                            $category['category_id'],
                            $product['description'],
                            $product['price'],
                            $product['product_name']
                        ]);
                        
                        $product_id = $existingProduct['product_id'];
                        $updatedCount++;
                        
                        $this->consoleLog("Product updated successfully", "success");
                    } else {
                        // Insert new product
                        $productStmt->execute([
                            $category['category_id'],
                            $product['product_name'],
                            $product['description'],
                            $product['price']
                        ]);
                        
                        $product_id = $this->pdo->lastInsertId();
                        
                        if ($product_id) {
                            $this->consoleLog("Product inserted with ID: {$product_id}", "success");
                            $successCount++;
                        } else {
                            $this->consoleLog("Failed to get product ID", "error");
                            $skippedCount++;
                            continue;
                        }
                    }
                    
                    // Update or insert inventory
                    $inventoryStmt->execute([
                        $product_id, 
                        $product['stock_quantity'],
                        10, // default low stock threshold
                        $product['stock_quantity'] // value for ON DUPLICATE KEY UPDATE
                    ]);
                    
                    $this->consoleLog("Inventory updated for product ID: {$product_id}", "success");
                    
                    // Insert or update image if available
                    if (!empty($product['image_url'])) {
                        $imageStmt->execute([
                            $product_id, 
                            $product['image_url'], 
                            TRUE,
                            $product['product_name'],
                            $product['image_url'], // values for ON DUPLICATE KEY UPDATE
                            TRUE,
                            $product['product_name']
                        ]);
                        $this->consoleLog("Image updated for product ID: {$product_id}", "success");
                    }
                    
                } else {
                    $this->consoleLog("Category '{$dbCategoryName}' not found in database! Product skipped.", "error");
                    $skippedCount++;
                    
                    // Debug: show all available categories
                    $allCategories = $this->pdo->query("SELECT category_name FROM category")->fetchAll();
                    $this->consoleLog("Available categories: " . implode(', ', array_column($allCategories, 'category_name')), "debug");
                }
            }
            
            $this->consoleLog("Sync completed: {$successCount} new products, {$updatedCount} updated, {$skippedCount} skipped", "success");
        } else {
            $this->consoleLog("No 'products' key found in JSON data", "error");
        }
    } else {
        $this->consoleLog("JSON file not found at: {$jsonFile}", "error");
    }
}

    private function consoleLog($message, $type = "info") {
        $styles = [
            "success" => "color: green; font-weight: bold;",
            "error"   => "color: red; font-weight: bold;",
            "info"    => "color: blue;",
            "debug"   => "color: orange;",
            "warning" => "color: purple;"
        ];
        
        $style = $styles[$type] ?? $styles["info"];
        echo "<script>console.log('%c[ DATABASE ] $message', '$style');</script>";
        
        // Flush output to make sure it appears immediately in browser
        flush();
        ob_flush();
    }
}

// Run setup if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === 'setup_database.php') {
    // Set header to HTML to ensure script tags work
    header('Content-Type: text/html');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Database Setup</title>
    </head>
    <body>
        <h2>Database Setup Running...</h2>
        <p>Check the browser console (F12) for detailed logs.</p>
        <div id="progress"></div>
        
        <script>
            console.log("=== DATABASE SETUP STARTED ===");
            document.getElementById("progress").innerHTML = "Setup in progress...";
        </script>';
    
    $setup = new DatabaseSetup();
    $setup->setupDatabase();
    
    echo '<script>
            document.getElementById("progress").innerHTML = "Database setup completed!";
            console.log("=== DATABASE SETUP COMPLETED ===");
          </script>
    </body>
    </html>';
}
?>