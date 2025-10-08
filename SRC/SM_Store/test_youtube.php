<?php
// Test YouTube URL normalization

// Function để test
function normalizeYouTubeUrl($url)
{
    if (empty($url)) {
        return null;
    }

    // Các pattern YouTube
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
        '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube.com/embed/{$videoId}";
        }
    }

    return $url; // Trả về URL gốc nếu không match
}

// Test cases
$testUrls = [
    'https://www.youtube.com/watch?v=fxzLoEQsTUI',
    'https://youtu.be/fxzLoEQsTUI?si=q6wIBXvgBw34Fzwo',
    'https://www.youtube.com/embed/fxzLoEQsTUI',
    'https://m.youtube.com/watch?v=fxzLoEQsTUI',
    'invalid-url',
    ''
];

echo "<h2>🔧 Test YouTube URL Normalization</h2>";
echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f0f0f0;'><th>Original URL</th><th>Normalized URL</th><th>Status</th></tr>";

foreach ($testUrls as $url) {
    $normalized = normalizeYouTubeUrl($url);
    $status = $normalized && strpos($normalized, 'embed') !== false ? '✅ Success' : '❌ No change';

    echo "<tr>";
    echo "<td>" . htmlspecialchars($url ?: '(empty)') . "</td>";
    echo "<td>" . htmlspecialchars($normalized ?: '(null)') . "</td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>🎬 How to display in HTML:</h3>";
$embedUrl = normalizeYouTubeUrl('https://youtu.be/fxzLoEQsTUI?si=q6wIBXvgBw34Fzwo');
echo "<p><strong>Embed URL:</strong> " . htmlspecialchars($embedUrl) . "</p>";
echo "<h4>HTML Code:</h4>";
echo "<pre>";
echo htmlspecialchars("<iframe width='560' height='315' 
       src='$embedUrl' 
       frameborder='0' 
       allowfullscreen>
</iframe>");
echo "</pre>";

echo "<h4>Live Preview:</h4>";
echo "<iframe width='560' height='315' 
       src='$embedUrl' 
       frameborder='0' 
       allowfullscreen>
</iframe>";
