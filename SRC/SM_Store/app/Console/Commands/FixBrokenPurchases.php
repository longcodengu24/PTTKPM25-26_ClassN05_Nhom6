<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class FixBrokenPurchases extends Command
{
    protected $signature = 'fix:broken-purchases';
    protected $description = 'Fix purchase records with incorrect file paths';

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== FIXING BROKEN PURCHASE RECORDS ===');

        // Mapping của files thực tế có sẵn
        $actualFiles = [
            'l0ETEytlFCQLRkJD49aLDFKluvk1' => [
                '1760288132_NHÀ TÔI CÓ TREO MỘT LÁ CỜ (2).txt',
                '1760288609_Akaza\'Love Theme (2).txt'
            ],
            'ysZ6O1vg4DfqrULFY2awZJYgjxx1' => [
                '1759933468_NHÀ TÔI CÓ TREO MỘT LÁ CỜ (2).txt',
                '1759934896_Shine in the Cruel Night.txt',
                '1759935027_Akaza\'Love Theme (2).txt',
                '1759941263_Giữ anh cho ngày hôm qua.txt',
                '1759941513_Kho Báu (1).txt',
                '1760029189_Thằng cuội (TTHVTCX).txt'
            ]
        ];

        $purchasesResponse = $firestoreService->listDocuments('purchases');
        $fixed = 0;
        $failed = 0;

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

                // Skip if file exists
                if (!empty($filePath) && file_exists(public_path($filePath))) {
                    continue;
                }

                $this->line("Processing: $productName");

                // Try to find exact match
                if (isset($actualFiles[$sellerId])) {
                    $matchFound = false;

                    foreach ($actualFiles[$sellerId] as $file) {
                        $cleanFileName = preg_replace('/^\d+_/', '', $file);
                        $cleanFileName = str_replace(['(', ')'], '', $cleanFileName);
                        $cleanFileName = trim($cleanFileName);

                        $cleanProductName = str_replace(['(', ')'], '', $productName);
                        $cleanProductName = trim($cleanProductName);

                        // Exact match after cleaning
                        if (strcasecmp($cleanFileName, $cleanProductName) === 0) {
                            $newFilePath = "seller_files/{$sellerId}/songs/vietnam/{$file}";

                            try {
                                $firestoreService->updateDocument('purchases', $docId, [
                                    'file_path' => $newFilePath
                                ]);

                                $this->info("✅ Fixed: $productName → $file");
                                $fixed++;
                                $matchFound = true;
                                break;
                            } catch (\Exception $e) {
                                $this->error("Failed to update: " . $e->getMessage());
                                $failed++;
                            }
                        }
                    }

                    if (!$matchFound) {
                        $this->warn("⚠️ No exact match for: $productName");
                        $failed++;
                    }
                } else {
                    $this->warn("⚠️ Unknown seller: $sellerId");
                    $failed++;
                }
            }
        }

        $this->info("=== RESULTS ===");
        $this->line("✅ Fixed: $fixed");
        $this->line("❌ Failed: $failed");

        if ($fixed > 0) {
            $this->info("🎉 Purchase records updated! Test downloads again.");
        }
    }
}
