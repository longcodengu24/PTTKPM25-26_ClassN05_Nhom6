<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Account\PaymentController;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class TestPaymentFlow extends Command
{
    protected $signature = 'test:payment-flow {uid?}';
    protected $description = 'Test payment flow với dữ liệu thực tế';

    public function handle()
    {
        $uid = $this->argument('uid') ?: 'test_user_' . time();
        
        $this->info("🧪 Testing Payment Flow với UID: {$uid}");
        
        try {
            // Tạo mock request
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'items' => [
                    [
                        'product_id' => 'test_prod_' . time(),
                        'seller_id' => 'test_seller_456',
                        'price' => 15000,
                        'name' => 'River Flows In You',
                        'quantity' => 1
                    ],
                    [
                        'product_id' => 'test_prod_' . (time() + 1),
                        'seller_id' => 'test_seller_789',
                        'price' => 20000,
                        'name' => 'Dreams of Light',
                        'quantity' => 1
                    ]
                ],
                'total_amount' => 35000
            ]);
            
            // Mock session
            session(['firebase_uid' => $uid]);
            
            // Tạo products trong Firestore
            $firestore = new FirestoreSimple();
            
            foreach ($request->items as $item) {
                $productData = [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'seller_id' => $item['seller_id'],
                    'author' => 'Test Author',
                    'file_path' => '/test/files/' . $item['product_id'] . '.txt',
                    'image_path' => '/test/images/' . $item['product_id'] . '.jpg',
                    'category' => 'Piano',
                    'description' => 'Test description for ' . $item['name'],
                    'status' => 'active'
                ];
                
                $firestore->createDocumentWithId('products', $item['product_id'], $productData);
                $this->info("✅ Created product: {$item['name']}");
            }
            
            // Tạo user với coins
            $userData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'coins' => 50000,
                'role' => 'user'
            ];
            
            $firestore->createDocumentWithId('users', $uid, $userData);
            $this->info("✅ Created user with 50,000 coins");
            
            // Tạo sellers
            $sellerData1 = [
                'name' => 'Test Seller 1',
                'email' => 'seller1@example.com',
                'coins' => 10000,
                'role' => 'saler'
            ];
            
            $firestore->createDocumentWithId('users', 'test_seller_456', $sellerData1);
            
            $sellerData2 = [
                'name' => 'Test Seller 2',
                'email' => 'seller2@example.com',
                'coins' => 15000,
                'role' => 'saler'
            ];
            
            $firestore->createDocumentWithId('users', 'test_seller_789', $sellerData2);
            $this->info("✅ Created sellers");
            
            // Test PaymentController
            $this->info("💳 Testing PaymentController::confirmCartPayment...");
            
            $paymentController = new PaymentController(
                new \App\Services\FirestoreRestService(),
                new \App\Services\UserPurchaseService(),
                new \App\Services\SheetActivityService()
            );
            
            $response = $paymentController->confirmCartPayment($request);
            $responseData = $response->getData(true);
            
            if ($responseData['success']) {
                $this->info("✅ Payment successful!");
                $this->info("📊 Transaction ID: " . $responseData['transaction_id']);
                $this->info("💰 New balance: " . number_format($responseData['new_balance']));
                $this->info("📄 Sheets created: " . count($responseData['sheets']));
                
                // Kiểm tra activities
                $this->info("🔍 Checking activities...");
                $activityService = new \App\Services\ActivityService();
                $activities = $activityService->getUserActivities($uid, 10);
                
                $this->info("📋 Found " . count($activities) . " activities:");
                foreach ($activities as $index => $activity) {
                    $this->line("  " . ($index + 1) . ". [{$activity['type']}] {$activity['title']}");
                }
                
                // Kiểm tra purchases
                $this->info("🔍 Checking purchases...");
                $purchaseService = new \App\Services\UserPurchaseService();
                $sheets = $purchaseService->getUserSheets($uid);
                
                $this->info("📄 Found " . count($sheets) . " sheets:");
                foreach ($sheets as $index => $sheet) {
                    $sheetData = $sheet['data'];
                    $this->line("  " . ($index + 1) . ". [{$sheet['id']}] {$sheetData['title']} - {$sheetData['price']} coins");
                }
                
            } else {
                $this->error("❌ Payment failed: " . $responseData['message']);
            }
            
            $this->info("🎉 Test completed!");
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            Log::error('TestPaymentFlow error: ' . $e->getMessage());
        }
    }
}
