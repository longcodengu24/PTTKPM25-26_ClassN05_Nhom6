<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CreateTestProducts extends Command
{
    protected $signature = 'create:test-products {--count=5}';
    protected $description = 'Create test products for purchase functionality testing';

    public function handle()
    {
        $firestore = new FirestoreSimple();
        $count = (int)$this->option('count');

        $this->info("🛍️  Creating Test Products");
        $this->line("Creating $count test products...\n");

        // Get sample sellers
        $sellers = [
            'cfT4zfDX4YRkuwd4T6X3seJhtbl1' => 'Seller Demo 1',
            'l0ETEytlFCQLRkJD49aLDFKluvk1' => 'Seller Demo 2',
            'ysZ6O1vg4DfqrULFY2awZJYgjxx1' => 'Seller Demo 3',
            'CArlDCWVs5Qo9rJR6aFAm58D6SI2' => 'Seller 4'
        ];

        $sampleProducts = [
            [
                'title' => 'Canon in D - Pachelbel',
                'description' => 'Sheet nhạc kinh điển Canon in D của Pachelbel, phù hợp cho piano solo.',
                'price' => 15000,
                'category' => 'Classical',
                'file_url' => 'vietnam/canon_in_d.pdf'
            ],
            [
                'title' => 'Moonlight Sonata - Beethoven',
                'description' => 'Bản sonata ánh trăng nổi tiếng của Beethoven, cấp độ trung cấp.',
                'price' => 20000,
                'category' => 'Classical',
                'file_url' => 'vietnam/moonlight_sonata.pdf'
            ],
            [
                'title' => 'Lá Cờ - Nhạc Việt Nam',
                'description' => 'Sheet nhạc bài hát Lá Cờ yêu nước, phù hợp cho guitar và vocal.',
                'price' => 12000,
                'category' => 'Vietnamese',
                'file_url' => 'vietnam/nha_toi_co_treo_mot_la_co.pdf'
            ],
            [
                'title' => 'Amazing Grace - Traditional',
                'description' => 'Bài hát tâm linh nổi tiếng, arrangement cho piano và vocal.',
                'price' => 18000,
                'category' => 'Traditional',
                'file_url' => 'vietnam/amazing_grace.pdf'
            ],
            [
                'title' => 'Tình Ca - Nhạc Trẻ Việt',
                'description' => 'Sheet nhạc tình ca hiện đại, phù hợp cho guitar fingerstyle.',
                'price' => 25000,
                'category' => 'Vietnamese',
                'file_url' => 'vietnam/tinh_ca.pdf'
            ],
            [
                'title' => 'Für Elise - Beethoven',
                'description' => 'Tác phẩm nổi tiếng nhất của Beethoven, cấp độ cơ bản.',
                'price' => 10000,
                'category' => 'Classical',
                'file_url' => 'vietnam/fur_elise.pdf'
            ],
            [
                'title' => 'Hotel California - Eagles',
                'description' => 'Guitar tabs và chords cho bài hát rock kinh điển.',
                'price' => 22000,
                'category' => 'Rock',
                'file_url' => 'vietnam/hotel_california.pdf'
            ]
        ];

        $created = 0;
        $failed = 0;
        $sellerKeys = array_keys($sellers);

        for ($i = 0; $i < $count; $i++) {
            try {
                // Select product and seller
                $productData = $sampleProducts[$i % count($sampleProducts)];
                $sellerUid = $sellerKeys[$i % count($sellerKeys)];
                $sellerName = $sellers[$sellerUid];

                // Generate unique product ID
                $productId = 'test_' . uniqid() . '_' . $i;

                // Add seller info and timestamp
                $productData['seller_uid'] = $sellerUid;
                $productData['seller_name'] = $sellerName;
                $productData['created_at'] = now()->toISOString();
                $productData['updated_at'] = now()->toISOString();
                $productData['status'] = 'active';
                $productData['downloads'] = 0;
                $productData['rating'] = rand(40, 50) / 10; // 4.0 to 5.0

                // Create product
                $result = $firestore->createDocument('products', $productData, $productId);

                if ($result) {
                    $created++;
                    $this->line("✅ Created: " . $productData['title'] . " by " . $sellerName . " (" . number_format($productData['price']) . " coins)");
                } else {
                    $failed++;
                    $this->error("❌ Failed to create: " . $productData['title']);
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("❌ Error creating product $i: " . $e->getMessage());
            }
        }

        $this->info("\n📊 Creation Summary:");
        $this->line("✅ Successfully created: $created products");
        if ($failed > 0) {
            $this->error("❌ Failed to create: $failed products");
        }

        if ($created > 0) {
            $this->info("\n🎯 Test Products Ready:");
            $this->line("1. $created products created with different prices");
            $this->line("2. Products assigned to different sellers");
            $this->line("3. Ready for purchase testing");
            $this->line("4. Each product has unique file path for download testing");

            $this->info("\n💡 Test Scenarios:");
            $this->line("• Test purchasing with sufficient coins");
            $this->line("• Test purchasing with insufficient coins");
            $this->line("• Test coin deduction from buyer");
            $this->line("• Test coin addition to seller");
            $this->line("• Test purchase history recording");
            $this->line("• Test download functionality");
        }

        return 0;
    }
}
