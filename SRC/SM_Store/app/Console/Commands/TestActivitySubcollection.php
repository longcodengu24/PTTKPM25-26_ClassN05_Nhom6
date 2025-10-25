<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class TestActivitySubcollection extends Command
{
    protected $signature = 'test:activity-subcollection {uid?}';
    protected $description = 'Test ActivityService với cấu trúc subcollection activities/{uid}';

    public function handle()
    {
        $uid = $this->argument('uid') ?: 'test_user_' . time();
        
        $this->info("🧪 Testing ActivityService với UID: {$uid}");
        
        try {
            $activityService = new ActivityService();
            
            // Test 1: Tạo activity mới
            $this->info("📝 Test 1: Tạo activity mới...");
            $activityId = $activityService->createActivity(
                $uid,
                'purchase',
                'Test mua sheet nhạc "River Flows In You" với giá 15,000 coins',
                [
                    'amount' => 15000,
                    'transaction_id' => 'test_txn_' . time(),
                    'product_id' => 'test_prod_123',
                    'seller_id' => 'test_seller_456'
                ]
            );
            
            if ($activityId) {
                $this->info("✅ Activity created successfully: {$activityId}");
            } else {
                $this->error("❌ Failed to create activity");
                return;
            }
            
            // Test 2: Tạo thêm activity khác
            $this->info("📝 Test 2: Tạo activity deposit...");
            $depositId = $activityService->createActivity(
                $uid,
                'deposit',
                'Nạp thành công 50,000 Sky Coins vào tài khoản qua SePay',
                [
                    'amount' => 50000,
                    'balance' => 65000,
                    'sepay_id' => 'sepay_' . time(),
                    'source' => 'sepay_webhook'
                ]
            );
            
            if ($depositId) {
                $this->info("✅ Deposit activity created: {$depositId}");
            } else {
                $this->error("❌ Failed to create deposit activity");
            }
            
            // Test 3: Lấy danh sách activities
            $this->info("📋 Test 3: Lấy danh sách activities...");
            $activities = $activityService->getUserActivities($uid, 10);
            
            $this->info("📊 Found " . count($activities) . " activities:");
            foreach ($activities as $index => $activity) {
                $this->line("  " . ($index + 1) . ". [{$activity['type']}] {$activity['title']} - {$activity['created_at']}");
            }
            
            // Test 4: Kiểm tra Firestore trực tiếp
            $this->info("🔍 Test 4: Kiểm tra Firestore trực tiếp...");
            $firestore = new FirestoreSimple();
            $response = $firestore->listDocuments("activities-{$uid}", 10);
            
            if (isset($response['documents'])) {
                $this->info("📊 Firestore có " . count($response['documents']) . " documents trong activities-{$uid}");
                
                foreach ($response['documents'] as $doc) {
                    $docId = basename($doc['name'] ?? '');
                    $this->line("  - Document ID: {$docId}");
                }
            } else {
                $this->warn("⚠️ Không tìm thấy documents trong Firestore");
            }
            
            // Test 5: Test markAsRead
            if (!empty($activities)) {
                $this->info("👁️ Test 5: Đánh dấu activity đã đọc...");
                $firstActivity = $activities[0];
                $result = $activityService->markAsRead($uid, $firstActivity['id']);
                
                if ($result) {
                    $this->info("✅ Activity marked as read");
                } else {
                    $this->error("❌ Failed to mark activity as read");
                }
            }
            
            $this->info("🎉 Test completed successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            Log::error('TestActivitySubcollection error: ' . $e->getMessage());
        }
    }
}
