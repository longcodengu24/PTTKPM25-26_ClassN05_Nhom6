<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class FixPurchaseRecords extends Command
{
    protected $signature = 'fix:purchase-records';
    protected $description = 'Fix purchase records with missing or incorrect file paths';

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== FIXING PURCHASE RECORDS ===');

        $purchasesResponse = $firestoreService->listDocuments('purchases');
        $fixed = 0;
        $issues = 0;

        if (isset($purchasesResponse['documents'])) {
            foreach ($purchasesResponse['documents'] as $doc) {
                $docPath = $doc['name'] ?? '';
                $docId = basename($docPath);

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
                $filePath = $docData['file_path'] ?? '';
                $sellerId = $docData['seller_id'] ?? '';

                // Skip if has valid file path and file exists
                if (!empty($filePath) && file_exists(public_path($filePath))) {
                    continue;
                }

                $this->line("Processing: $productName (ID: $docId)");

                // Try to find matching file in seller directory
                if (!empty($sellerId) && !empty($productName)) {
                    $sellerDir = public_path("seller_files/{$sellerId}/songs/vietnam");

                    if (is_dir($sellerDir)) {
                        $files = glob($sellerDir . '/*');
                        $matchFound = false;

                        foreach ($files as $file) {
                            $fileName = basename($file);
                            $cleanFileName = preg_replace('/^\d+_/', '', $fileName);

                            // Check similarity
                            $similarity = 0;
                            similar_text(strtolower($cleanFileName), strtolower($productName), $similarity);

                            if ($similarity > 70) { // 70% similarity threshold
                                $newFilePath = "seller_files/{$sellerId}/songs/vietnam/" . $fileName;

                                // Update purchase record
                                try {
                                    $firestoreService->updateDocument('purchases', $docId, [
                                        'file_path' => $newFilePath
                                    ]);

                                    $this->info("✅ Fixed: $productName -> $newFilePath");
                                    $fixed++;
                                    $matchFound = true;
                                    break;
                                } catch (\Exception $e) {
                                    $this->error("Failed to update $docId: " . $e->getMessage());
                                }
                            }
                        }

                        if (!$matchFound) {
                            $this->warn("⚠️  No match found for: $productName");
                            $issues++;
                        }
                    }
                } else {
                    $this->warn("⚠️  Missing seller_id or product_name for: $docId");
                    $issues++;
                }
            }
        }

        $this->info("=== SUMMARY ===");
        $this->line("Fixed: $fixed records");
        $this->line("Issues remaining: $issues records");

        if ($fixed > 0) {
            $this->info("🎉 Purchase records have been updated!");
        }
    }
}
