<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirestoreSimple;

class RestoreUsersData extends Command
{
    protected $signature = 'users:restore';
    protected $description = 'Restore missing user data for users affected by coin update bug';

    // Dữ liệu backup từ thông tin đã biết
    private $knownUserData = [
        'KoIg7lvu61YxX5FNiLCdHmIUhbu1' => [
            'name' => 'khánhLê',
            'email' => 'lekhanh2k5@example.com', // cần xác nhận
            'role' => 'user', // hoặc 'saler' - cần xác nhận
            'avatar' => 'http://127.0.0.1:8000/img/avatars/avatar_KoIg7lvu61YxX5FNiLCdHmIUhbu1_1759736639.jpg'
        ],
        'cfT4zfDX4YRkuwd4T6X3seJhtbl1' => [
            'name' => 'Seller Demo 1', // cần xác nhận
            'email' => 'seller1@demo.com', // cần xác nhận  
            'role' => 'saler',
            'avatar' => ''
        ],
        'l0ETEytlFCQLRkJD49aLDFKluvk1' => [
            'name' => 'Seller Demo 2',
            'email' => 'seller2@demo.com',
            'role' => 'saler',
            'avatar' => ''
        ],
        'u3HETe7NwIOQvfmjQnly4x2VfEs1' => [
            'name' => 'Customer Demo 3',
            'email' => 'customer3@demo.com',
            'role' => 'user',
            'avatar' => ''
        ],
        'ysZ6O1vg4DfqrULFY2awZJYgjxx1' => [
            'name' => 'Seller Demo 3', // cần xác nhận
            'email' => 'seller3@demo.com', // cần xác nhận
            'role' => 'saler',
            'avatar' => ''
        ]
    ];

    public function handle()
    {
        $firestoreService = new FirestoreSimple();

        $this->info('=== BẮT ĐẦU PHỤC HỒI DỮ LIỆU USERS ===');

        foreach ($this->knownUserData as $userId => $userData) {
            try {
                $this->line("Đang phục hồi user: $userId");

                // Lấy dữ liệu hiện tại (để giữ lại coins)
                $currentData = $firestoreService->getDocument('users', $userId);
                $currentCoins = $currentData['coins'] ?? 0;

                // Merge dữ liệu: giữ coins hiện tại + thêm thông tin cá nhân
                $restoreData = array_merge($userData, ['coins' => $currentCoins]);

                // Cập nhật document
                $firestoreService->updateDocument('users', $userId, $restoreData);

                $this->info("✅ Đã phục hồi user $userId");
                $this->line("   Name: {$restoreData['name']}");
                $this->line("   Email: {$restoreData['email']}");
                $this->line("   Role: {$restoreData['role']}");
                $this->line("   Coins: {$restoreData['coins']}");
                $this->line("");
            } catch (\Exception $e) {
                $this->error("❌ Lỗi khi phục hồi user $userId: " . $e->getMessage());
            }
        }

        $this->info('=== HOÀN THÀNH PHỤC HỒI ===');

        // Kiểm tra lại kết quả
        $this->call('users:check');
    }
}
