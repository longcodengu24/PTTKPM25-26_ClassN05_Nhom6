<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class ShowPurchaseStructure extends Command
{
    protected $signature = 'purchases:show-structure {user_id?}';
    protected $description = 'Show current purchase structure';

    protected $firestore;

    public function __construct()
    {
        parent::__construct();
        $this->firestore = new FirestoreSimple();
    }

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info('📊 Current Purchase Structure');
        $this->info('=============================');
        $this->line('');

        try {
            if ($userId) {
                // Hiển thị cấu trúc cho user cụ thể
                $this->showUserStructure($userId);
            } else {
                // Hiển thị tổng quan
                $this->showOverview();
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            throw $e; // Re-throw để hiển thị lỗi đầy đủ
        }
    }

    private function showOverview()
    {
        $this->info('1. Overview of all purchases...');
        
        // Lấy tất cả purchases
        $purchases = $this->firestore->queryDocuments('purchases');
        
        if (empty($purchases)) {
            $this->info('   ℹ️ No purchases found');
            return;
        }

        $this->info("   Found " . count($purchases) . " purchase documents");

        // Phân tích cấu trúc
        $userCount = 0;
        $sheetCount = 0;
        $oldStructureCount = 0;
        $newStructureCount = 0;

        foreach ($purchases as $purchase) {
            // Kiểm tra xem có phải là user document (có total_purchases) hay không
            if (isset($purchase['total_purchases'])) {
                $newStructureCount++;
                $userCount++;
                
                // Kiểm tra xem có subcollection sheets không
                $sheets = $this->firestore->queryDocuments("purchases/{$purchase['user_id']}/sheets");
                $sheetCount += count($sheets);
            } else {
                $oldStructureCount++;
            }
        }

        $this->line('');
        $this->info('📈 Structure Analysis:');
        $this->info("   - Old structure documents: {$oldStructureCount}");
        $this->info("   - New structure user documents: {$newStructureCount}");
        $this->info("   - Total sheets in subcollections: {$sheetCount}");

        if ($oldStructureCount > 0) {
            $this->line('');
            $this->warn('⚠️ Found old structure purchases that need migration');
            $this->info('   Run: php artisan purchases:migrate-to-new-structure --dry-run');
        }

        if ($newStructureCount > 0) {
            $this->line('');
            $this->info('✅ New structure is in use');
            $this->info('   Structure: purchases/{user_id}/sheets/{sheet_id}');
        }
    }

    private function showUserStructure($userId)
    {
        $this->info("1. User structure for: {$userId}");
        
        // Kiểm tra user purchase document
        $userDoc = $this->firestore->getDocument('purchases', $userId);
        
        if ($userDoc !== null) {
            $this->info('   ✅ User purchase document found');
            $this->info("   - User ID: {$userDoc['user_id']}");
            $this->info("   - Total purchases: {$userDoc['total_purchases']}");
            $this->info("   - Last updated: {$userDoc['last_updated']}");
            $this->info("   - Created at: {$userDoc['created_at']}");

            // Kiểm tra sheets subcollection
            $this->line('');
            $this->info('2. Sheets subcollection...');
            $sheets = $this->firestore->listDocuments("purchases/{$userId}/sheets");
            
            if (!isset($sheets['documents']) || empty($sheets['documents'])) {
                $this->info('   ℹ️ No sheets found in subcollection');
            } else {
                $this->info("   Found " . count($sheets['documents']) . " sheets:");
                
                foreach ($sheets['documents'] as $index => $sheet) {
                    $sheetId = basename($sheet['name'] ?? '');
                    $sheetData = [];
                    if (isset($sheet['fields'])) {
                        foreach ($sheet['fields'] as $key => $field) {
                            if (isset($field['stringValue'])) {
                                $sheetData[$key] = $field['stringValue'];
                            } elseif (isset($field['doubleValue'])) {
                                $sheetData[$key] = $field['doubleValue'];
                            } elseif (isset($field['integerValue'])) {
                                $sheetData[$key] = $field['integerValue'];
                            } elseif (isset($field['booleanValue'])) {
                                $sheetData[$key] = $field['booleanValue'];
                            }
                        }
                    }
                    
                    $this->info("   - Sheet " . ($index + 1) . ":");
                    $this->info("     * ID: {$sheetId}");
                    $this->info("     * Title: " . ($sheetData['title'] ?? 'N/A'));
                    $this->info("     * Price: " . ($sheetData['price'] ?? 'N/A'));
                    $this->info("     * Seller: " . ($sheetData['seller_name'] ?? 'N/A'));
                    $this->info("     * Status: " . ($sheetData['status'] ?? 'N/A'));
                    $this->line('');
                }
            }

        } else {
            $this->warn('   ⚠️ User purchase document not found');
            
            // Kiểm tra xem có purchases cũ không
            $oldPurchases = $this->firestore->queryDocuments('purchases', [
                'where' => [
                    ['buyer_id', '==', $userId]
                ]
            ]);
            
            if (!empty($oldPurchases)) {
                $this->info("   Found " . count($oldPurchases) . " old structure purchases");
                $this->warn('   These need to be migrated to new structure');
            } else {
                $this->info('   No purchases found for this user');
            }
        }
    }
}
