// debug_json.php
<?php
require_once 'config.php';

header('Content-Type: text/plain');
echo "=== JSON DATA DEBUG ===\n\n";

$jsonFile = __DIR__ . '/data/products.json';
echo "JSON File: $jsonFile\n";
echo "File exists: " . (file_exists($jsonFile) ? 'YES' : 'NO') . "\n\n";

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $data = json_decode($jsonContent, true);
    
    echo "JSON decode error: " . json_last_error_msg() . "\n\n";
    echo "Products count: " . count($data['products'] ?? []) . "\n\n";
    
    echo "PRODUCTS STRUCTURE:\n";
    foreach ($data['products'] as $index => $product) {
        echo "Product #" . ($index + 1) . ":\n";
        echo "  ID fields: id=" . ($product['id'] ?? 'NOT SET') . ", product_id=" . ($product['product_id'] ?? 'NOT SET') . "\n";
        echo "  Name: " . ($product['product_name'] ?? 'NOT SET') . "\n";
        echo "  Price: " . ($product['price'] ?? 'NOT SET') . "\n";
        echo "  Stock: " . ($product['stock_quantity'] ?? 'NOT SET') . "\n";
        echo "  Category: " . ($product['category'] ?? 'NOT SET') . "\n";
        echo "  Image: " . ($product['image_url'] ?? 'NOT SET') . "\n";
        echo "  Created: " . ($product['created_at'] ?? 'NOT SET') . "\n";
        echo "  Updated: " . ($product['updated_at'] ?? 'NOT SET') . "\n";
        echo "\n";
    }
} else {
    echo "JSON file not found!\n";
}
?>