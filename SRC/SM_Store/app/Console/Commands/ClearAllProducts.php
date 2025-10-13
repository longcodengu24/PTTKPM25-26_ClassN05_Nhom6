<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class ClearAllProducts extends Command
{
    protected $signature = 'clear:products {--confirm}';
    protected $description = 'Clear all products from Firestore to start fresh testing';

    public function handle()
    {
        $firestore = new FirestoreSimple();

        $this->info("🗑️  Clearing All Products");

        if (!$this->option('confirm')) {
            $this->warn("⚠️  This will DELETE ALL PRODUCTS from Firestore!");
            $this->warn("This action cannot be undone.");

            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            // Get all products first
            $this->info("📋 Fetching all products...");
            $products = $firestore->getAllProducts();

            if (empty($products)) {
                $this->info("No products found to delete.");
                return 0;
            }

            $this->info("Found " . count($products) . " products to delete:");

            // Show products that will be deleted
            $headers = ['ID', 'Title', 'Price', 'Seller', 'Created'];
            $rows = [];

            foreach ($products as $productId => $product) {
                $rows[] = [
                    substr($productId, 0, 12) . '...',
                    substr($product['title'] ?? 'N/A', 0, 20),
                    number_format($product['price'] ?? 0) . ' coins',
                    substr($product['seller_name'] ?? 'N/A', 0, 15),
                    isset($product['created_at']) ? date('Y-m-d', strtotime($product['created_at'])) : 'N/A'
                ];
            }

            $this->table($headers, $rows);

            if (!$this->option('confirm')) {
                if (!$this->confirm('Proceed with deletion of these ' . count($products) . ' products?')) {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }

            // Delete all products
            $this->info("🔥 Deleting products...");
            $deleted = 0;
            $failed = 0;

            foreach ($products as $productId => $product) {
                try {
                    $result = $firestore->deleteDocument('products', $productId);
                    if ($result) {
                        $deleted++;
                        $this->line("✅ Deleted: " . ($product['title'] ?? $productId));
                    } else {
                        $failed++;
                        $this->error("❌ Failed to delete: " . ($product['title'] ?? $productId));
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("❌ Error deleting " . ($product['title'] ?? $productId) . ": " . $e->getMessage());
                }
            }

            $this->info("\n📊 Deletion Summary:");
            $this->line("✅ Successfully deleted: $deleted products");
            if ($failed > 0) {
                $this->error("❌ Failed to delete: $failed products");
            }

            if ($deleted > 0) {
                $this->info("\n🎯 Next Steps:");
                $this->line("1. Products collection is now empty");
                $this->line("2. You can create new test products");
                $this->line("3. Test purchase functionality with fresh data");
                $this->line("4. All purchase history remains intact");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error clearing products: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
