<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;

class TestPaymentDistribution extends Command
{
    protected $signature = 'test:payment-distribution';
    protected $description = 'Test payment distribution for multiple items from same seller';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('Testing Payment Distribution...');
        $this->info('==================================');

        try {
            // Step 1: Tạo test seller
            $sellerId = 'test_seller_payment_' . time();
            $sellerData = [
                'displayName' => 'Test Seller Payment',
                'email' => 'testseller@payment.com',
                'coins' => 1000,
                'role' => 'seller',
                'created_at' => now()->toISOString()
            ];

            $this->firestoreService->createDocumentWithId('users', $sellerId, $sellerData);
            $this->info("✓ Created test seller: $sellerId (Initial coins: 1000)");

            // Step 2: Tạo test buyer
            $buyerId = 'test_buyer_payment_' . time();
            $buyerData = [
                'displayName' => 'Test Buyer Payment',
                'email' => 'testbuyer@payment.com',
                'coins' => 5000,
                'role' => 'customer',
                'created_at' => now()->toISOString()
            ];

            $this->firestoreService->createDocumentWithId('users', $buyerId, $buyerData);
            $this->info("✓ Created test buyer: $buyerId (Initial coins: 5000)");

            // Step 3: Tạo 3 products cùng seller với giá khác nhau
            $products = [];
            $prices = [100, 200, 300]; // Total: 600

            for ($i = 1; $i <= 3; $i++) {
                $productId = 'test_product_payment_' . $i . '_' . time();
                $productData = [
                    'name' => "Test Product Payment $i",
                    'price' => $prices[$i - 1],
                    'seller_id' => $sellerId,
                    'category' => 'test',
                    'description' => "Test product $i for payment distribution",
                    'file_path' => "test_files/product$i.pdf",
                    'image_path' => "test_images/product$i.jpg",
                    'author' => 'Test Author',
                    'status' => 'active',
                    'sold_count' => 0,
                    'created_at' => now()->toISOString()
                ];

                $this->firestoreService->createDocumentWithId('products', $productId, $productData);
                $products[] = array_merge($productData, ['id' => $productId]);
                $this->info("✓ Created product: {$productData['name']} (Price: {$prices[$i - 1]} xu)");
            }

            // Wait for Firebase sync
            sleep(1);

            // Step 4: Tạo cart với 3 products từ cùng seller
            $cartItems = [];
            foreach ($products as $product) {
                $cartItems[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'seller_id' => $sellerId
                ];
            }

            $this->info("\n📦 Cart Items:");
            $totalExpected = 0;
            foreach ($cartItems as $item) {
                $this->info("   - {$item['name']}: {$item['price']} xu");
                $totalExpected += $item['price'];
            }
            $this->info("   Total Expected: $totalExpected xu");

            // Step 5: Kiểm tra coins trước khi mua
            $sellerBefore = $this->firestoreService->getDocument('users', $sellerId);
            $buyerBefore = $this->firestoreService->getDocument('users', $buyerId);

            $this->info("\n💰 Before Purchase:");
            $this->info("   Seller coins: {$sellerBefore['coins']}");
            $this->info("   Buyer coins: {$buyerBefore['coins']}");

            // Step 6: Simulate session for CheckoutController
            session(['firebase_uid' => $buyerId]);

            // Step 7: Test checkout process - simulate direct Firestore access
            // Override getProductById to use direct Firestore lookup
            $checkoutController = new class extends \App\Http\Controllers\CheckoutController {
                private $testProducts;

                public function setTestProducts($products)
                {
                    $this->testProducts = $products;
                }

                protected function getProductById($productId)
                {
                    foreach ($this->testProducts as $product) {
                        if ($product['id'] === $productId) {
                            return [
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'price' => $product['price'],
                                'seller_id' => $product['seller_id'],
                                'file_path' => $product['file_path'],
                                'image_path' => $product['image_path'],
                                'author' => $product['author']
                            ];
                        }
                    }
                    return null;
                }
            };

            $checkoutController->setTestProducts($products);
            $request = new Request(['cart_items' => $cartItems]);

            $this->info("\n🛒 Processing Checkout...");
            $response = $checkoutController->processCheckout($request);
            $responseData = json_decode($response->getContent(), true);

            if ($responseData['success']) {
                $this->info("✓ Checkout successful!");

                // Step 8: Kiểm tra coins sau khi mua
                $sellerAfter = $this->firestoreService->getDocument('users', $sellerId);
                $buyerAfter = $this->firestoreService->getDocument('users', $buyerId);

                $this->info("\n💰 After Purchase:");
                $this->info("   Seller coins: {$sellerAfter['coins']} (+" . ($sellerAfter['coins'] - $sellerBefore['coins']) . ")");
                $this->info("   Buyer coins: {$buyerAfter['coins']} (-" . ($buyerBefore['coins'] - $buyerAfter['coins']) . ")");

                // Step 9: Validation
                $sellerEarned = $sellerAfter['coins'] - $sellerBefore['coins'];
                $buyerSpent = $buyerBefore['coins'] - $buyerAfter['coins'];

                $this->info("\n🔍 Validation:");
                $this->info("   Expected seller to earn: $totalExpected xu");
                $this->info("   Actual seller earned: $sellerEarned xu");
                $this->info("   Expected buyer to spend: $totalExpected xu");
                $this->info("   Actual buyer spent: $buyerSpent xu");

                if ($sellerEarned === $totalExpected && $buyerSpent === $totalExpected) {
                    $this->info("✅ PAYMENT DISTRIBUTION TEST PASSED!");
                } else {
                    $this->error("❌ PAYMENT DISTRIBUTION TEST FAILED!");
                    $this->error("   Seller should have earned $totalExpected but got $sellerEarned");
                    $this->error("   Buyer should have spent $totalExpected but spent $buyerSpent");
                }
            } else {
                $this->error("❌ Checkout failed: " . ($responseData['message'] ?? 'Unknown error'));
            }

            // Cleanup
            $this->info("\n🧹 Cleaning up test data...");
            $this->firestoreService->deleteDocument('users', $sellerId);
            $this->firestoreService->deleteDocument('users', $buyerId);
            foreach ($products as $product) {
                $this->firestoreService->deleteDocument('products', $product['id']);
            }
            $this->info("✓ Cleanup completed");
        } catch (\Exception $e) {
            $this->error('Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
