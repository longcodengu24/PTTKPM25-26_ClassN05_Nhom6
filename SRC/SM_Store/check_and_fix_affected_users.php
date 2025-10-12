<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use App\Services\FirestoreSimple;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    $firestore = new FirestoreSimple();

    echo "🔍 KIỂM TRA TÁC ĐỘNG CỦA BUG BACKFILL COMMAND\n";
    echo str_repeat("=", 70) . "\n\n";

    $users = $auth->listUsers();
    $affectedUsers = [];
    $totalUsers = 0;

    foreach ($users as $user) {
        $totalUsers++;
        $uid = $user->uid;

        // Kiểm tra Firebase Auth data
        $authData = [
            'email' => $user->email ?? '',
            'displayName' => $user->displayName ?? '',
            'photoUrl' => $user->photoUrl ?? '',
            'customClaims' => $user->customClaims ?? []
        ];

        // Kiểm tra Firestore data
        try {
            $firestoreData = $firestore->getDocument('users', $uid);

            // So sánh dữ liệu
            $issues = [];

            if (($authData['displayName'] ?? '') !== ($firestoreData['name'] ?? '')) {
                $issues[] = "Display Name không khớp";
            }

            if (($authData['email'] ?? '') !== ($firestoreData['email'] ?? '')) {
                $issues[] = "Email không khớp";
            }

            if (($authData['photoUrl'] ?? '') !== ($firestoreData['avatar'] ?? '')) {
                $issues[] = "Avatar không khớp";
            }

            // Kiểm tra xem có chỉ còn lại coins không
            $firestoreKeys = array_keys($firestoreData);
            if (count($firestoreKeys) == 1 && in_array('coins', $firestoreKeys)) {
                $issues[] = "CHỈ CÒN LẠI COINS - Dữ liệu khác bị mất!";
            }

            if (!empty($issues)) {
                $affectedUsers[] = [
                    'uid' => $uid,
                    'email' => $user->email ?? 'N/A',
                    'auth_data' => $authData,
                    'firestore_data' => $firestoreData,
                    'issues' => $issues
                ];
            }
        } catch (Exception $e) {
            // User không có document trong Firestore
            $affectedUsers[] = [
                'uid' => $uid,
                'email' => $user->email ?? 'N/A',
                'auth_data' => $authData,
                'firestore_data' => null,
                'issues' => ['Không có document trong Firestore']
            ];
        }
    }

    echo "📊 KẾT QUẢ KIỂM TRA:\n";
    echo "   Tổng số users: {$totalUsers}\n";
    echo "   Users bị ảnh hưởng: " . count($affectedUsers) . "\n\n";

    if (empty($affectedUsers)) {
        echo "✅ KHÔNG CÓ USER NÀO BỊ ẢNH HƯỞNG!\n";
        exit;
    }

    echo "🚨 CHI TIẾT USERS BỊ ẢNH HƯỞNG:\n";
    echo str_repeat("-", 70) . "\n";

    foreach ($affectedUsers as $index => $affected) {
        echo "❌ USER #{" . ($index + 1) . "}: {$affected['email']}\n";
        echo "   🆔 UID: {$affected['uid']}\n";
        echo "   🚨 Vấn đề: " . implode(', ', $affected['issues']) . "\n";

        echo "   📱 Firebase Auth:\n";
        echo "      Email: " . ($affected['auth_data']['email'] ?: 'N/A') . "\n";
        echo "      Name: " . ($affected['auth_data']['displayName'] ?: 'N/A') . "\n";
        echo "      Role: " . ($affected['auth_data']['customClaims']['role'] ?? 'N/A') . "\n";

        echo "   🔥 Firestore:\n";
        if ($affected['firestore_data']) {
            foreach ($affected['firestore_data'] as $key => $value) {
                echo "      {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
        } else {
            echo "      (Không có data)\n";
        }
        echo "\n";
    }

    // Đề xuất khắc phục
    echo "🔧 ĐỀ XUẤT KHẮC PHỤC:\n";
    echo str_repeat("-", 70) . "\n";
    echo "1. 🚫 KHÔNG chạy command 'php artisan app:backfill-users-to-firestore'\n";
    echo "2. 🛠️  Sửa bug trong BackfillUsersToFirestore.php (đã sửa)\n";
    echo "3. 🔄 Chạy script khôi phục dữ liệu cho users bị ảnh hưởng\n";
    echo "4. ✅ Kiểm tra lại sau khi khôi phục\n\n";

    $choice = readline("Bạn có muốn tự động khôi phục dữ liệu cho các users bị ảnh hưởng? (y/n): ");

    if (strtolower($choice) === 'y') {
        echo "\n🛠️  BẮT ĐẦU KHÔI PHỤC...\n";

        foreach ($affectedUsers as $affected) {
            $uid = $affected['uid'];
            $authData = $affected['auth_data'];

            echo "🔄 Khôi phục user: {$affected['email']}\n";

            // Giữ nguyên coins nếu có
            $existingCoins = 0;
            if ($affected['firestore_data'] && isset($affected['firestore_data']['coins'])) {
                $existingCoins = $affected['firestore_data']['coins'];
            }

            // Khôi phục đầy đủ thông tin
            $restoreData = [
                'name' => $authData['displayName'] ?? '',
                'email' => $authData['email'] ?? '',
                'avatar' => $authData['photoUrl'] ?? '',
                'coins' => $existingCoins,
                'role' => $authData['customClaims']['role'] ?? 'user',
                'restored_at' => date('Y-m-d H:i:s'),
                'restored_by' => 'auto_fix_script'
            ];

            try {
                $firestore->updateDocument('users', $uid, $restoreData);
                echo "   ✅ Khôi phục thành công (coins: {$existingCoins})\n";
            } catch (Exception $e) {
                echo "   ❌ Lỗi khôi phục: " . $e->getMessage() . "\n";
            }
        }

        echo "\n🎉 HOÀN THÀNH KHÔI PHỤC!\n";
    }
} catch (Exception $e) {
    echo "💥 Lỗi: " . $e->getMessage() . "\n";
}
