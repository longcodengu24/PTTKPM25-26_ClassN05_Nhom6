<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    $uid = 'ysZ6O1vg4DfqrULFY2awZJYgjxx1';

    echo "🔍 KIỂM TRA CHI TIẾT TÀI KHOẢN: {$uid}\n";
    echo str_repeat("=", 70) . "\n";

    $user = $auth->getUser($uid);

    echo "👤 THÔNG TIN FIREBASE AUTH:\n";
    echo "   📧 Email: " . ($user->email ?? 'N/A') . "\n";
    echo "   🏷️  Display Name: " . ($user->displayName ?? 'N/A') . "\n";
    echo "   🆔 UID: " . $user->uid . "\n";
    echo "   📅 Tạo lúc: " . $user->metadata->createdAt->format('Y-m-d H:i:s') . "\n";
    $lastSignIn = $user->metadata->lastSignInAt ?? null;
    echo "   📅 Cập nhật lần cuối: " . ($lastSignIn ? $lastSignIn->format('Y-m-d H:i:s') : 'Chưa đăng nhập') . "\n";
    echo "   🖼️  Avatar: " . ($user->photoUrl ?? 'N/A') . "\n";
    echo "   ✅ Email verified: " . ($user->emailVerified ? 'Yes' : 'No') . "\n";

    $customClaims = $user->customClaims ?? [];
    echo "   🏆 Custom Claims: " . (empty($customClaims) ? 'N/A' : json_encode($customClaims, JSON_PRETTY_PRINT)) . "\n";

    echo "\n";
} catch (Exception $e) {
    echo "💥 Lỗi kiểm tra Firebase Auth: " . $e->getMessage() . "\n";
}

// Kiểm tra Firestore
try {
    $firestore = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createFirestore();

    echo "🔍 KIỂM TRA FIRESTORE:\n";
    echo str_repeat("-", 40) . "\n";

    // Kiểm tra collection users
    $userDoc = $firestore->database()->collection('users')->document($uid);
    $snapshot = $userDoc->snapshot();

    if ($snapshot->exists()) {
        echo "📄 Document 'users/{$uid}' TỒN TẠI:\n";
        $data = $snapshot->data();
        foreach ($data as $key => $value) {
            echo "   {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
        }
    } else {
        echo "❌ Document 'users/{$uid}' KHÔNG TỒN TẠI\n";
    }

    echo "\n";

    // Kiểm tra các collection khác có thể chứa thông tin user
    $collections = ['profiles', 'accounts', 'user_data', 'customers'];

    foreach ($collections as $collection) {
        $doc = $firestore->database()->collection($collection)->document($uid);
        $snapshot = $doc->snapshot();

        if ($snapshot->exists()) {
            echo "📄 Tìm thấy trong '{$collection}/{$uid}':\n";
            $data = $snapshot->data();
            foreach ($data as $key => $value) {
                echo "   {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
            echo "\n";
        }
    }
} catch (Exception $e) {
    echo "💥 Lỗi kiểm tra Firestore: " . $e->getMessage() . "\n";
}
