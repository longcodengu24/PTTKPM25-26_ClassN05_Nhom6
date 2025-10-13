<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class DebugDownload extends Command
{
    protected $signature = 'debug:download';
    protected $description = 'Debug download issues';

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== DEBUGGING DOWNLOAD ISSUES ===');

        // 1. Check purchase records
        $this->info('1. Purchase Records:');
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

                if (isset($docData['product_name'])) {
                    $this->line("Product: " . $docData['product_name']);
                    $this->line("File path: " . ($docData['file_path'] ?? 'EMPTY'));
                    $this->line("Buyer: " . ($docData['buyer_id'] ?? 'N/A'));

                    // Check if file exists
                    $filePath = $docData['file_path'] ?? '';
                    if ($filePath) {
                        $fullPath = public_path($filePath);
                        $exists = file_exists($fullPath);
                        $this->line("File exists: " . ($exists ? 'YES' : 'NO'));
                        if (!$exists) {
                            $this->error("Missing file: $fullPath");
                        }
                    }
                    $this->line("---");
                }
            }
        }

        $this->info('');
        $this->info('2. Solution suggestions:');
        $this->line('- Files with empty file_path need to be updated');
        $this->line('- Files with non-existent paths need file_path correction');
        $this->line('- Remove fallback logic that causes wrong file downloads');
    }
}
