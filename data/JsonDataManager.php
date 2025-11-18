<?php
class JsonDataManager {
    private $jsonFile;
    
    public function __construct($filename = 'products.json') {
        $this->jsonFile = __DIR__ . '/' . $filename;
        $this->ensureDirectoryExists();
        $this->debugLog("JsonDataManager initialized with file: " . $this->jsonFile);
    }
    
    private function ensureDirectoryExists() {
        $dir = dirname($this->jsonFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $this->debugLog("Created directory: " . $dir);
        }
    }
    
    private function debugLog($message, $data = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] JSON MANAGER: $message";
    if ($data !== null) {
        $logMessage .= " | Data: " . json_encode($data);
    }
    error_log($logMessage);
    if (php_sapi_name() !== 'cli' && !$this->isJsonRequest()) {
        echo "<script>console.log('" . addslashes($logMessage) . "');</script>";
    }
}

private function isJsonRequest() {
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return true;
    }
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        return true;
    }
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptName, 'api/') !== false || 
        strpos($scriptName, 'delete_item.php') !== false ||
        strpos($scriptName, 'get_item.php') !== false) {
        return true;
    }
    return false;
}
    
    public function loadData() {
        $this->debugLog("Loading data from JSON file");
        
        if (!file_exists($this->jsonFile)) {
            $this->debugLog("JSON file does not exist, creating empty structure");
            return ['products' => []];
        }
        
        $jsonContent = file_get_contents($this->jsonFile);
        $data = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->debugLog("JSON decode error: " . json_last_error_msg());
            return ['products' => []];
        }
        
        $this->debugLog("Successfully loaded data", ['product_count' => count($data['products'] ?? [])]);
        return $data ?: ['products' => []];
    }
    
    public function saveData($data) {
        $this->debugLog("Saving data to JSON file", ['product_count' => count($data['products'] ?? [])]);
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $result = file_put_contents($this->jsonFile, $json) !== false;
        
        if ($result) {
            $this->debugLog("Data saved successfully");
        } else {
            $this->debugLog("FAILED to save data");
        }
        
        return $result;
    }
    
    public function addProduct($product) {
        $this->debugLog("Adding new product", $product);
        
        $data = $this->loadData();
        $product['id'] = $this->generateProductId($data);
        $product['created_at'] = date('Y-m-d H:i:s');
        $product['updated_at'] = date('Y-m-d H:i:s');
        
        $data['products'][] = $product;
        $result = $this->saveData($data);
        
        if ($result) {
            $this->debugLog("Product added successfully", ['id' => $product['id']]);
        } else {
            $this->debugLog("FAILED to add product");
        }
        
        return $result;
    }
    
    public function updateProduct($productId, $updatedProduct) {
    $this->debugLog("Updating product", [
        'product_id' => $productId,
        'updated_data' => $updatedProduct
    ]);
    
    $data = $this->loadData();
    $this->debugLog("Current products in JSON", array_map(function($p) {
        return ['id' => $p['id'] ?? 'none', 'name' => $p['product_name'] ?? 'unnamed'];
    }, $data['products']));
    
    $found = false;
    $updatedCount = 0;
    
    foreach ($data['products'] as &$product) {
        $currentId = $product['id'] ?? 'none';
        $this->debugLog("Checking product", [
            'json_id' => $currentId,
            'target_id' => $productId,
            'match' => ($currentId == $productId) ? 'YES' : 'NO'
        ]);
        
        if (isset($product['id']) && $product['id'] == $productId) {
            $this->debugLog("FOUND matching product to update", $product);
            $updatedProduct['created_at'] = $product['created_at'] ?? date('Y-m-d H:i:s');
            $updatedProduct['updated_at'] = date('Y-m-d H:i:s');
            $updatedProduct['id'] = $product['id'];

            $product = array_merge($product, $updatedProduct);
            $found = true;
            $updatedCount++;
            $this->debugLog("Product after merge", $product);
            break;
        }
    }
    
    if ($found) {
        $result = $this->saveData($data);
        if ($result) {
            $this->debugLog("Product updated successfully in JSON", [
                'product_id' => $productId,
                'products_updated' => $updatedCount
            ]);
        } else {
            $this->debugLog("FAILED to save updated product data");
        }
        return $result;
    } else {
        $this->debugLog("Product NOT found for update", ['product_id' => $productId]);
        return false;
    }
}

public function deleteProduct($productId) {
    $this->debugLog("Deleting product", ['product_id' => $productId]);
    
    $data = $this->loadData();
    $this->debugLog("Current products before deletion", [
        'count' => count($data['products']),
        'products' => array_map(function($p) {
            return ['id' => $p['id'] ?? 'none', 'name' => $p['product_name'] ?? 'unnamed'];
        }, $data['products'])
    ]);
    
    $initialCount = count($data['products']);

    $data['products'] = array_filter($data['products'], function($product) use ($productId) {
        $match = !(isset($product['id']) && $product['id'] == $productId);
        
        $this->debugLog("Filter check", [
            'product_name' => $product['product_name'] ?? 'unnamed',
            'json_id' => $product['id'] ?? 'none',
            'target_id' => $productId,
            'keep' => $match ? 'YES' : 'NO'
        ]);
        
        return $match;
    });
    
    $finalCount = count($data['products']);
    $deletedCount = $initialCount - $finalCount;
    
    $this->debugLog("Deletion results", [
        'initial_count' => $initialCount,
        'final_count' => $finalCount,
        'deleted_count' => $deletedCount
    ]);

    if ($deletedCount > 0) {
        $result = $this->saveData($data);
        if ($result) {
            $this->debugLog("Product deleted successfully from JSON", [
                'product_id' => $productId,
                'products_deleted' => $deletedCount
            ]);
        } else {
            $this->debugLog("FAILED to save data after deletion");
        }
        return $result;
    } else {
        $this->debugLog("No product was found to delete", ['product_id' => $productId]);
        return false;
    }
}
    private function generateProductId($data) {
        $maxId = 0;
        foreach ($data['products'] as $product) {
            if (isset($product['id']) && $product['id'] > $maxId) {
                $maxId = $product['id'];
            }
            if (isset($product['product_id']) && $product['product_id'] > $maxId) {
                $maxId = $product['product_id'];
            }
        }
        $newId = $maxId + 1;
        $this->debugLog("Generated new product ID", ['max_id' => $maxId, 'new_id' => $newId]);
        return $newId;
    }
}
?>