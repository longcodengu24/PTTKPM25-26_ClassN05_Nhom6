<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class FindSellerUID extends Command
{
    protected $signature = 'find:seller-uid {seller_name}';
    protected $description = 'Find seller UID by seller name';

    public function handle()
    {
        $sellerName = $this->argument('seller_name');
        $firestore = new FirestoreSimple();

        $this->info("Searching for seller: {$sellerName}");

        try {
            // Get all products and look for seller
            $response = $firestore->listDocuments('products', 50);
            $documents = $response['documents'] ?? [];

            $this->info("Checking " . count($documents) . " products...");

            $foundSellers = [];

            foreach ($documents as $doc) {
                $fields = $doc['fields'] ?? [];
                $productId = basename($doc['name'] ?? 'no-name');

                // Parse fields
                $sellerNameField = '';
                $sellerUidField = '';
                $title = '';

                if (isset($fields['seller_name']['stringValue'])) {
                    $sellerNameField = $fields['seller_name']['stringValue'];
                }
                if (isset($fields['seller_uid']['stringValue'])) {
                    $sellerUidField = $fields['seller_uid']['stringValue'];
                }
                if (isset($fields['title']['stringValue'])) {
                    $title = $fields['title']['stringValue'];
                }

                if (stripos($sellerNameField, $sellerName) !== false) {
                    $foundSellers[] = [
                        'product_id' => $productId,
                        'title' => $title,
                        'seller_name' => $sellerNameField,
                        'seller_uid' => $sellerUidField
                    ];
                }
            }

            if (empty($foundSellers)) {
                $this->error("No products found for seller: {$sellerName}");
                return;
            }

            $this->info("Found " . count($foundSellers) . " products:");
            foreach ($foundSellers as $seller) {
                $this->line("Product: {$seller['title']} (ID: {$seller['product_id']})");
                $this->line("Seller Name: {$seller['seller_name']}");
                $this->line("Seller UID: {$seller['seller_uid']}");
                $this->line("---");
            }

            // Get unique seller UIDs
            $uniqueUIDs = array_unique(array_column($foundSellers, 'seller_uid'));
            $this->info("Unique seller UIDs found:");
            foreach ($uniqueUIDs as $uid) {
                $this->line("• {$uid}");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
