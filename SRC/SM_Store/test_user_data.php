<?php
// Clear cache và test user data
session_start();

echo "<h2>Clear Cache & Test User Data</h2>";

if (isset($_GET['action'])) {
    if ($_GET['action'] === 'clear') {
        // Clear session
        session_destroy();
        echo "<p style='color: green;'>✅ Session cleared!</p>";
        echo "<a href='?'>Back to test</a>";
        exit;
    }
}

echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION ?? []);
echo "</pre>";

echo "<h3>Actions:</h3>";
echo "<a href='?action=clear' style='background: red; color: white; padding: 10px; text-decoration: none; margin: 5px;'>Clear Session</a><br><br>";

echo "<h3>Test Avatar URLs:</h3>";
$avatarFiles = glob(__DIR__ . '/public/img/avatars/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
foreach ($avatarFiles as $file) {
    $fileName = basename($file);
    $url = '/img/avatars/' . $fileName;
    echo "<p><a href='$url' target='_blank'>$fileName</a> - <img src='$url' style='width: 50px; height: 50px; border-radius: 50%;'></p>";
}

echo "<h3>Laravel Logs (last 20 lines):</h3>";
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -20);
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow: auto;'>";
    foreach ($lastLines as $line) {
        if (strpos($line, 'User data loaded') !== false || strpos($line, 'Avatar') !== false) {
            echo "<span style='background: yellow;'>$line</span>";
        } else {
            echo $line;
        }
    }
    echo "</pre>";
} else {
    echo "<p>No log file found</p>";
}
