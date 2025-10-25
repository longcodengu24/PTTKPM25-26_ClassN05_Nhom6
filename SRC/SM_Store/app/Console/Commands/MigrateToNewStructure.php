<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UserPurchaseService;
use App\Services\FirestoreSimple;

class MigrateToNewStructure extends Command
{
    protected $signature = 'purchases:migrate-to-new-structure {--dry-run : Show what would be migrated without actually doing it}';
    protected $description = 'Migrate existing purchases to new subcollection structure';

    protected $userPurchaseService;
    protected $firestore;

    public function __construct()
    {
        parent::__construct();
        $this->userPurchaseService = new UserPurchaseService();
        $this->firestore = new FirestoreSimple();
    }

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔄 Migrating purchases to new subcollection structure...');
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        $this->info('==================================================');
        $this->line('');

        try {
            // 1. Lấy tất cả purchases hiện tại
            $this->info('1. Getting existing purchases...');
            $existingPurchases = $this->firestore->queryDocuments('purchases');
            
            if (empty($existingPurchases)) {
                $this->info('   ℹ️ No existing purchases found');
                return;
            }

            $this->info("   Found " . count($existingPurchases) . " existing purchases");

            // 2. Nhóm purchases theo user_id
            $userPurchases = [];
            foreach ($existingPurchases as $purchase) {
                $buyerId = $purchase['buyer_id'] ?? null;
                if ($buyerId) {
                    if (!isset($userPurchases[$buyerId])) {
                        $userPurchases[$buyerId] = [];
                    }
                    $userPurchases[$buyerId][] = $purchase;
                }
            }

            $this->info("   Grouped into " . count($userPurchases) . " users");

            // 3. Migrate từng user
            $totalMigrated = 0;
            $totalSheets = 0;

            foreach ($userPurchases as $userId => $purchases) {
                $this->info("\n2. Processing user: {$userId} (" . count($purchases) . " purchases)");
                
                if ($isDryRun) {
                    $this->info("   🔍 Would migrate " . count($purchases) . " purchases to subcollection");
                    continue;
                }

                // Tạo user purchase document
                $userPurchaseData = [
                    'user_id' => $userId,
                    'total_purchases' => count($purchases),
                    'last_updated' => now()->toIso8601String(),
                    'created_at' => now()->toIso8601String()
                ];

                // Lưu user purchase document
                $userResult = $this->firestore->createDocument('purchases', $userId, $userPurchaseData);
                
                if (!$userResult) {
                    $this->error("   ❌ Failed to create user purchase document for: {$userId}");
                    continue;
                }

                $this->info("   ✅ Created user purchase document: {$userId}");

                // Migrate từng purchase thành sheet
                $sheetsCreated = 0;
                foreach ($purchases as $purchase) {
                    $sheetData = [
                        'category' => $purchase['category'] ?? '',
                        'description' => $purchase['description'] ?? '',
                        'file_url' => $purchase['file_path'] ?? '',
                        'price' => floatval($purchase['price'] ?? 0),
                        'purchased_at' => $purchase['purchased_at'] ?? now()->toIso8601String(),
                        'rating' => 0,
                        'seller_name' => $purchase['author'] ?? '',
                        'seller_uid' => $purchase['seller_id'] ?? '',
                        'status' => $purchase['status'] ?? 'active',
                        'title' => $purchase['product_name'] ?? '',
                        'product_id' => $purchase['product_id'] ?? '',
                        'transaction_id' => $purchase['transaction_id'] ?? '',
                        'image_path' => $purchase['image_path'] ?? '',
                        'buyer_id' => $purchase['buyer_id'] ?? $userId
                    ];

                    // Tạo sheet ID
                    $sheetId = 'sheet_' . time() . '_' . substr(md5($purchase['product_id'] ?? ''), 0, 8);
                    
                    // Lưu sheet vào subcollection
                    $sheetResult = $this->firestore->createDocument("purchases/{$userId}/sheets", $sheetId, $sheetData);
                    
                    if ($sheetResult) {
                        $sheetsCreated++;
                        $this->info("   ✅ Created sheet: {$sheetId} - " . ($purchase['product_name'] ?? 'N/A'));
                    } else {
                        $this->error("   ❌ Failed to create sheet: {$sheetId}");
                    }
                }

                $totalMigrated++;
                $totalSheets += $sheetsCreated;
                $this->info("   📊 User {$userId}: {$sheetsCreated} sheets created");
            }

            $this->line('');
            $this->info('📈 Migration Summary:');
            $this->info("   - Users processed: {$totalMigrated}");
            $this->info("   - Total sheets created: {$totalSheets}");
            
            if (!$isDryRun && $totalSheets > 0) {
                $this->line('');
                $this->info('💡 New structure created:');
                $this->info('   - Collection: purchases');
                $this->info('   - Document: {user_id}');
                $this->info('   - Subcollection: sheets');
                $this->info('   - Sheet documents: sheet_{timestamp}_{hash}');
                $this->line('');
                $this->info('🔍 How to view in Firebase Console:');
                $this->info('   1. Go to Firebase Console → Firestore Database');
                $this->info('   2. Click on "purchases" collection');
                $this->info('   3. Click on any user document (e.g., user_id)');
                $this->info('   4. You will see "sheets" subcollection');
                $this->info('   5. Click on "sheets" to see all sheet documents');
            }

        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
