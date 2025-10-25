<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserPurchaseService;
use App\Services\FirestoreSimple;

class TestPurchase extends Command
{
    protected $signature = 'test:purchase {user_id} {product_id}';
    protected $description = 'Test purchase process for a user and product';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $productId = $this->argument('product_id');
        
        $this->info("🛒 Testing purchase for user: {$userId}, product: {$productId}");
        
        // Lấy thông tin sản phẩm
        $firestore = new FirestoreSimple();
        $productDoc = $firestore->getDocument('products', $productId);
        
        if (!$productDoc) {
            $this->error("❌ Product not found: {$productId}");
            return 1;
        }
        
        $product = $productDoc;
        $this->info("✅ Product found: " . ($product['name'] ?? 'N/A'));
        
        // Tạo purchase data
        $purchaseData = [
            'product_id' => $productId,
            'product_name' => $product['name'] ?? 'Unknown Product',
            'seller_id' => $product['seller_id'] ?? 'unknown_seller',
            'buyer_id' => $userId,
            'price' => floatval($product['price'] ?? 0),
            'transaction_id' => 'test_txn_' . time(),
            'purchased_at' => now()->toIso8601String(),
            'file_path' => $product['file_path'] ?? '',
            'image_path' => $product['image_path'] ?? '',
            'author' => $product['author'] ?? '',
            'status' => 'completed',
            'category' => $product['category'] ?? '',
            'description' => $product['description'] ?? ''
        ];
        
        $this->info("📝 Purchase data prepared:");
        $this->line("   - Product: " . $purchaseData['product_name']);
        $this->line("   - Price: " . $purchaseData['price']);
        $this->line("   - Author: " . $purchaseData['author']);
        $this->line("   - Category: " . $purchaseData['category']);
        
        // Lưu sheet
        $userPurchaseService = new UserPurchaseService();
        $result = $userPurchaseService->savePurchase($userId, $purchaseData);
        
        if ($result['success']) {
            $this->info("✅ Sheet saved successfully!");
            $this->line("   - Sheet ID: " . $result['sheet_id']);
            $this->line("   - User ID: " . $result['user_id']);
        } else {
            $this->error("❌ Failed to save sheet:");
            $this->line("   - Error: " . $result['error']);
            return 1;
        }
        
        // Test lấy sheets
        $this->info("📊 Testing sheets retrieval...");
        $sheets = $userPurchaseService->getUserSheets($userId);
        
        $this->info("Found " . count($sheets) . " sheets:");
        foreach ($sheets as $index => $sheet) {
            $this->line("   " . ($index + 1) . ". " . ($sheet['data']['title'] ?? 'N/A') . " - " . ($sheet['data']['category'] ?? 'N/A'));
        }
        
        return 0;
    }
}
