<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CheckoutController;
use App\Models\Product;
use App\Services\FirestoreSimple;
use Illuminate\Http\Request;

class TestPurchaseSoldCount extends Command
{
    protected $signature = 'test:purchase-sold-count {buyer_uid} {seller_uid} {product_id}';
    protected $description = 'Test sold count increment during purchase';

    public function handle()
    {
        $buyerUid = $this->argument('buyer_uid');
        $sellerUid = $this->argument('seller_uid');
        $productId = $this->argument('product_id');

        $this->info('🧪 Testing sold count increment during purchase...');
        $this->info(str_repeat('=', 80));
        $this->line("Buyer: {$buyerUid}");
        $this->line("Seller: {$sellerUid}");
        $this->line("Product: {$productId}");
        $this->line("");

        try {
            $firestore = new FirestoreSimple();
            $productModel = new Product();

            // Check product exists
            $product = $firestore->getDocument('products', $productId);
            if (!$product) {
                $this->error('Product not found');
                return 1;
            }

            $this->info('📦 Product found:');
            $this->line("  Name: " . ($product['name'] ?? 'N/A'));
            $this->line("  Price: " . number_format($product['price'] ?? 0) . "đ");
            $this->line("  Current sold_count: " . ($product['sold_count'] ?? 0));
            $this->line("");

            // Check buyer has enough coins
            $buyer = $firestore->getDocument('users', $buyerUid);
            if (!$buyer) {
                $this->error('Buyer not found');
                return 1;
            }

            $buyerCoins = $buyer['coins'] ?? 0;
            $productPrice = $product['price'] ?? 0;

            $this->info('💰 Buyer status:');
            $this->line("  Current coins: " . number_format($buyerCoins));
            $this->line("  Product price: " . number_format($productPrice));

            if ($buyerCoins < $productPrice) {
                $this->error('Buyer does not have enough coins');
                return 1;
            }

            // Simulate purchase by calling incrementSoldCount directly
            $this->info('🛒 Simulating purchase (incrementing sold count)...');
            $oldSoldCount = $product['sold_count'] ?? 0;

            $result = $productModel->incrementSoldCount($productId);

            if ($result) {
                // Check the new sold count
                $updatedProduct = $firestore->getDocument('products', $productId);
                $newSoldCount = $updatedProduct['sold_count'] ?? 0;

                $this->line("✅ Sold count incremented:");
                $this->line("  Before: {$oldSoldCount}");
                $this->line("  After: {$newSoldCount}");

                if ($newSoldCount === $oldSoldCount + 1) {
                    $this->info("✅ Sold count increment verified!");

                    $this->line("");
                    $this->info("📋 Template Display Simulation:");
                    $this->line("  For seller (Sheet Nhạc Của Tôi):");
                    $this->line("    • Product: {$product['name']}");
                    $this->line("    • Sold Count: {$newSoldCount}");
                    $this->line("");
                    $this->line("  For buyer (after purchase):");
                    $this->line("    • Product: {$product['name']}");
                    $this->line("    • Sold Count: - (shows dash)");
                } else {
                    $this->error("❌ Sold count increment failed!");
                }
            } else {
                $this->error('❌ Failed to increment sold count');
            }

            $this->line("");
            $this->info("🎉 Test completed!");
        } catch (\Exception $e) {
            $this->error("Test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
