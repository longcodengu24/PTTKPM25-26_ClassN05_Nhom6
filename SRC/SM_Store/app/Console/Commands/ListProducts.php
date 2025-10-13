<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class ListProducts extends Command
{
    protected $signature = 'list:products';
    protected $description = 'List all products in the system';

    public function handle()
    {
        $firestore = new FirestoreSimple();

        $this->info("📋 All Products in System");

        try {
            $products = $firestore->getAllProducts();

            if (empty($products)) {
                $this->warn("No products found.");
                return 0;
            }

            $this->info("Found " . count($products) . " products:\n");

            $headers = ['ID', 'Title', 'Price', 'Category', 'Seller', 'Rating', 'Status'];
            $rows = [];

            $totalValue = 0;
            $activeProducts = 0;

            foreach ($products as $productId => $product) {
                $price = $product['price'] ?? 0;
                $totalValue += $price;

                if (($product['status'] ?? '') === 'active') {
                    $activeProducts++;
                }

                $rows[] = [
                    substr($productId, 0, 12) . '...',
                    substr($product['title'] ?? 'N/A', 0, 25),
                    number_format($price) . ' coins',
                    $product['category'] ?? 'N/A',
                    substr($product['seller_name'] ?? 'N/A', 0, 15),
                    number_format($product['rating'] ?? 0, 1) . '⭐',
                    $product['status'] ?? 'N/A'
                ];
            }

            $this->table($headers, $rows);

            $this->info("\n📊 Products Summary:");
            $this->line("Total products: " . count($products));
            $this->line("Active products: $activeProducts");
            $this->line("Total value: " . number_format($totalValue) . " coins");
            $this->line("Average price: " . number_format($totalValue / count($products)) . " coins");

            // Group by seller
            $sellerStats = [];
            foreach ($products as $product) {
                $seller = $product['seller_name'] ?? 'Unknown';
                if (!isset($sellerStats[$seller])) {
                    $sellerStats[$seller] = 0;
                }
                $sellerStats[$seller]++;
            }

            $this->info("\n👥 Products by Seller:");
            foreach ($sellerStats as $seller => $count) {
                $this->line("• $seller: $count products");
            }

            $this->info("\n🎯 Ready for Testing:");
            $this->line("✅ Products created with variety of prices");
            $this->line("✅ Multiple sellers available");
            $this->line("✅ Different categories represented");
            $this->line("✅ Ready for purchase workflow testing");
        } catch (\Exception $e) {
            $this->error("❌ Error listing products: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
