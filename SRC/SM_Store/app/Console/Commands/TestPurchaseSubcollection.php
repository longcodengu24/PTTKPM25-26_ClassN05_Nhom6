<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserPurchaseService;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class TestPurchaseSubcollection extends Command
{
    protected $signature = 'test:purchase-subcollection {uid?}';
    protected $description = 'Test UserPurchaseService với cấu trúc subcollection purchases/{uid}/sheets';

    public function handle()
    {
        $uid = $this->argument('uid') ?: 'test_user_' . time();
        
        $this->info("🧪 Testing UserPurchaseService với UID: {$uid}");
        
        try {
            $purchaseService = new UserPurchaseService();
            
            // Test 1: Tạo purchase mới
            $this->info("📝 Test 1: Tạo purchase mới...");
            $purchaseData = [
                'product_id' => 'test_prod_' . time(),
                'product_name' => 'River Flows In You',
                'seller_id' => 'test_seller_456',
                'buyer_id' => $uid,
                'price' => 15000,
                'transaction_id' => 'test_txn_' . time(),
                'purchased_at' => now()->toISOString(),
                'file_path' => '/seller_files/test_seller_456/river_flows.txt',
                'image_path' => '/seller_files/test_seller_456/river_flows.jpg',
                'author' => 'Yiruma',
                'status' => 'completed',
                'category' => 'Piano',
                'description' => 'Beautiful piano piece by Yiruma'
            ];
            
            $result = $purchaseService->savePurchase($uid, $purchaseData);
            
            if ($result['success']) {
                $this->info("✅ Purchase created successfully: {$result['sheet_id']}");
            } else {
                $this->error("❌ Failed to create purchase: " . ($result['error'] ?? 'Unknown error'));
                return;
            }
            
            // Test 2: Tạo thêm purchase khác
            $this->info("📝 Test 2: Tạo purchase thứ 2...");
            $purchaseData2 = [
                'product_id' => 'test_prod_' . (time() + 1),
                'product_name' => 'Dreams of Light',
                'seller_id' => 'test_seller_789',
                'buyer_id' => $uid,
                'price' => 20000,
                'transaction_id' => 'test_txn_' . (time() + 1),
                'purchased_at' => now()->toISOString(),
                'file_path' => '/seller_files/test_seller_789/dreams.txt',
                'image_path' => '/seller_files/test_seller_789/dreams.jpg',
                'author' => 'Ludovico Einaudi',
                'status' => 'completed',
                'category' => 'Piano',
                'description' => 'Emotional piano piece by Ludovico Einaudi'
            ];
            
            $result2 = $purchaseService->savePurchase($uid, $purchaseData2);
            
            if ($result2['success']) {
                $this->info("✅ Second purchase created: {$result2['sheet_id']}");
            } else {
                $this->error("❌ Failed to create second purchase: " . ($result2['error'] ?? 'Unknown error'));
            }
            
            // Test 3: Lấy danh sách sheets
            $this->info("📋 Test 3: Lấy danh sách sheets...");
            $sheets = $purchaseService->getUserSheets($uid);
            
            $this->info("📊 Found " . count($sheets) . " sheets:");
            foreach ($sheets as $index => $sheet) {
                $sheetData = $sheet['data'];
                $this->line("  " . ($index + 1) . ". [{$sheet['id']}] {$sheetData['title']} - {$sheetData['price']} coins");
            }
            
            // Test 4: Kiểm tra Firestore trực tiếp
            $this->info("🔍 Test 4: Kiểm tra Firestore trực tiếp...");
            $firestore = new FirestoreSimple();
            
            // Kiểm tra purchases/{uid} document
            $purchaseDoc = $firestore->getDocument('purchases', $uid);
            if ($purchaseDoc) {
                $this->info("📊 User purchase document found:");
                $this->line("  - Total purchases: " . ($purchaseDoc['total_purchases'] ?? 'N/A'));
                $this->line("  - Last updated: " . ($purchaseDoc['last_updated'] ?? 'N/A'));
            } else {
                $this->warn("⚠️ User purchase document not found");
            }
            
            // Kiểm tra purchases/{uid}/sheets subcollection
            $response = $firestore->listDocuments("purchases/{$uid}/sheets", 10);
            
            if (isset($response['documents'])) {
                $this->info("📊 Firestore có " . count($response['documents']) . " sheets trong purchases/{$uid}/sheets");
                
                foreach ($response['documents'] as $doc) {
                    $docId = basename($doc['name'] ?? '');
                    $this->line("  - Sheet ID: {$docId}");
                }
            } else {
                $this->warn("⚠️ Không tìm thấy sheets trong Firestore");
            }
            
            // Test 5: Kiểm tra hasPurchasedProduct
            $this->info("🔍 Test 5: Kiểm tra hasPurchasedProduct...");
            $hasPurchased = $purchaseService->hasPurchasedProduct($uid, $purchaseData['product_id']);
            $this->info("Has purchased product {$purchaseData['product_id']}: " . ($hasPurchased ? 'Yes' : 'No'));
            
            $hasNotPurchased = $purchaseService->hasPurchasedProduct($uid, 'non_existent_product');
            $this->info("Has purchased non-existent product: " . ($hasNotPurchased ? 'Yes' : 'No'));
            
            $this->info("🎉 Test completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            Log::error('TestPurchaseSubcollection error: ' . $e->getMessage());
        }
    }
}
