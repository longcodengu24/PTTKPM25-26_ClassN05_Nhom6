<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\FirestoreSimple;

class CreateTestProduct extends Command
{
    protected $signature = 'create:test-product {seller_uid}';
    protected $description = 'Create a test product for testing sold count';

    public function handle()
    {
        $sellerUid = $this->argument('seller_uid');

        $this->info('🏗️ Creating test product...');
        $this->line("Seller: {$sellerUid}");

        try {
            $productModel = new Product();

            $testProductData = [
                'name' => 'Test Song for Sold Count Demo',
                'author' => 'Test Author',
                'transcribed_by' => 'Test Transcriber',
                'country_region' => 'vietnam',
                'file_path' => 'test/demo_song.txt',
                'price' => 25000,
                'youtube_demo_url' => null,
                'downloads_count' => 0,
                'sold_count' => 0,
                'is_active' => true,
                'seller_id' => $sellerUid
            ];

            $productId = $productModel->create($testProductData);

            $this->line("");
            $this->info("✅ Test product created successfully!");
            $this->line("Product ID: {$productId}");
            $this->line("Name: {$testProductData['name']}");
            $this->line("Price: " . number_format($testProductData['price']) . "đ");
            $this->line("Initial sold_count: 0");
            $this->line("");
            $this->info("📋 Now you can test with:");
            $this->line("php artisan test:purchase-sold-count {buyer_uid} {$sellerUid} {$productId}");
        } catch (\Exception $e) {
            $this->error("Failed to create test product: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
