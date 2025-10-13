<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CleanInvalidProducts extends Command
{
    protected $signature = 'clean:invalid-products {--confirm}';
    protected $description = 'Remove products with invalid or missing data';

    public function handle()
    {
        $firestore = new FirestoreSimple();

        $this->info("🧹 Cleaning Invalid Products");

        try {
            $products = $firestore->getAllProducts();

            if (empty($products)) {
                $this->info("No products found.");
                return 0;
            }

            // Find invalid products
            $invalidProducts = [];
            $validProducts = [];

            foreach ($products as $productId => $product) {
                $isInvalid = false;
                $reasons = [];

                // Check for missing title
                if (empty($product['title']) || $product['title'] === 'N/A') {
                    $isInvalid = true;
                    $reasons[] = 'Missing title';
                }

                // Check for missing seller info
                if (empty($product['seller_name']) || $product['seller_name'] === 'N/A') {
                    $isInvalid = true;
                    $reasons[] = 'Missing seller';
                }

                // Check for missing category
                if (empty($product['category']) || $product['category'] === 'N/A') {
                    $isInvalid = true;
                    $reasons[] = 'Missing category';
                }

                if ($isInvalid) {
                    $invalidProducts[$productId] = [
                        'data' => $product,
                        'reasons' => $reasons
                    ];
                } else {
                    $validProducts[$productId] = $product;
                }
            }

            if (empty($invalidProducts)) {
                $this->info("✅ All products are valid. No cleanup needed.");
                return 0;
            }

            $this->info("Found " . count($invalidProducts) . " invalid products:");

            $headers = ['ID', 'Title', 'Price', 'Issues'];
            $rows = [];

            foreach ($invalidProducts as $productId => $info) {
                $product = $info['data'];
                $rows[] = [
                    substr($productId, 0, 12) . '...',
                    substr($product['title'] ?? 'N/A', 0, 20),
                    number_format($product['price'] ?? 0) . ' coins',
                    implode(', ', $info['reasons'])
                ];
            }

            $this->table($headers, $rows);

            if (!$this->option('confirm')) {
                $this->warn("⚠️  This will DELETE " . count($invalidProducts) . " invalid products!");
                if (!$this->confirm('Proceed with cleanup?')) {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }

            // Delete invalid products
            $this->info("🗑️  Deleting invalid products...");
            $deleted = 0;
            $failed = 0;

            foreach ($invalidProducts as $productId => $info) {
                try {
                    $result = $firestore->deleteDocument('products', $productId);
                    if ($result) {
                        $deleted++;
                        $this->line("✅ Deleted: $productId");
                    } else {
                        $failed++;
                        $this->error("❌ Failed to delete: $productId");
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("❌ Error deleting $productId: " . $e->getMessage());
                }
            }

            $this->info("\n📊 Cleanup Summary:");
            $this->line("✅ Deleted: $deleted invalid products");
            $this->line("✅ Remaining: " . count($validProducts) . " valid products");
            if ($failed > 0) {
                $this->error("❌ Failed: $failed products");
            }

            if ($deleted > 0) {
                $this->info("\n🎯 Cleanup Complete:");
                $this->line("• Invalid products removed");
                $this->line("• Only valid test products remain");
                $this->line("• Ready for clean purchase testing");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error during cleanup: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
