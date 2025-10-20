<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel Environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Kreait\Firebase\Factory;

try {
    // Khởi tạo Firebase Auth
    $auth = (new Factory)
        ->withServiceAccount(__DIR__ . '/resources/key/firebasekey.json')
        ->createAuth();

    // Email của admin cần update
    $adminEmail = 'admin@smstore.com';

    echo "🔄 Đang cập nhật custom claims cho admin...\n\n";

    try {
        // Lấy thông tin user theo email
        $user = $auth->getUserByEmail($adminEmail);
        $uid = $user->uid;

        echo "📋 Tìm thấy tài khoản:\n";
        echo "   UID: {$uid}\n";
        echo "   Email: {$user->email}\n";
        echo "   Display Name: {$user->displayName}\n\n";

        // Set custom claims
        $auth->setCustomUserClaims($uid, ['role' => 'admin']);

        echo "✅ Đã cập nhật custom claims thành công!\n";
        echo "   Role: admin\n\n";

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🎉 HOÀN TẤT! Vui lòng đăng xuất và đăng nhập lại\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "⚠️  LƯU Ý: Custom claims chỉ có hiệu lực sau khi đăng nhập lại!\n\n";
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        echo "❌ Không tìm thấy tài khoản với email: {$adminEmail}\n";
        echo "   Hãy chạy create_admin.php để tạo tài khoản mới.\n\n";
    }
} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
