<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB connection settings
$DB_HOST = '127.0.0.1';
$DB_NAME = 'mjiphil_db';
$DB_USER = 'root';
$DB_PASS = '';

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


/* 
-- Create the database
CREATE DATABASE IF NOT EXISTS mjiphil_catalog;
USE mjiphil_catalog;

-- 1. USER
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'regular') DEFAULT 'regular'
);

-- 2. ADDRESS
CREATE TABLE address (
    address_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 3. CATEGORY
CREATE TABLE category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- 4. PRODUCT
CREATE TABLE product (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES category(category_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- 5. PRODUCT_IMAGE
CREATE TABLE product_image (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 6. INVENTORY
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNIQUE NOT NULL,
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- 7. ORDER_STATUS
CREATE TABLE order_status (
    order_status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name ENUM('Pending', 'Paid', 'Shipped', 'Completed', 'Cancelled') NOT NULL
);

-- 8. ORDER
CREATE TABLE `order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_status_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (order_status_id) REFERENCES order_status(order_status_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- 9. ORDER_ITEM
CREATE TABLE order_item (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `order`(order_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- 10. PAYMENT_METHOD
CREATE TABLE payment_method (
    payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
    method_name ENUM('Cash', 'GCash', 'Bank Transfer', 'Credit Card') NOT NULL
);

-- 11. PAYMENT
CREATE TABLE payment (
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
);

-- 12. USER_PAYMENT_METHOD
CREATE TABLE user_payment_method (
    user_payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    account_identifier VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_method(payment_method_id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO user (first_name, last_name, email, password, role) VALUES
('Roel', 'Admin', 'roel@mjiphil.com', 'cm9lbDEyMw==', 'admin'),
('Kyle', 'Admin', 'kyle@mjiphil.com', 'a3lsZTEyMw==', 'admin'),
('Wilbert', 'Admin', 'wilbert@mjiphil.com', 'd2lsYmVydDEyMw==', 'admin'),
('Gwen', 'Admin', 'gwen@mjiphil.com', 'Z3dlbjEyMw==', 'admin'),
('JC', 'Admin', 'jc@mjiphil.com', 'amMxMjM=', 'admin'),
('Nikolai', 'Admin', 'nikolai@mjiphil.com', 'bmlrb2xhaTEyMw==', 'admin');

INSERT INTO category (category_name, description) VALUES
('Tools', 'Construction tools and equipment'),
('Materials', 'Building and construction materials'),
('Safety Gear', 'Personal protective equipment'),
('Essentials', 'Essential construction supplies'),
('Electrical', 'Electrical supplies and equipment'),
('Plumbing', 'Plumbing materials and fixtures'),
('Hardware', 'Hardware and fasteners'),
('Paint & Supplies', 'Paints, coatings, and application tools'),
('Concrete & Masonry', 'Concrete, cement, and masonry supplies'),
('Lumber & Wood', 'Lumber, plywood, and wood products');
*/
?>