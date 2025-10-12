<?php

require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

try {
    $auth = (new Factory)
        ->withServiceAccount('resources/key/firebasekey.json')
        ->createAuth();

    echo "🔍 PHÂN TÍCH TÀI KHOẢN BỊ MẤT THÔNG TIN\n";
    echo str_repeat("=", 60) . "\n\n";

    $users = $auth->listUsers();
    $problemUsers = [];

    // Tìm các tài khoản có vấn đề
    foreach ($users as $user) {
        $hasProblems = false;
        $problems = [];

        // Kiểm tra các vấn đề
        if (empty($user->displayName)) {
            $hasProblems = true;
            $problems[] = "Thiếu Display Name";
        }

        if (empty($user->customClaims) || !isset($user->customClaims['role'])) {
            $hasProblems = true;
            $problems[] = "Thiếu Role";
        }

        if (empty($user->email)) {
            $hasProblems = true;
            $problems[] = "Thiếu Email";
        }

        if ($hasProblems) {
            $problemUsers[] = [
                'user' => $user,
                'problems' => $problems
            ];
        }
    }

    $totalUsersCount = 0;
    // Đếm users trong vòng lặp trước
    foreach ($auth->listUsers() as $u) {
        $totalUsersCount++;
    }

    echo "📊 KẾT QUẢ PHÂN TÍCH:\n";
    echo "   Tổng số users: " . $totalUsersCount . "\n";
    echo "   Users có vấn đề: " . count($problemUsers) . "\n\n";

    if (empty($problemUsers)) {
        echo "✅ Không có tài khoản nào bị thiếu thông tin quan trọng!\n";
        exit;
    }

    echo "🚨 CHI TIẾT CÁC TÀI KHOẢN CÓ VẤN ĐỀ:\n";
    echo str_repeat("-", 60) . "\n";

    foreach ($problemUsers as $index => $item) {
        $user = $item['user'];
        $problems = $item['problems'];

        echo "❌ TÀI KHOẢN #{" . ($index + 1) . "}\n";
        echo "   📧 Email: " . ($user->email ?? 'N/A') . "\n";
        echo "   🏷️  Display Name: " . ($user->displayName ?? 'N/A') . "\n";
        echo "   🆔 UID: " . $user->uid . "\n";
        echo "   👨‍💼 Role: " . ($user->customClaims['role'] ?? 'N/A') . "\n";
        echo "   📅 Tạo lúc: " . $user->metadata->createdAt->format('Y-m-d H:i:s') . "\n";
        echo "   🚨 Vấn đề: " . implode(', ', $problems) . "\n";

        // Đánh giá tác động
        echo "   💥 TÁC ĐỘNG:\n";
        if (in_array("Thiếu Display Name", $problems)) {
            echo "      - Giao diện sẽ hiển thị 'N/A' thay vì tên người dùng\n";
            echo "      - Trải nghiệm người dùng kém\n";
        }
        if (in_array("Thiếu Role", $problems)) {
            echo "      - Không thể phân quyền đúng chức năng\n";
            echo "      - Có thể gây lỗi khi truy cập các trang yêu cầu role\n";
            echo "      - Middleware role sẽ không hoạt động đúng\n";
        }
        if (in_array("Thiếu Email", $problems)) {
            echo "      - Không thể đăng nhập\n";
            echo "      - Tài khoản thực tế không sử dụng được\n";
        }
        echo "\n";
    }

    echo "🔧 KHUYẾN NGHỊ SỬA CHỮA:\n";
    echo str_repeat("-", 60) . "\n";

    $choice = readline("Bạn có muốn tự động sửa chữa các tài khoản này không? (y/n): ");

    if (strtolower($choice) === 'y') {
        echo "\n🛠️  BẮT ĐẦU SỬA CHỮA...\n";

        foreach ($problemUsers as $index => $item) {
            $user = $item['user'];
            $problems = $item['problems'];
            $uid = $user->uid;

            echo "\n🔄 Đang sửa tài khoản: {$user->email}\n";

            $updateData = [];

            // Sửa Display Name nếu thiếu
            if (in_array("Thiếu Display Name", $problems)) {
                $defaultName = "User " . substr($uid, 0, 8);
                $updateData['displayName'] = $defaultName;
                echo "   ✅ Thêm Display Name: {$defaultName}\n";
            }

            // Cập nhật user record nếu có thay đổi
            if (!empty($updateData)) {
                try {
                    $auth->updateUser($uid, $updateData);
                    echo "   ✅ Cập nhật user record thành công\n";
                } catch (Exception $e) {
                    echo "   ❌ Lỗi cập nhật user record: " . $e->getMessage() . "\n";
                }
            }

            // Sửa Role nếu thiếu
            if (in_array("Thiếu Role", $problems)) {
                try {
                    // Mặc định gán role 'user' cho tài khoản thiếu role
                    $auth->setCustomUserClaims($uid, ['role' => 'user']);
                    echo "   ✅ Thêm Role: user\n";
                } catch (Exception $e) {
                    echo "   ❌ Lỗi thêm role: " . $e->getMessage() . "\n";
                }
            }
        }

        echo "\n🎉 HOÀN THÀNH SỬA CHỮA!\n";
        echo "📝 Tóm tắt:\n";
        echo "   - Đã sửa " . count($problemUsers) . " tài khoản\n";
        echo "   - Gán role mặc định: 'user'\n";
        echo "   - Thêm Display Name tự động\n";
    } else {
        echo "\n📋 HƯỚNG DẪN SỬA CHỮA THỦ CÔNG:\n";
        foreach ($problemUsers as $index => $item) {
            $user = $item['user'];
            echo "\n" . ($index + 1) . ". Tài khoản: {$user->email} (UID: {$user->uid})\n";
            if (in_array("Thiếu Display Name", $item['problems'])) {
                echo "   - Thêm Display Name trong Firebase Console\n";
            }
            if (in_array("Thiếu Role", $item['problems'])) {
                echo "   - Thêm custom claim 'role' với giá trị: 'user', 'saler', hoặc 'admin'\n";
            }
        }
    }
} catch (Exception $e) {
    echo "💥 Lỗi: " . $e->getMessage() . "\n";
}
