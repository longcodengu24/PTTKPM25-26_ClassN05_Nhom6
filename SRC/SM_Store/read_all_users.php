<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    echo "📋 DANH SÁCH TẤT CẢ USERS TRONG FIREBASE\n";
    echo str_repeat("=", 80) . "\n\n";

    $users = $auth->listUsers();
    $totalUsers = 0;

    // Thống kê theo role
    $roleStats = [
        'admin' => 0,
        'saler' => 0,
        'user' => 0,
        'no_role' => 0
    ];

    echo "🔍 CHI TIẾT TỪNG USER:\n";
    echo str_repeat("-", 80) . "\n";

    foreach ($users as $user) {
        $totalUsers++;

        // Lấy custom claims (role)
        $customClaims = $user->customClaims ?? [];
        $role = $customClaims['role'] ?? 'no_role';

        // Thống kê role
        if (isset($roleStats[$role])) {
            $roleStats[$role]++;
        } else {
            $roleStats['no_role']++;
        }

        echo "👤 USER #{$totalUsers}\n";
        echo "   📧 Email: " . ($user->email ?? 'N/A') . "\n";
        echo "   🏷️  Display Name: " . ($user->displayName ?? 'N/A') . "\n";
        echo "   🆔 UID: " . $user->uid . "\n";
        echo "   👨‍💼 Role: " . $role . "\n";
        echo "   📅 Tạo lúc: " . $user->metadata->createdAt->format('Y-m-d H:i:s') . "\n";
        echo "   📱 Số điện thoại: " . ($user->phoneNumber ?? 'N/A') . "\n";
        echo "   🖼️  Avatar: " . ($user->photoUrl ?? 'N/A') . "\n";
        echo "   ✅ Email verified: " . ($user->emailVerified ? 'Yes' : 'No') . "\n";
        echo "   🚫 Disabled: " . ($user->disabled ? 'Yes' : 'No') . "\n";

        // Hiển thị custom claims nếu có
        if (!empty($customClaims)) {
            echo "   🏆 Custom Claims: " . json_encode($customClaims, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }

        echo str_repeat("-", 80) . "\n";
    }

    echo "\n📊 THỐNG KÊ TỔNG QUAN:\n";
    echo str_repeat("=", 40) . "\n";
    echo "🔢 Tổng số users: {$totalUsers}\n";
    echo "👨‍💼 Admin: {$roleStats['admin']} users\n";
    echo "🛒 Seller: {$roleStats['saler']} users\n";
    echo "👤 Customer: {$roleStats['user']} users\n";
    echo "❓ Không có role: {$roleStats['no_role']} users\n";

    echo "\n🔑 THÔNG TIN ĐĂNG NHẬP MẪU:\n";
    echo str_repeat("=", 40) . "\n";
    echo "Admin:\n";
    echo "  - admin@skvmusic.com / admin123456\n";
    echo "  - admin@demo.com / admin123\n\n";
    echo "Seller:\n";
    echo "  - seller1@demo.com / seller123\n";
    echo "  - seller2@demo.com / seller123\n\n";
    echo "Customer:\n";
    echo "  - customer1@demo.com / customer123\n";
    echo "  - customer2@demo.com / customer123\n";
} catch (Exception $e) {
    echo "💥 Lỗi khi đọc users: " . $e->getMessage() . "\n";
    echo "🔧 Kiểm tra:\n";
    echo "   1. File firebasekey.json có tồn tại không?\n";
    echo "   2. Kết nối internet có ổn định không?\n";
    echo "   3. Firebase project có được cấu hình đúng không?\n";
}
