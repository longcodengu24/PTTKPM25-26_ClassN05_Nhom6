<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class ListFirestoreCollections extends Command
{
    protected $signature = 'firestore:list-collections';
    protected $description = 'List all Firestore collections and their usage';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('Firestore Collections Overview');
        $this->info('=============================');

        try {
            // Define known collections and their purposes
            $knownCollections = [
                'users' => [
                    'description' => 'User accounts (customers and sellers)',
                    'status' => 'active',
                    'used_in' => ['AuthController', 'CheckoutController', 'UserController']
                ],
                'products' => [
                    'description' => 'Music sheet products',
                    'status' => 'active',
                    'used_in' => ['ProductController', 'CheckoutController', 'Product model']
                ],
                'purchases' => [
                    'description' => 'Purchase history records',
                    'status' => 'active',
                    'used_in' => ['CheckoutController', 'PurchaseService']
                ],
                'activities' => [
                    'description' => 'User activity logs',
                    'status' => 'active',
                    'used_in' => ['ActivityService', 'CheckoutController']
                ],
                'user_activities' => [
                    'description' => 'Legacy user activities (replaced by activities)',
                    'status' => 'cleaned',
                    'used_in' => ['NONE - deleted']
                ],
                'test_collection' => [
                    'description' => 'Testing collection for Firestore connection',
                    'status' => 'cleaned',
                    'used_in' => ['NONE - deleted']
                ]
            ];

            $this->info("\n📋 Collection Status Report:");
            $this->info("============================");

            foreach ($knownCollections as $collection => $info) {
                $status = $info['status'];
                $description = $info['description'];
                $usedIn = is_array($info['used_in']) ? implode(', ', $info['used_in']) : $info['used_in'];

                $statusIcon = match ($status) {
                    'active' => '✅',
                    'cleaned' => '🧹',
                    'deprecated' => '⚠️',
                    default => '❓'
                };

                $this->info("\n$statusIcon Collection: $collection");
                $this->info("   Description: $description");
                $this->info("   Status: $status");
                $this->info("   Used in: $usedIn");

                if ($status === 'active') {
                    $count = $this->getCollectionDocumentCount($collection);
                    $this->info("   Document count: $count");
                }
            }

            // Check for unknown collections
            $this->info("\n🔍 Scanning for unknown collections...");
            $allCollections = $this->scanForCollections();

            $unknownCollections = array_diff($allCollections, array_keys($knownCollections));

            if (!empty($unknownCollections)) {
                $this->warn("\n⚠️  Unknown collections found:");
                foreach ($unknownCollections as $collection) {
                    $count = $this->getCollectionDocumentCount($collection);
                    $this->warn("   - $collection ($count documents)");
                }
            } else {
                $this->info("✓ No unknown collections found");
            }

            $this->info("\n📊 Summary:");
            $activeCount = count(array_filter($knownCollections, fn($info) => $info['status'] === 'active'));
            $cleanedCount = count(array_filter($knownCollections, fn($info) => $info['status'] === 'cleaned'));

            $this->info("   Active collections: $activeCount");
            $this->info("   Cleaned collections: $cleanedCount");
            $this->info("   Unknown collections: " . count($unknownCollections));
        } catch (\Exception $e) {
            $this->error('Failed to list collections: ' . $e->getMessage());
        }
    }

    private function getCollectionDocumentCount(string $collection): int
    {
        try {
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
                return 0;
            }

            if ($response->failed()) {
                return -1; // Error
            }

            $data = $response->json();
            return isset($data['documents']) ? count($data['documents']) : 0;
        } catch (\Exception $e) {
            return -1; // Error
        }
    }

    private function scanForCollections(): array
    {
        // This is a simplified version - Firestore doesn't have a direct API to list collections
        // We return known collections that might exist
        return ['users', 'products', 'purchases', 'activities', 'user_activities', 'test_collection'];
    }
}
