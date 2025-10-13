<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;

class TestIntegratedPurchaseSystem extends Command
{
    protected $signature = 'test:integrated-purchase';
    protected $description = 'Test integrated purchase validation and payment distribution system';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('Testing Integrated Purchase System...');
        $this->info('===================================');

        try {
            // Step 1: Setup test data
            $sellerId = 'test_seller_integrated_' . time();
            $buyerId = 'test_buyer_integrated_' . time();

            // Create seller
            $sellerData = [
                'displayName' => 'Test Seller Integrated',
                'email' => 'seller@integrated.com',
                'coins' => 1000,
                'role' => 'seller',
                'created_at' => now()->toISOString()
            ];
            $this->firestoreService->createDocumentWithId('users', $sellerId, $sellerData);

            // Create buyer
            $buyerData = [
                'displayName' => 'Test Buyer Integrated',
                'email' => 'buyer@integrated.com',
                'coins' => 2000,
                'role' => 'customer',
                'created_at' => now()->toISOString()
            ];
            $this->firestoreService->createDocumentWithId('users', $buyerId, $buyerData);

            // Create products
            $products = [];
            $prices = [150, 250]; // Total: 400
            for ($i = 1; $i <= 2; $i++) {
                $productId = "test_product_integrated_$i" . '_' . time();
                $productData = [
                    'name' => "Integrated Test Product $i",
                    'price' => $prices[$i - 1],
                    'seller_id' => $sellerId,
                    'category' => 'test',
                    'description' => "Integrated test product $i",
                    'file_path' => "test_files/integrated_product$i.pdf",
                    'image_path' => "test_images/integrated_product$i.jpg",
                    'author' => 'Test Author',
                    'status' => 'active',
                    'sold_count' => 0,
                    'created_at' => now()->toISOString()
                ];

                $this->firestoreService->createDocumentWithId('products', $productId, $productData);
                $products[] = array_merge($productData, ['id' => $productId]);
            }

            $this->info("✓ Setup completed: seller, buyer, 2 products");
            sleep(1);

            // Step 2: First purchase - should succeed
            session(['firebase_uid' => $buyerId]);

            $cartItems = [];
            foreach ($products as $product) {
                $cartItems[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => 1,
                    'seller_id' => $product['seller_id']
                ];
            }

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

            $this->info("\n🛒 Test 1: First Purchase (should succeed)");
            $request = new Request(['cart_items' => $cartItems]);
            $response = $checkoutController->processCheckout($request);
            $responseData = json_decode($response->getContent(), true);

            if ($responseData['success']) {
                $this->info("✅ First purchase succeeded");

                // Check coins
                $sellerAfter = $this->firestoreService->getDocument('users', $sellerId);
                $buyerAfter = $this->firestoreService->getDocument('users', $buyerId);

                $sellerEarned = $sellerAfter['coins'] - 1000;
                $buyerSpent = 2000 - $buyerAfter['coins'];

                $this->info("   Seller earned: $sellerEarned xu (expected: 400)");
                $this->info("   Buyer spent: $buyerSpent xu (expected: 400)");

                if ($sellerEarned === 400 && $buyerSpent === 400) {
                    $this->info("✅ Payment distribution correct");
                } else {
                    $this->error("❌ Payment distribution incorrect");
                    return;
                }
            } else {
                $this->error("❌ First purchase failed: " . ($responseData['message'] ?? 'Unknown error'));
                return;
            }

            // Step 3: Second purchase attempt - should fail (already purchased)
            $this->info("\n🛒 Test 2: Second Purchase Attempt (should fail - already purchased)");

            $request2 = new Request(['cart_items' => $cartItems]);
            $response2 = $checkoutController->processCheckout($request2);
            $responseData2 = json_decode($response2->getContent(), true);

            if (!$responseData2['success']) {
                $this->info("✅ Second purchase correctly blocked");
                $this->info("   Message: " . $responseData2['message']);

                // Verify coins didn't change
                $sellerFinal = $this->firestoreService->getDocument('users', $sellerId);
                $buyerFinal = $this->firestoreService->getDocument('users', $buyerId);

                if (
                    $sellerFinal['coins'] === $sellerAfter['coins'] &&
                    $buyerFinal['coins'] === $buyerAfter['coins']
                ) {
                    $this->info("✅ Coins correctly unchanged after blocked purchase");
                } else {
                    $this->error("❌ Coins changed incorrectly after blocked purchase");
                    return;
                }
            } else {
                $this->error("❌ Second purchase should have been blocked but succeeded");
                return;
            }

            // Step 4: Test partial cart (some already purchased)
            $this->info("\n🛒 Test 3: Partial Cart Test (mix of new and purchased products)");

            // Create one more product
            $newProductId = "test_product_integrated_new_" . time();
            $newProductData = [
                'name' => "New Integrated Test Product",
                'price' => 300,
                'seller_id' => $sellerId,
                'category' => 'test',
                'description' => "New integrated test product",
                'file_path' => "test_files/integrated_product_new.pdf",
                'image_path' => "test_images/integrated_product_new.jpg",
                'author' => 'Test Author',
                'status' => 'active',
                'sold_count' => 0,
                'created_at' => now()->toISOString()
            ];

            $this->firestoreService->createDocumentWithId('products', $newProductId, $newProductData);
            $allProducts = array_merge($products, [array_merge($newProductData, ['id' => $newProductId])]);
            $checkoutController->setTestProducts($allProducts);

            sleep(1);

            // Mix cart: 1 already purchased + 1 new
            $mixedCartItems = [
                [
                    'product_id' => $products[0]['id'], // Already purchased
                    'name' => $products[0]['name'],
                    'price' => $products[0]['price'],
                    'quantity' => 1,
                    'seller_id' => $products[0]['seller_id']
                ],
                [
                    'product_id' => $newProductId, // New product
                    'name' => $newProductData['name'],
                    'price' => $newProductData['price'],
                    'quantity' => 1,
                    'seller_id' => $newProductData['seller_id']
                ]
            ];

            $request3 = new Request(['cart_items' => $mixedCartItems]);
            $response3 = $checkoutController->processCheckout($request3);
            $responseData3 = json_decode($response3->getContent(), true);

            if (!$responseData3['success']) {
                $this->info("✅ Mixed cart correctly blocked");
                $this->info("   Message: " . $responseData3['message']);
            } else {
                $this->error("❌ Mixed cart should have been blocked but succeeded");
                return;
            }

            $this->info("\n✅ ALL INTEGRATED PURCHASE SYSTEM TESTS PASSED!");
            $this->info("   ✓ Payment distribution works correctly");
            $this->info("   ✓ Purchase validation prevents re-purchase");
            $this->info("   ✓ Mixed cart validation works");

            // Cleanup
            $this->info("\n🧹 Cleaning up test data...");
            $this->firestoreService->deleteDocument('users', $sellerId);
            $this->firestoreService->deleteDocument('users', $buyerId);
            foreach ($allProducts as $product) {
                $this->firestoreService->deleteDocument('products', $product['id']);
            }
            $this->info("✓ Cleanup completed");
        } catch (\Exception $e) {
            $this->error('Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
