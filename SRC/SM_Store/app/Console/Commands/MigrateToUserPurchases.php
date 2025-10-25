<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreRestService;
use App\Services\FirestoreSimple;

class MigrateToUserPurchases extends Command
{
    protected $signature = 'purchases:migrate-to-user-structure';
    protected $description = 'Migrate purchases to user-based structure';

    protected $firestoreRest;
    protected $firestoreSimple;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreRest = new FirestoreRestService();
        $this->firestoreSimple = new FirestoreSimple();
    }

    public function handle()
    {
        $this->info('🔄 Migrating to user-based purchases structure...');
        $this->info('================================================');
        $this->line('');

        try {
            // 1. Lấy tất cả purchases hiện tại
            $this->info('1. Getting current purchases...');
            $purchasesResponse = $this->firestoreSimple->listDocuments('purchases');
            
            if (!isset($purchasesResponse['documents'])) {
                $this->error('❌ No purchases found');
                return;
            }

            $purchases = $purchasesResponse['documents'];
            $this->info("   Found " . count($purchases) . " purchases");

            // 2. Nhóm purchases theo user
            $this->info('\n2. Grouping purchases by user...');
            $userPurchases = [];
            
            foreach ($purchases as $purchaseDoc) {
                $fields = $purchaseDoc['fields'] ?? [];
                $data = [];
                
                foreach ($fields as $key => $value) {
                    if (isset($value['stringValue'])) {
                        $data[$key] = $value['stringValue'];
                    } elseif (isset($value['integerValue'])) {
                        $data[$key] = $value['integerValue'];
                    } elseif (isset($value['doubleValue'])) {
                        $data[$key] = $value['doubleValue'];
                    } elseif (isset($value['booleanValue'])) {
                        $data[$key] = $value['booleanValue'];
                    }
                }

                $userId = $data['buyer_id'] ?? 'unknown';
                
                if (!isset($userPurchases[$userId])) {
                    $userPurchases[$userId] = [];
                }
                
                $userPurchases[$userId][] = $data;
            }

            $this->info("   Grouped into " . count($userPurchases) . " users");

            // 3. Tạo cấu trúc mới cho từng user
            $this->info('\n3. Creating new user-based structure...');
            $totalMigrated = 0;

            foreach ($userPurchases as $userId => $userSheets) {
                $sheetCount = count($userSheets);
                $this->info("   Processing user: {$userId} ({$sheetCount} sheets)");
                
                // Tạo cấu trúc mới - chuyển array thành string JSON
                $userPurchaseData = [
                    'user_id' => $userId,
                    'sheets' => json_encode($userSheets), // Chuyển array thành JSON string
                    'total_purchases' => count($userSheets),
                    'last_updated' => now()->toIso8601String(),
                    'created_at' => now()->toIso8601String()
                ];

                // Lưu vào collection purchases với document ID là user_id
                $result = $this->firestoreRest->createDocument('purchases', $userId, $userPurchaseData);
                
                if ($result['success']) {
                    $this->info("   ✅ Created user purchase document: {$userId}");
                    $totalMigrated++;
                } else {
                    $this->error("   ❌ Failed to create document for user: {$userId}");
                }
            }

            $this->line('');
            $this->info('📈 Migration Summary:');
            $this->info("   - Users processed: " . count($userPurchases));
            $this->info("   - Successfully migrated: {$totalMigrated}");
            
            if ($totalMigrated > 0) {
                $this->line('');
                $this->info('💡 New structure:');
                $this->info('   - Collection: purchases');
                $this->info('   - Document ID: {user_id}');
                $this->info('   - Fields: user_id, sheets[], total_purchases, last_updated, created_at');
                $this->line('');
                $this->warn('⚠️  Next steps:');
                $this->warn('   1. Update your code to use the new structure');
                $this->warn('   2. Test the new functionality');
                $this->warn('   3. Delete old individual purchase documents if everything works');
            }

        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
