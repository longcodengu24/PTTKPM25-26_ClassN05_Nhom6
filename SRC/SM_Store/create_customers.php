<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    // Mảng các customer demo
    $customers = [];

    // Tạo 10 tài khoản customer tự động
    for ($i = 1; $i <= 10; $i++) {
        $customers[] = [
            'email' => "customer{$i}@demo.com",
            'password' => 'customer123',
            'displayName' => "Customer Demo {$i}",
            'role' => 'user'
        ];
    }

    echo "🚀 Bắt đầu tạo " . count($customers) . " tài khoản khách hàng...\n\n";

    foreach ($customers as $index => $customer) {
        try {
            // Tạo user
            $userRecord = $auth->createUser([
                'email' => $customer['email'],
                'password' => $customer['password'],
                'displayName' => $customer['displayName'],
            ]);

            // Set custom claims (role)
            $auth->setCustomUserClaims($userRecord->uid, [
                'role' => $customer['role']
            ]);

            echo "✅ Tạo thành công: {$customer['displayName']} ({$customer['email']})\n";
            echo "   UID: {$userRecord->uid}\n";
            echo "   Role: {$customer['role']}\n\n";
        } catch (EmailExists $e) {
            echo "⚠️  Email {$customer['email']} đã tồn tại, bỏ qua...\n\n";
        } catch (Exception $e) {
            echo "❌ Lỗi tạo {$customer['email']}: {$e->getMessage()}\n\n";
        }
    }

    echo "🎉 Hoàn thành tạo tài khoản khách hàng!\n";
    echo "📝 Thông tin đăng nhập:\n";
    echo "   Email: customer1@demo.com đến customer10@demo.com\n";
    echo "   Password: customer123\n";
    echo "   Role: user\n";
} catch (Exception $e) {
    echo "💥 Lỗi: " . $e->getMessage() . "\n";
}
