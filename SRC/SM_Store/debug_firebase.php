<?php
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;

try {
    // Khởi tạo Firebase
    $factory = (new Factory)
        ->withServiceAccount(__DIR__ . '/resources/key/firebasekey.json');

    $auth = $factory->createAuth();

    echo "<h2>Debug Firebase User Data</h2>";

    // Nếu có UID từ session
    if (isset($_GET['uid'])) {
        $uid = $_GET['uid'];
        echo "<h3>Checking UID: " . $uid . "</h3>";

        try {
            $user = $auth->getUser($uid);

            echo "<h4>User Info:</h4>";
            echo "<pre>";
            echo "UID: " . $user->uid . "\n";
            echo "Email: " . $user->email . "\n";
            echo "Display Name: " . ($user->displayName ?? 'NULL') . "\n";
            echo "Photo URL: " . ($user->photoUrl ?? 'NULL') . "\n";
            echo "Email Verified: " . ($user->emailVerified ? 'Yes' : 'No') . "\n";
            echo "Created: " . $user->metadata->createdAt->format('Y-m-d H:i:s') . "\n";
            echo "Last Login: " . ($user->metadata->lastLoginAt ? $user->metadata->lastLoginAt->format('Y-m-d H:i:s') : 'NULL') . "\n";
            echo "</pre>";

            // Kiểm tra custom claims
            $customClaims = $user->customClaims;
            echo "<h4>Custom Claims:</h4>";
            echo "<pre>";
            var_dump($customClaims);
            echo "</pre>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }

    // List tất cả users
    echo "<h3>All Users:</h3>";
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th>UID</th><th>Email</th><th>Name</th><th>Photo URL</th><th>Role</th><th>Created</th>";
    echo "</tr>";

    foreach ($auth->listUsers() as $user) {
        $customClaims = $user->customClaims;
        $role = isset($customClaims['role']) ? $customClaims['role'] : 'no role';

        echo "<tr>";
        echo "<td><a href='?uid=" . $user->uid . "'>" . substr($user->uid, 0, 20) . "...</a></td>";
        echo "<td>" . $user->email . "</td>";
        echo "<td>" . ($user->displayName ?? 'NULL') . "</td>";
        echo "<td>" . ($user->photoUrl ? '<a href="' . $user->photoUrl . '" target="_blank">View</a>' : 'NULL') . "</td>";
        echo "<td>" . $role . "</td>";
        echo "<td>" . $user->metadata->createdAt->format('Y-m-d H:i:s') . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Firebase Error: " . $e->getMessage() . "</p>";
}
