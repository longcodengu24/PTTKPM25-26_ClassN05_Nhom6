<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class CleanupUnusedCollections extends Command
{
    protected $signature = 'cleanup:unused-collections';
    protected $description = 'Clean up unused collections from Firestore';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('Cleaning up unused collections...');
        $this->info('==================================');

        try {
            // List of collections to check and potentially remove
            $collectionsToCheck = [
                'user_activities' => 'User activities collection - not used in current codebase',
                'test_collection' => 'Test collection - only used for testing Firestore connection'
            ];

            foreach ($collectionsToCheck as $collection => $description) {
                $this->info("\n🔍 Checking collection: $collection");
                $this->info("   Description: $description");

                // Check if collection has any documents
                $documents = $this->getCollectionDocuments($collection);

                if (empty($documents)) {
                    $this->info("   ✓ Collection is empty or doesn't exist");
                    continue;
                }

                $count = count($documents);
                $this->warn("   ⚠️  Collection has $count documents");

                // Show first few document IDs
                $sampleIds = array_slice(array_keys($documents), 0, 5);
                $this->info("   Sample document IDs: " . implode(', ', $sampleIds));

                if ($this->confirm("   Do you want to delete all documents in '$collection'?")) {
                    $this->deleteCollectionDocuments($collection, $documents);
                    $this->info("   ✅ Collection '$collection' cleaned");
                } else {
                    $this->info("   ⏭️  Skipped collection '$collection'");
                }
            }

            $this->info("\n✅ Cleanup completed!");
        } catch (\Exception $e) {
            $this->error('Cleanup failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    private function getCollectionDocuments(string $collection): array
    {
        try {
            // Get all documents in collection using reflection to access private properties
            $reflection = new \ReflectionClass($this->firestoreService);
            $baseUrlProperty = $reflection->getProperty('baseUrl');
            $baseUrlProperty->setAccessible(true);
            $baseUrl = $baseUrlProperty->getValue($this->firestoreService);

            $apiKeyProperty = $reflection->getProperty('apiKey');
            $apiKeyProperty->setAccessible(true);
            $apiKey = $apiKeyProperty->getValue($this->firestoreService);

            $url = $baseUrl . "/{$collection}?key=" . $apiKey;
            $response = \Illuminate\Support\Facades\Http::get($url);

            if ($response->status() === 404) {
                return []; // Collection doesn't exist
            }

            if ($response->failed()) {
                throw new \Exception("Failed to get collection documents: " . $response->body());
            }

            $data = $response->json();
            $documents = [];

            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $doc) {
                    $docId = basename($doc['name'] ?? '');
                    if ($docId) {
                        $documents[$docId] = $doc;
                    }
                }
            }

            return $documents;
        } catch (\Exception $e) {
            $this->error("Error getting documents from $collection: " . $e->getMessage());
            return [];
        }
    }

    private function deleteCollectionDocuments(string $collection, array $documents): void
    {
        $deleted = 0;
        $failed = 0;

        foreach (array_keys($documents) as $docId) {
            try {
                $this->firestoreService->deleteDocument($collection, $docId);
                $deleted++;

                if ($deleted % 10 === 0) {
                    $this->info("     Deleted $deleted documents...");
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("     Failed to delete document $docId: " . $e->getMessage());
            }
        }

        $this->info("   📊 Results: $deleted deleted, $failed failed");
    }
}
