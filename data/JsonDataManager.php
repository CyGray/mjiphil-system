<?php
class JsonDataManager {
    private $jsonFile;
    
    public function __construct($filename = 'products.json') {
        $this->jsonFile = __DIR__ . '/' . $filename;
        $this->ensureDirectoryExists();
    }
    
    private function ensureDirectoryExists() {
        $dir = dirname($this->jsonFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    public function loadData() {
        if (!file_exists($this->jsonFile)) {
            return ['products' => []];
        }
        
        $data = json_decode(file_get_contents($this->jsonFile), true);
        return $data ?: ['products' => []];
    }
    
    public function saveData($data) {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return file_put_contents($this->jsonFile, $json) !== false;
    }
    
    public function addProduct($product) {
        $data = $this->loadData();
        $product['id'] = $this->generateProductId($data);
        $product['created_at'] = date('Y-m-d H:i:s');
        $product['updated_at'] = date('Y-m-d H:i:s');
        
        $data['products'][] = $product;
        return $this->saveData($data);
    }
    
    public function updateProduct($productId, $updatedProduct) {
        $data = $this->loadData();
        
        foreach ($data['products'] as &$product) {
            if ($product['id'] == $productId) {
                $updatedProduct['id'] = $productId;
                $updatedProduct['created_at'] = $product['created_at'];
                $updatedProduct['updated_at'] = date('Y-m-d H:i:s');
                $product = $updatedProduct;
                return $this->saveData($data);
            }
        }
        
        return false;
    }
    
    public function deleteProduct($productId) {
        $data = $this->loadData();
        
        $data['products'] = array_filter($data['products'], function($product) use ($productId) {
            return $product['id'] != $productId;
        });
        
        return $this->saveData($data);
    }
    
    private function generateProductId($data) {
        $maxId = 0;
        foreach ($data['products'] as $product) {
            if (isset($product['id']) && $product['id'] > $maxId) {
                $maxId = $product['id'];
            }
        }
        return $maxId + 1;
    }
}
?>