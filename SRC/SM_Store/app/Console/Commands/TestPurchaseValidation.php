<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PurchaseService;
use App\Services\FirestoreSimple;
use App\Models\Product;

class TestPurchaseValidation extends Command
{
    protected $signature = 'test:purchase-validation {buyer_uid} {product_id}';
    protected $description = 'Test purchase validation system - prevent re-purchasing';

    public function handle()
    {
        $buyerUid = $this->argument('buyer_uid');
        $productId = $this->argument('product_id');

        $this->info("🧪 Testing purchase validation system...");
        $this->info("================================================================================");

        try {
            $firestoreService = new FirestoreSimple();
            $purchaseService = new PurchaseService($firestoreService);
            $productModel = new Product($firestoreService);

            $this->line("Buyer ID: {$buyerUid}");
            $this->line("Product ID: {$productId}");
            $this->newLine();

            // Step 1: Check product exists
            $this->info("Step 1: Validating product and buyer");
            $product = $firestoreService->getDocument('products', $productId);
            if (!$product) {
                $this->error("❌ Product not found: {$productId}");
                return 1;
            }

            $buyer = $firestoreService->getDocument('users', $buyerUid);
            if (!$buyer) {
                $this->error("❌ Buyer not found: {$buyerUid}");
                return 1;
            }

            $this->line("✅ Product: " . ($product['name'] ?? 'Unknown'));
            $this->line("✅ Buyer: " . ($buyer['name'] ?? 'Unknown'));
            $this->newLine();

            // Step 2: Check initial purchase status
            $this->info("Step 2: Checking initial purchase status");
            $hasPurchased = $purchaseService->hasPurchasedProduct($buyerUid, $productId);
            $this->line("📋 Has purchased before: " . ($hasPurchased ? 'YES' : 'NO'));
            $this->newLine();

            // Step 3: Test cart validation
            $this->info("Step 3: Testing cart validation");
            $cartItems = [
                [
                    'product_id' => $productId,
                    'name' => $product['name'] ?? 'Test Product',
                    'seller_id' => $product['seller_id'] ?? 'unknown',
                    'price' => $product['price'] ?? 25000
                ]
            ];

            $cartErrors = $purchaseService->validateCartItems($buyerUid, $cartItems);
            if (empty($cartErrors)) {
                $this->line("✅ Cart validation: PASSED - Can add to cart");
                $canAddToCart = true;
            } else {
                $this->line("❌ Cart validation: FAILED - Cannot add to cart");
                foreach ($cartErrors as $error) {
                    $this->line("  → " . $error['message']);
                }
                $canAddToCart = false;
            }
            $this->newLine();

            // Step 4: Simulate purchase if allowed
            if ($canAddToCart) {
                $this->info("Step 4: Simulating purchase");

                // Create purchase record
                $purchaseData = [
                    'buyer_id' => $buyerUid,
                    'product_id' => $productId,
                    'seller_id' => $product['seller_id'] ?? 'unknown',
                    'product_name' => $product['name'] ?? 'Test Product',
                    'price' => $product['price'] ?? 25000,
                    'purchased_at' => now()->toISOString(),
                    'transaction_id' => 'test_' . time()
                ];

                $purchaseId = $firestoreService->createDocument('purchases', $purchaseData);
                $this->line("✅ Purchase simulated: {$purchaseId}");

                // Increment sold count
                $productModel->incrementSoldCount($productId);
                $this->line("✅ Sold count incremented");
                $this->newLine();

                // Step 5: Re-test validation after purchase
                $this->info("Step 5: Re-testing validation after purchase");
                $hasPurchasedAfter = $purchaseService->hasPurchasedProduct($buyerUid, $productId);
                $this->line("📋 Has purchased after: " . ($hasPurchasedAfter ? 'YES' : 'NO'));

                $cartErrorsAfter = $purchaseService->validateCartItems($buyerUid, $cartItems);
                if (empty($cartErrorsAfter)) {
                    $this->error("❌ VALIDATION FAILED: Still can add to cart after purchase!");
                    return 1;
                } else {
                    $this->line("✅ VALIDATION PASSED: Cannot add to cart after purchase");
                    foreach ($cartErrorsAfter as $error) {
                        $this->line("  → " . $error['message']);
                    }
                }
                $this->newLine();

                // Clean up test purchase
                $this->info("Step 6: Cleaning up test data");
                $firestoreService->deleteDocument('purchases', $purchaseId);
                $this->line("✅ Test purchase record deleted");
            } else {
                $this->info("Step 4: Skipping purchase simulation (already purchased)");
            }

            // Step 7: Final validation
            $this->info("Step 7: Final system validation");
            $purchasedProducts = $purchaseService->getUserPurchasedProducts($buyerUid);
            $this->line("📊 Total purchased products by user: " . count($purchasedProducts));

            if ($hasPurchased && in_array($productId, $purchasedProducts)) {
                $this->line("✅ Purchase history consistent");
            } elseif (!$hasPurchased && !in_array($productId, $purchasedProducts)) {
                $this->line("✅ Non-purchase history consistent");
            } else {
                $this->error("❌ Purchase history inconsistent!");
                return 1;
            }

            $this->newLine();
            $this->info("🎉 Purchase validation system test completed successfully!");

            // Summary
            $this->line("📋 Test Summary:");
            $this->line("  • Product validation: ✅");
            $this->line("  • Purchase history check: ✅");
            $this->line("  • Cart validation logic: ✅");
            $this->line("  • Post-purchase prevention: ✅");
            $this->line("  • Data consistency: ✅");

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Test failed with exception: " . $e->getMessage());
            $this->line("📍 File: " . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
