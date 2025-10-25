<?php

require_once 'vendor/autoload.php';

use App\Services\UserPurchaseService;
use App\Services\FirestoreSimple;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing UserPurchaseService to debug sheet saving issue...\n";

try {
    $userId = 'cfT4zfDX4YRkuwd4T6X3seJhtbl1';
    
    // 1. Get all products
    $firestore = new FirestoreSimple();
    $productsResult = $firestore->getAllProducts();
    
    if (empty($productsResult)) {
        echo "No products found in database\n";
        exit(1);
    }
    
    echo "Found " . count($productsResult) . " products\n";
    
    // Get first product
    $productIds = array_keys($productsResult);
    $productId = $productIds[0];
    $firstProduct = $productsResult[$productId];
    
    echo "Using product ID: {$productId}\n";
    
    // Use the product data we already have
    $product = $firstProduct;
    echo "Product found: " . ($product['title'] ?? 'N/A') . "\n";
    echo "Price: " . ($product['price'] ?? 'N/A') . "\n";
    echo "Seller: " . ($product['seller_name'] ?? 'N/A') . "\n";
    
    // 2. Create purchase data (simulate real payment data)
    $purchaseData = [
        'author' => $product['seller_name'] ?? '',
        'buyer_id' => $userId,
        'file_path' => $product['file_url'] ?? '',
        'image_path' => is_array($product['image_path'] ?? '') ? '' : ($product['image_path'] ?? ''),
        'price' => floatval($product['price'] ?? 0),
        'product_id' => $productId,
        'product_name' => $product['title'] ?? '',
        'purchased_at' => now()->toIso8601String(),
        'seller_id' => $product['seller_uid'] ?? '',
        'status' => 'completed',
        'transaction_id' => 'debug_txn_' . time(),
        'category' => $product['category'] ?? '',
        'description' => $product['description'] ?? ''
    ];
    
    echo "Purchase data created:\n";
    echo json_encode($purchaseData, JSON_PRETTY_PRINT) . "\n";
    
    // 3. Test UserPurchaseService
    $userPurchaseService = new UserPurchaseService();
    echo "\nTesting UserPurchaseService::savePurchase...\n";
    
    $result = $userPurchaseService->savePurchase($userId, $purchaseData);
    
    if ($result['success']) {
        echo "✅ Sheet saved successfully!\n";
        echo "Sheet ID: " . $result['sheet_id'] . "\n";
        
        // 4. Verify by getting user sheets
        $sheets = $userPurchaseService->getUserSheets($userId);
        echo "Total sheets for user: " . count($sheets) . "\n";
        
        foreach ($sheets as $sheet) {
            echo "---\n";
            echo "Sheet ID: " . $sheet['id'] . "\n";
            echo "Title: " . ($sheet['data']['title'] ?? 'N/A') . "\n";
            echo "Product ID: " . ($sheet['data']['product_id'] ?? 'N/A') . "\n";
            echo "File URL: " . ($sheet['data']['file_url'] ?? 'N/A') . "\n";
            echo "Buyer ID: " . ($sheet['data']['buyer_id'] ?? 'N/A') . "\n";
        }
        
    } else {
        echo "❌ Failed to save sheet: " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
