<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;

class TestMultiSellerPayment extends Command
{
    protected $signature = 'test:multi-seller-payment';
    protected $description = 'Test payment distribution for multiple sellers';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('Testing Multi-Seller Payment Distribution...');
        $this->info('==========================================');

        try {
            // Step 1: Tạo 2 test sellers
            $sellers = [];
            for ($i = 1; $i <= 2; $i++) {
                $sellerId = "test_seller_multi_$i" . '_' . time();
                $sellerData = [
                    'displayName' => "Test Seller Multi $i",
                    'email' => "testseller$i@multi.com",
                    'coins' => 1000,
                    'role' => 'seller',
                    'created_at' => now()->toISOString()
                ];

                $this->firestoreService->createDocumentWithId('users', $sellerId, $sellerData);
                $sellers[] = ['id' => $sellerId, 'initial_coins' => 1000];
                $this->info("✓ Created test seller $i: $sellerId (Initial coins: 1000)");
            }

            // Step 2: Tạo test buyer
            $buyerId = 'test_buyer_multi_' . time();
            $buyerData = [
                'displayName' => 'Test Buyer Multi',
                'email' => 'testbuyer@multi.com',
                'coins' => 5000,
                'role' => 'customer',
                'created_at' => now()->toISOString()
            ];

            $this->firestoreService->createDocumentWithId('users', $buyerId, $buyerData);
            $this->info("✓ Created test buyer: $buyerId (Initial coins: 5000)");

            // Step 3: Tạo products cho mỗi seller
            $products = [];
            $sellerExpectedEarnings = [];

            // Seller 1: 2 products (100, 200 xu) = 300 xu
            for ($j = 1; $j <= 2; $j++) {
                $productId = "test_product_seller1_$j" . '_' . time();
                $price = $j * 100; // 100, 200
                $productData = [
                    'name' => "Seller 1 Product $j",
                    'price' => $price,
                    'seller_id' => $sellers[0]['id'],
                    'category' => 'test',
                    'description' => "Test product $j from seller 1",
                    'file_path' => "test_files/s1_product$j.pdf",
                    'image_path' => "test_images/s1_product$j.jpg",
                    'author' => 'Test Author 1',
                    'status' => 'active',
                    'sold_count' => 0,
                    'created_at' => now()->toISOString()
                ];

                $this->firestoreService->createDocumentWithId('products', $productId, $productData);
                $products[] = array_merge($productData, ['id' => $productId]);

                $sellerExpectedEarnings[$sellers[0]['id']] = ($sellerExpectedEarnings[$sellers[0]['id']] ?? 0) + $price;
                $this->info("✓ Created product for seller 1: {$productData['name']} (Price: $price xu)");
            }

            // Seller 2: 1 product (500 xu)
            $productId = "test_product_seller2_1" . '_' . time();
            $price = 500;
            $productData = [
                'name' => "Seller 2 Product 1",
                'price' => $price,
                'seller_id' => $sellers[1]['id'],
                'category' => 'test',
                'description' => "Test product 1 from seller 2",
                'file_path' => "test_files/s2_product1.pdf",
                'image_path' => "test_images/s2_product1.jpg",
                'author' => 'Test Author 2',
                'status' => 'active',
                'sold_count' => 0,
                'created_at' => now()->toISOString()
            ];

            $this->firestoreService->createDocumentWithId('products', $productId, $productData);
            $products[] = array_merge($productData, ['id' => $productId]);

            $sellerExpectedEarnings[$sellers[1]['id']] = $price;
            $this->info("✓ Created product for seller 2: {$productData['name']} (Price: $price xu)");

            // Wait for Firebase sync
            sleep(1);

            // Step 4: Tạo cart với products từ cả 2 sellers
            $cartItems = [];
            $totalExpected = 0;
            foreach ($products as $product) {
                $cartItems[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'seller_id' => $product['seller_id']
                ];
                $totalExpected += $product['price'];
            }

            $this->info("\n📦 Cart Items:");
            foreach ($cartItems as $item) {
                $sellerName = $item['seller_id'] === $sellers[0]['id'] ? 'Seller 1' : 'Seller 2';
                $this->info("   - {$item['name']} ($sellerName): {$item['price']} xu");
            }
            $this->info("   Total Expected: $totalExpected xu");

            $this->info("\n💰 Expected Earnings:");
            foreach ($sellerExpectedEarnings as $sellerId => $expected) {
                $sellerName = $sellerId === $sellers[0]['id'] ? 'Seller 1' : 'Seller 2';
                $this->info("   $sellerName: $expected xu");
            }

            // Step 5: Kiểm tra coins trước khi mua
            $sellersBefore = [];
            foreach ($sellers as $seller) {
                $sellerData = $this->firestoreService->getDocument('users', $seller['id']);
                $sellersBefore[$seller['id']] = $sellerData['coins'];
            }
            $buyerBefore = $this->firestoreService->getDocument('users', $buyerId);

            $this->info("\n💰 Before Purchase:");
            foreach ($sellers as $i => $seller) {
                $this->info("   Seller " . ($i + 1) . " coins: {$sellersBefore[$seller['id']]}");
            }
            $this->info("   Buyer coins: {$buyerBefore['coins']}");

            // Step 6: Simulate session for CheckoutController
            session(['firebase_uid' => $buyerId]);

            // Step 7: Test checkout process with override
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

            $this->info("\n🛒 Processing Multi-Seller Checkout...");
            $response = $checkoutController->processCheckout($request);
            $responseData = json_decode($response->getContent(), true);

            if ($responseData['success']) {
                $this->info("✓ Checkout successful!");

                // Step 8: Kiểm tra coins sau khi mua
                $sellersAfter = [];
                foreach ($sellers as $seller) {
                    $sellerData = $this->firestoreService->getDocument('users', $seller['id']);
                    $sellersAfter[$seller['id']] = $sellerData['coins'];
                }
                $buyerAfter = $this->firestoreService->getDocument('users', $buyerId);

                $this->info("\n💰 After Purchase:");
                foreach ($sellers as $i => $seller) {
                    $earned = $sellersAfter[$seller['id']] - $sellersBefore[$seller['id']];
                    $this->info("   Seller " . ($i + 1) . " coins: {$sellersAfter[$seller['id']]} (+$earned)");
                }
                $buyerSpent = $buyerBefore['coins'] - $buyerAfter['coins'];
                $this->info("   Buyer coins: {$buyerAfter['coins']} (-$buyerSpent)");

                // Step 9: Validation
                $this->info("\n🔍 Validation:");
                $allValid = true;

                foreach ($sellers as $i => $seller) {
                    $actualEarned = $sellersAfter[$seller['id']] - $sellersBefore[$seller['id']];
                    $expectedEarned = $sellerExpectedEarnings[$seller['id']];

                    $this->info("   Seller " . ($i + 1) . " - Expected: $expectedEarned xu, Actual: $actualEarned xu");

                    if ($actualEarned !== $expectedEarned) {
                        $allValid = false;
                        $this->error("   ❌ Seller " . ($i + 1) . " payment mismatch!");
                    } else {
                        $this->info("   ✅ Seller " . ($i + 1) . " payment correct!");
                    }
                }

                if ($buyerSpent !== $totalExpected) {
                    $allValid = false;
                    $this->error("   ❌ Buyer spent $buyerSpent but should have spent $totalExpected");
                } else {
                    $this->info("   ✅ Buyer payment correct!");
                }

                if ($allValid) {
                    $this->info("\n✅ MULTI-SELLER PAYMENT DISTRIBUTION TEST PASSED!");
                } else {
                    $this->error("\n❌ MULTI-SELLER PAYMENT DISTRIBUTION TEST FAILED!");
                }
            } else {
                $this->error("❌ Checkout failed: " . ($responseData['message'] ?? 'Unknown error'));
            }

            // Cleanup
            $this->info("\n🧹 Cleaning up test data...");
            foreach ($sellers as $seller) {
                $this->firestoreService->deleteDocument('users', $seller['id']);
            }
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
