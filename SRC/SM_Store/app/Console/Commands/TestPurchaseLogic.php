<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PurchaseService;
use App\Services\FirestoreSimple;

class TestPurchaseLogic extends Command
{
    protected $signature = 'test:purchase-logic';
    protected $description = 'Test core purchase validation logic';

    public function handle()
    {
        $this->info("🧪 Testing purchase validation logic...");
        $this->info("================================================================================");

        try {
            $firestoreService = new FirestoreSimple();
            $purchaseService = new PurchaseService($firestoreService);

            // Create test data
            $testUserId = 'test_user_' . time();
            $testProductId = 'test_product_' . time();

            $this->info("Step 1: Testing with fresh user (no purchases)");
            $this->line("Test User ID: {$testUserId}");
            $this->line("Test Product ID: {$testProductId}");
            $this->newLine();

            // Test 1: User with no purchases
            $hasPurchased = $purchaseService->hasPurchasedProduct($testUserId, $testProductId);
            $this->line("📋 Has purchased (no purchase history): " . ($hasPurchased ? 'YES' : 'NO'));

            if ($hasPurchased) {
                $this->error("❌ FAILED: Fresh user shows as having purchased");
                return 1;
            } else {
                $this->line("✅ PASSED: Fresh user correctly shows no purchase");
            }
            $this->newLine();

            // Test 2: Cart validation for fresh user
            $cartItems = [
                [
                    'product_id' => $testProductId,
                    'name' => 'Test Product',
                    'seller_id' => 'test_seller',
                    'price' => 25000
                ]
            ];

            $cartErrors = $purchaseService->validateCartItems($testUserId, $cartItems);
            if (empty($cartErrors)) {
                $this->line("✅ PASSED: Fresh user can add product to cart");
            } else {
                $this->error("❌ FAILED: Fresh user cannot add product to cart");
                foreach ($cartErrors as $error) {
                    $this->line("  → " . $error['message']);
                }
                return 1;
            }
            $this->newLine();

            // Test 3: Create purchase record and test again
            $this->info("Step 2: Creating purchase record and re-testing");

            $purchaseData = [
                'buyer_id' => $testUserId,
                'product_id' => $testProductId,
                'seller_id' => 'test_seller',
                'product_name' => 'Test Product',
                'price' => 25000,
                'purchased_at' => now()->toISOString(),
                'transaction_id' => 'test_' . time()
            ];

            $purchaseId = $firestoreService->createDocument('purchases', $purchaseData);
            $this->line("✅ Test purchase created: {$purchaseId}");

            // Test after purchase
            $hasPurchasedAfter = $purchaseService->hasPurchasedProduct($testUserId, $testProductId);
            $this->line("📋 Has purchased (after purchase): " . ($hasPurchasedAfter ? 'YES' : 'NO'));

            if (!$hasPurchasedAfter) {
                $this->error("❌ FAILED: User doesn't show as having purchased after purchase");
                return 1;
            } else {
                $this->line("✅ PASSED: User correctly shows as having purchased");
            }

            // Test cart validation after purchase
            $cartErrorsAfter = $purchaseService->validateCartItems($testUserId, $cartItems);
            if (!empty($cartErrorsAfter)) {
                $this->line("✅ PASSED: User cannot add purchased product to cart");
                foreach ($cartErrorsAfter as $error) {
                    $this->line("  → " . $error['message']);
                }
            } else {
                $this->error("❌ FAILED: User can still add purchased product to cart");
                return 1;
            }
            $this->newLine();

            // Test 4: Test multiple products
            $this->info("Step 3: Testing multiple products scenario");

            $testProductId2 = 'test_product_2_' . time();
            $multiCartItems = [
                [
                    'product_id' => $testProductId,  // Already purchased
                    'name' => 'Test Product 1 (Purchased)',
                    'seller_id' => 'test_seller',
                    'price' => 25000
                ],
                [
                    'product_id' => $testProductId2, // Not purchased
                    'name' => 'Test Product 2 (New)',
                    'seller_id' => 'test_seller',
                    'price' => 30000
                ]
            ];

            $multiCartErrors = $purchaseService->validateCartItems($testUserId, $multiCartItems);
            if (count($multiCartErrors) === 1) {
                $this->line("✅ PASSED: Multi-cart validation correctly identifies 1 purchased item");
                $this->line("  → " . $multiCartErrors[0]['message']);
            } else {
                $this->error("❌ FAILED: Multi-cart validation incorrect. Found " . count($multiCartErrors) . " errors");
                foreach ($multiCartErrors as $error) {
                    $this->line("  → " . $error['message']);
                }
                return 1;
            }
            $this->newLine();

            // Test 5: Filter purchased items
            $this->info("Step 4: Testing filter functionality");
            $filteredItems = $purchaseService->filterPurchasedItems($testUserId, $multiCartItems);

            $filteredItems = array_values($filteredItems); // Reindex array
            if (count($filteredItems) === 1 && $filteredItems[0]['product_id'] === $testProductId2) {
                $this->line("✅ PASSED: Filter correctly removes purchased items");
                $this->line("  → Remaining item: " . $filteredItems[0]['name']);
            } else {
                $this->error("❌ FAILED: Filter functionality incorrect");
                $this->line("  → Expected 1 item, got " . count($filteredItems));
                if (count($filteredItems) > 0) {
                    foreach ($filteredItems as $i => $item) {
                        $this->line("  → Item {$i}: " . ($item['product_id'] ?? 'unknown'));
                    }
                }
                return 1;
            }
            $this->newLine();

            // Clean up
            $this->info("Step 5: Cleaning up test data");
            $firestoreService->deleteDocument('purchases', $purchaseId);
            $this->line("✅ Test purchase record deleted");
            $this->newLine();

            // Final summary
            $this->info("🎉 All purchase validation logic tests PASSED!");
            $this->line("📋 Test Summary:");
            $this->line("  • Fresh user validation: ✅");
            $this->line("  • Purchase detection: ✅");
            $this->line("  • Cart blocking after purchase: ✅");
            $this->line("  • Multi-item cart validation: ✅");
            $this->line("  • Purchase filtering: ✅");
            $this->newLine();
            $this->line("🚀 System ready for integration with frontend cart!");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Test failed with exception: " . $e->getMessage());
            $this->line("📍 File: " . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
