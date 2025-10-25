<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreRestService;

class ShowUserPurchases extends Command
{
    protected $signature = 'purchases:show-user-structure';
    protected $description = 'Show user-based purchases structure';

    protected $firestoreService;

    public function __construct()
    {
        parent::__construct();
        $this->firestoreService = new FirestoreRestService();
    }

    public function handle()
    {
        $this->info('👤 User-based Purchases Structure');
        $this->info('==================================');
        $this->line('');

        try {
            // Lấy tất cả users
            $usersResponse = $this->firestoreService->listDocuments('users');
            
            if (!isset($usersResponse['documents'])) {
                $this->error('❌ No users found');
                return;
            }

            $totalUsers = count($usersResponse['documents']);
            $usersWithPurchases = 0;

            $this->info("📊 Found {$totalUsers} users in Firestore");
            $this->line('');

            foreach ($usersResponse['documents'] as $userDoc) {
                $userId = $userDoc['name'] ?? '';
                $userId = substr($userId, strrpos($userId, '/') + 1);
                
                // Kiểm tra xem user có purchases không
                $purchaseDoc = $this->firestoreService->getDocument('purchases', $userId);
                
                if ($purchaseDoc['success']) {
                    $usersWithPurchases++;
                    $data = $purchaseDoc['data'];
                    
                    $this->info("✅ User: {$userId}");
                    $this->info("   📦 Total purchases: " . ($data['total_purchases'] ?? 0));
                    $this->info("   📅 Last updated: " . ($data['last_updated'] ?? 'N/A'));
                    $this->info("   📅 Created at: " . ($data['created_at'] ?? 'N/A'));
                    
                    // Parse sheets JSON
                    $sheets = json_decode($data['sheets'] ?? '[]', true);
                    if (is_array($sheets) && count($sheets) > 0) {
                        $this->info("   🎼 Purchased sheets:");
                        foreach ($sheets as $index => $sheet) {
                            $this->line("      " . ($index + 1) . ". " . ($sheet['product_name'] ?? 'N/A'));
                            $this->line("         - Price: " . ($sheet['price'] ?? 'N/A'));
                            $this->line("         - Status: " . ($sheet['status'] ?? 'N/A'));
                            $this->line("         - Date: " . ($sheet['purchased_at'] ?? 'N/A'));
                        }
                    }
                    $this->line('');
                } else {
                    $this->line("⚪ User: {$userId} - No purchases");
                }
            }

            $this->line('');
            $this->info('📈 Summary:');
            $this->info("   - Total users: {$totalUsers}");
            $this->info("   - Users with purchases: {$usersWithPurchases}");
            $this->line('');
            $this->info('💡 Structure:');
            $this->info('   - Collection: purchases');
            $this->info('   - Document ID: {user_id}');
            $this->info('   - Fields: user_id, sheets (JSON), total_purchases, last_updated, created_at');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
