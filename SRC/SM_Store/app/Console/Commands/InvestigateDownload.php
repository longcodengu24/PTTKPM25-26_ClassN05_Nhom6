<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class InvestigateDownload extends Command
{
    protected $signature = 'investigate:download';
    protected $description = 'Investigate specific download issues';

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== INVESTIGATING DOWNLOAD ISSUES ===');

        // Focus on problematic products
        $problematicProducts = ['5 Ngón Bàn Tay', 'Akaza\'Love Theme', 'MADE IN VIETNAM'];

        $purchasesResponse = $firestoreService->listDocuments('purchases');

        if (isset($purchasesResponse['documents'])) {
            foreach ($purchasesResponse['documents'] as $doc) {
                $docData = [];
                if (isset($doc['fields'])) {
                    foreach ($doc['fields'] as $field => $value) {
                        if (isset($value['stringValue'])) {
                            $docData[$field] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $docData[$field] = (int)$value['integerValue'];
                        }
                    }
                }

                $productName = $docData['product_name'] ?? '';

                if (in_array($productName, $problematicProducts)) {
                    $this->line("🔍 FOUND: $productName");
                    $this->line("   File path: " . ($docData['file_path'] ?? 'EMPTY'));
                    $this->line("   Seller ID: " . ($docData['seller_id'] ?? 'N/A'));
                    $this->line("   Buyer: " . ($docData['buyer_id'] ?? 'N/A'));

                    // Check actual files in seller directory
                    $sellerId = $docData['seller_id'] ?? '';
                    if ($sellerId) {
                        $sellerDir = public_path("seller_files/{$sellerId}/songs/vietnam");
                        if (is_dir($sellerDir)) {
                            $files = scandir($sellerDir);
                            $this->line("   Available files in seller directory:");
                            foreach ($files as $file) {
                                if ($file !== '.' && $file !== '..') {
                                    $this->line("     - $file");
                                }
                            }
                        }
                    }
                    $this->line("   ---");
                }
            }
        }

        $this->info('');
        $this->info('Next steps:');
        $this->line('1. Check if the file_path in purchase records is correct');
        $this->line('2. Test download with logging enabled');
        $this->line('3. Verify which file is actually being downloaded');
    }
}
