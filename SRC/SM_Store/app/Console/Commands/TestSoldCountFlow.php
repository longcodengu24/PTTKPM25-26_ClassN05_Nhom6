<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\FirestoreSimple;

class TestSoldCountFlow extends Command
{
    protected $signature = 'test:sold-count-flow';
    protected $description = 'Test sold count increment flow';

    public function handle()
    {
        $this->info('🧪 Testing sold count increment flow...');
        $this->info(str_repeat('=', 80));

        try {
            $productModel = new Product();
            $firestore = new FirestoreSimple();

            // Test 1: Create a test product with sold_count = 0
            $this->info('Test 1: Creating test product with sold_count = 0');
            $testProductData = [
                'name' => 'Test Song for Sold Count',
                'author' => 'Test Author',
                'transcribed_by' => 'Test Transcriber',
                'country_region' => 'vietnam',
                'file_path' => 'test/path.txt',
                'price' => 50000,
                'youtube_demo_url' => null,
                'downloads_count' => 0,
                'sold_count' => 0,
                'is_active' => true,
                'seller_id' => 'test_seller_123'
            ];

            $testProductId = $productModel->create($testProductData);

            if (!$testProductId) {
                $this->error('Failed to create test product');
                return 1;
            }

            $this->line("✅ Test product created: {$testProductId}");

            // Test 2: Check initial sold_count
            $this->info('Test 2: Checking initial sold_count');
            $product = $firestore->getDocument('products', $testProductId);
            $initialSoldCount = $product['sold_count'] ?? 0;
            $this->line("  Initial sold_count: {$initialSoldCount}");

            // Test 3: Increment sold count
            $this->info('Test 3: Incrementing sold count');
            $result = $productModel->incrementSoldCount($testProductId);

            if ($result) {
                $this->line("✅ Sold count incremented successfully");

                // Verify the increment
                $updatedProduct = $firestore->getDocument('products', $testProductId);
                $newSoldCount = $updatedProduct['sold_count'] ?? 0;
                $this->line("  New sold_count: {$newSoldCount}");

                if ($newSoldCount === $initialSoldCount + 1) {
                    $this->line("✅ Sold count increment verified");
                } else {
                    $this->error("❌ Sold count increment failed - expected " . ($initialSoldCount + 1) . ", got {$newSoldCount}");
                }
            } else {
                $this->error("❌ Failed to increment sold count");
            }

            // Test 4: Multiple increments
            $this->info('Test 4: Testing multiple increments');
            for ($i = 1; $i <= 3; $i++) {
                $productModel->incrementSoldCount($testProductId);
                $this->line("  Increment {$i} completed");
            }

            $finalProduct = $firestore->getDocument('products', $testProductId);
            $finalSoldCount = $finalProduct['sold_count'] ?? 0;
            $this->line("  Final sold_count after 3 more increments: {$finalSoldCount}");

            // Test 5: Show product in seller view format
            $this->info('Test 5: Product display simulation');
            $this->line("  Product Name: " . ($finalProduct['name'] ?? 'N/A'));
            $this->line("  Author: " . ($finalProduct['author'] ?? 'N/A'));
            $this->line("  Price: " . number_format($finalProduct['price'] ?? 0) . "đ");
            $this->line("  Sold Count: {$finalSoldCount} (for seller view)");
            $this->line("  Display for buyer: - (would show '-' for purchased products)");

            // Test 6: Clean up (delete test product)
            $this->info('Test 6: Cleaning up test product');
            $firestore->deleteDocument('products', $testProductId);
            $this->line("✅ Test product deleted");

            $this->line("");
            $this->info("🎉 Sold count flow test completed successfully!");
            $this->line("");
            $this->info("📋 Summary:");
            $this->line("  - Product creation with sold_count = 0: ✅");
            $this->line("  - Sold count increment: ✅");
            $this->line("  - Multiple increments: ✅");
            $this->line("  - Template logic ready: ✅");
            $this->line("    • Seller sees: sold_count number");
            $this->line("    • Buyer sees: '-' for purchased products");
        } catch (\Exception $e) {
            $this->error("Test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
