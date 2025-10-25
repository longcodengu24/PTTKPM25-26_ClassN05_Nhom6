<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreRestService;
use App\Services\FirestoreSimple;

class MigrateToSheetsSubcollection extends Command
{
    protected $signature = 'purchases:migrate-to-sheets-subcollection';
    protected $description = 'Migrate to sheets subcollection structure';

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
        $this->info('🔄 Migrating to sheets subcollection structure...');
        $this->info('================================================');
        $this->line('');

        try {
            // 1. Lấy user purchase document hiện tại
            $this->info('1. Getting current user purchase document...');
            $userPurchaseDoc = $this->firestoreRest->getDocument('purchases', 'l0ETEytlFCQLRkJD49aLDFKluvk1');
            
            if (!$userPurchaseDoc['success']) {
                $this->error('❌ User purchase document not found');
                return;
            }

            $data = $userPurchaseDoc['data'];
            $sheets = json_decode($data['sheets'] ?? '[]', true);
            
            if (!is_array($sheets) || count($sheets) === 0) {
                $this->error('❌ No sheets found in user purchase document');
                return;
            }

            $this->info("   Found " . count($sheets) . " sheets to migrate");

            // 2. Tạo cấu trúc mới với sheets subcollection
            $this->info('\n2. Creating new structure with sheets subcollection...');
            
            // Tạo user purchase document mới (không có sheets field)
            $userPurchaseData = [
                'user_id' => $data['user_id'],
                'total_purchases' => $data['total_purchases'],
                'last_updated' => now()->toIso8601String(),
                'created_at' => $data['created_at']
            ];

            // Lưu user purchase document
            $result = $this->firestoreRest->createDocument('purchases', $data['user_id'], $userPurchaseData);
            
            if (!$result['success']) {
                $this->error('❌ Failed to create user purchase document');
                return;
            }

            $this->info("   ✅ Created user purchase document: {$data['user_id']}");

            // 3. Tạo sheets subcollection
            $this->info('\n3. Creating sheets subcollection...');
            $totalSheets = 0;

            foreach ($sheets as $index => $sheet) {
                // Tạo sheet ID
                $sheetId = 'sheet_' . time() . '_' . $index;
                
                // Tạo sheet document
                $sheetData = [
                    'category' => $sheet['category'] ?? '',
                    'description' => $sheet['description'] ?? '',
                    'file_url' => $sheet['file_path'] ?? '',
                    'price' => $sheet['price'] ?? 0,
                    'purchased_at' => $sheet['purchased_at'] ?? now()->toIso8601String(),
                    'rating' => 0,
                    'seller_name' => $sheet['author'] ?? '',
                    'seller_uid' => $sheet['seller_id'] ?? '',
                    'status' => $sheet['status'] ?? 'active',
                    'title' => $sheet['product_name'] ?? '',
                    'product_id' => $sheet['product_id'] ?? '',
                    'transaction_id' => $sheet['transaction_id'] ?? ''
                ];

                // Lưu vào subcollection sheets
                $sheetResult = $this->firestoreSimple->createDocument("purchases/{$data['user_id']}/sheets", $sheetId, $sheetData);
                
                if ($sheetResult) {
                    $this->info("   ✅ Created sheet: {$sheetId} - " . ($sheet['product_name'] ?? 'N/A'));
                    $totalSheets++;
                } else {
                    $this->error("   ❌ Failed to create sheet: {$sheetId}");
                }
            }

            $this->line('');
            $this->info('📈 Migration Summary:');
            $this->info("   - User: {$data['user_id']}");
            $this->info("   - Sheets migrated: {$totalSheets}");
            
            if ($totalSheets > 0) {
                $this->line('');
                $this->info('💡 New structure:');
                $this->info('   - Collection: purchases');
                $this->info('   - Document: {user_id}');
                $this->info('   - Subcollection: sheets');
                $this->info('   - Sheet documents: sheet_{timestamp}_{index}');
                $this->line('');
                $this->info('🔍 How to view in Firebase Console:');
                $this->info('   1. Go to Firebase Console → Firestore Database');
                $this->info('   2. Click on "purchases" collection');
                $this->info('   3. Click on user document (e.g., l0ETEytlFCQLRkJD49aLDFKluvk1)');
                $this->info('   4. You will see "sheets" subcollection');
                $this->info('   5. Click on "sheets" to see all sheet documents');
            }

        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
