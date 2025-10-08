<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    // Mảng các seller demo
    $sellers = [
        [
            'email' => 'seller1@demo.com',
            'password' => 'seller123',
            'displayName' => 'Seller Demo 1',
            'role' => 'saler'
        ],
        [
            'email' => 'seller2@demo.com',
            'password' => 'seller123',
            'displayName' => 'Seller Demo 2',
            'role' => 'saler'
        ],
        [
            'email' => 'seller3@demo.com',
            'password' => 'seller123',
            'displayName' => 'Seller Demo 3',
            'role' => 'saler'
        ],
        [
            'email' => 'seller4@demo.com',
            'password' => 'seller123',
            'displayName' => 'Seller Demo 4',
            'role' => 'saler'
        ],
        [
            'email' => 'seller5@demo.com',
            'password' => 'seller123',
            'displayName' => 'Seller Demo 5',
            'role' => 'saler'
        ]
    ];

    echo "🚀 Bắt đầu tạo " . count($sellers) . " tài khoản seller...\n\n";

    foreach ($sellers as $index => $seller) {
        try {
            // Tạo user
            $userRecord = $auth->createUser([
                'email' => $seller['email'],
                'password' => $seller['password'],
                'displayName' => $seller['displayName'],
            ]);

            // Set custom claims (role)
            $auth->setCustomUserClaims($userRecord->uid, [
                'role' => $seller['role']
            ]);

            echo "✅ Tạo thành công: {$seller['displayName']} ({$seller['email']})\n";
            echo "   UID: {$userRecord->uid}\n";
            echo "   Role: {$seller['role']}\n\n";
        } catch (EmailExists $e) {
            echo "⚠️  Email {$seller['email']} đã tồn tại, bỏ qua...\n\n";
        } catch (Exception $e) {
            echo "❌ Lỗi tạo {$seller['email']}: {$e->getMessage()}\n\n";
        }
    }

    echo "🎉 Hoàn thành tạo tài khoản seller!\n";
    echo "📝 Thông tin đăng nhập:\n";
    echo "   Email: seller1@demo.com đến seller5@demo.com\n";
    echo "   Password: seller123\n";
    echo "   Role: saler\n";
} catch (Exception $e) {
    echo "💥 Lỗi: " . $e->getMessage() . "\n";
}
