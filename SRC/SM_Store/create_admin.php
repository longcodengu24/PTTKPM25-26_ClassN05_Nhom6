<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel Environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;
use App\Services\FirestoreSimple;

try {
    // Khởi tạo Firebase Auth
    $auth = (new Factory)
        ->withServiceAccount(__DIR__ . '/resources/key/firebase.json')
        ->createAuth();

    // Khởi tạo Firestore Simple Service
    $firestoreService = new FirestoreSimple();

    // Thông tin admin
    $adminEmail = 'admin@smstore.com';
    $adminPassword = 'admin123456';
    $adminName = 'System Administrator';

    echo "🚀 Đang tạo tài khoản admin...\n\n";

    try {
        // Tạo user trong Firebase Authentication
        $userProperties = [
            'email' => $adminEmail,
            'emailVerified' => true,
            'password' => $adminPassword,
            'displayName' => $adminName,
            'disabled' => false,
        ];

        $createdUser = $auth->createUser($userProperties);
        $uid = $createdUser->uid;

        echo "✅ Tạo tài khoản Firebase Auth thành công!\n";
        echo "   UID: {$uid}\n";
        echo "   Email: {$adminEmail}\n";
        echo "   Password: {$adminPassword}\n\n";

        // Set custom claims để đánh dấu role admin
        $auth->setCustomUserClaims($uid, ['role' => 'admin']);
        echo "✅ Đã set custom claims: role = admin\n\n";

        // Thêm thông tin admin vào Firestore collection 'users'
        $userData = [
            'email' => $adminEmail,
            'displayName' => $adminName,
            'role' => 'admin',
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];

        $firestoreService->createDocumentWithId('users', $uid, $userData);

        echo "✅ Đã lưu thông tin admin vào Firestore!\n\n";

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🎉 HOÀN TẤT! Tài khoản admin đã được tạo\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "📋 Thông tin đăng nhập:\n";
        echo "   Email: {$adminEmail}\n";
        echo "   Password: {$adminPassword}\n";
        echo "   Role: admin\n";
        echo "   UID: {$uid}\n\n";

        echo "🔗 Đường dẫn đăng nhập:\n";
        echo "   http://127.0.0.1:8000/login\n\n";

        echo "⚠️  LƯU Ý: Hãy đổi mật khẩu sau khi đăng nhập lần đầu!\n\n";
    } catch (EmailExists $e) {
        echo "⚠️  Email {$adminEmail} đã tồn tại!\n";
        echo "   Nếu bạn muốn tạo lại, hãy xóa tài khoản cũ trước.\n\n";

        // Lấy thông tin user hiện tại
        try {
            $user = $auth->getUserByEmail($adminEmail);
            echo "📋 Thông tin tài khoản hiện tại:\n";
            echo "   UID: {$user->uid}\n";
            echo "   Email: {$user->email}\n";
            echo "   Display Name: {$user->displayName}\n\n";
        } catch (\Exception $e) {
            // Ignore
        }
    }
} catch (\Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
