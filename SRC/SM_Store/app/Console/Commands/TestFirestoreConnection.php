<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ActivityService;
use App\Services\FirestoreSimple;

class TestFirestoreConnection extends Command
{
    protected $signature = 'firestore:test';
    protected $description = 'Test Firestore connection and document creation';

    public function handle()
    {
        $this->info("Đang test kết nối Firestore...");

        $firestore = new FirestoreSimple();

        try {
            // Test simple document creation
            $testData = [
                'test_field' => 'test_value',
                'timestamp' => now()->toISOString(),
                'number' => 123
            ];

            $this->info("Đang tạo test document...");
            $docId = $firestore->createDocument('test_collection', $testData);

            if ($docId) {
                $this->info("✅ Document được tạo thành công với ID: " . $docId);

                // Try to retrieve it
                $this->info("Đang lấy document vừa tạo...");
                $document = $firestore->getDocument('test_collection', $docId);

                if ($document) {
                    $this->info("✅ Document được lấy thành công:");
                    $this->line(json_encode($document, JSON_PRETTY_PRINT));
                } else {
                    $this->error("❌ Không thể lấy document");
                }
            } else {
                $this->error("❌ Không thể tạo document");
            }
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }

        return 0;
    }
}
