<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserPurchaseService;
use App\Services\FirestoreSimple;

class TestNewPurchaseStructure extends Command
{
    protected $signature = 'purchases:test-new-structure {user_id}';
    protected $description = 'Test new purchase structure with subcollection';

    protected $userPurchaseService;

    public function __construct()
    {
        parent::__construct();
        $this->userPurchaseService = new UserPurchaseService();
    }

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info('🧪 Testing new purchase structure...');
        $this->info('=====================================');
        $this->line('');

        try {
            // 1. Test tạo user purchase document và sheet
            $this->info('1. Testing purchase creation...');
            
            $testPurchaseData = [
                'product_id' => 'test_product_' . time(),
                'product_name' => 'Test Sheet Music',
                'seller_id' => 'test_seller_123',
                'buyer_id' => $userId,
                'price' => 1000,
                'transaction_id' => 'test_txn_' . time(),
                'purchased_at' => now()->toIso8601String(),
                'file_path' => 'seller_files/l0ETEytlFCQLRkJD49aLDFKluvk1/songs/vietnam/1760288132_NHÀ TÔI CÓ TREO MỘT LÁ CỜ (2).txt',
                'image_path' => '/test/image.jpg',
                'author' => 'Test Author',
                'status' => 'completed',
                'category' => 'Piano',
                'description' => 'Test description'
            ];

            $result = $this->userPurchaseService->savePurchase($userId, $testPurchaseData);
            
            if ($result['success']) {
                $this->info("   ✅ Purchase created successfully");
                $this->info("   - Sheet ID: {$result['sheet_id']}");
                $this->info("   - User ID: {$result['user_id']}");
            } else {
                $this->error("   ❌ Failed to create purchase: {$result['error']}");
                return;
            }

            // 2. Test lấy thông tin user purchase
            $this->info('\n2. Testing user purchase info...');
            $userInfo = $this->userPurchaseService->getUserPurchaseInfo($userId);
            
            if ($userInfo) {
                $this->info("   ✅ User purchase info retrieved");
                $this->info("   - User ID: {$userInfo['user_id']}");
                $this->info("   - Total purchases: {$userInfo['total_purchases']}");
                $this->info("   - Last updated: {$userInfo['last_updated']}");
            } else {
                $this->error("   ❌ Failed to get user purchase info");
            }

            // 3. Test lấy danh sách sheets
            $this->info('\n3. Testing sheets retrieval...');
            $sheets = $this->userPurchaseService->getUserSheets($userId);
            
            $this->info("   ✅ Found " . count($sheets) . " sheets");
            foreach ($sheets as $index => $sheet) {
                $this->info("   - Sheet " . ($index + 1) . ": {$sheet['data']['title']} (ID: {$sheet['id']})");
            }

            // 4. Test kiểm tra purchase history
            $this->info('\n4. Testing purchase history check...');
            $hasPurchased = $this->userPurchaseService->hasPurchasedProduct($userId, $testPurchaseData['product_id']);
            
            if ($hasPurchased) {
                $this->info("   ✅ Product purchase history check works");
            } else {
                $this->error("   ❌ Product purchase history check failed");
            }

            // 5. Test cấu trúc Firestore
            $this->info('\n5. Testing Firestore structure...');
            $firestore = new FirestoreSimple();
            
            // Kiểm tra user purchase document
            $userDoc = $firestore->getDocument('purchases', $userId);
            if ($userDoc !== null) {
                $this->info("   ✅ User purchase document exists");
                $this->info("   - Document path: purchases/{$userId}");
            } else {
                $this->error("   ❌ User purchase document not found");
            }

            // Kiểm tra sheets subcollection
            $sheetsCollection = $firestore->listDocuments("purchases/{$userId}/sheets");
            if (isset($sheetsCollection['documents']) && count($sheetsCollection['documents']) > 0) {
                $this->info("   ✅ Sheets subcollection exists");
                $this->info("   - Subcollection path: purchases/{$userId}/sheets");
                $this->info("   - Number of sheets: " . count($sheetsCollection['documents']));
            } else {
                $this->error("   ❌ Sheets subcollection not found or empty");
            }

            $this->line('');
            $this->info('🎉 Test completed successfully!');
            $this->line('');
            $this->info('📋 New structure summary:');
            $this->info('   - Collection: purchases');
            $this->info('   - Document: {user_id}');
            $this->info('   - Fields: user_id, total_purchases, last_updated, created_at');
            $this->info('   - Subcollection: sheets');
            $this->info('   - Sheet documents: sheet_{timestamp}_{hash}');
            $this->info('   - Sheet fields: category, description, file_url, price, purchased_at, rating, seller_name, seller_uid, status, title, product_id, transaction_id, image_path, buyer_id');

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            throw $e; // Re-throw để hiển thị lỗi đầy đủ
        }
    }
}
