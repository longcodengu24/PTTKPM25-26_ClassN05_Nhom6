<?php
// Test upload functionality
echo "<h2>Test Upload Configuration</h2>";

// Kiểm tra cài đặt PHP
echo "<h3>PHP Configuration:</h3>";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default') . "<br>";

// Kiểm tra thư mục
echo "<h3>Directory Check:</h3>";
$uploadDir = __DIR__ . '/public/img/avatars';
echo "Upload directory: " . $uploadDir . "<br>";
echo "Directory exists: " . (file_exists($uploadDir) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "<br>";

// Test form
if ($_POST) {
    echo "<h3>Upload Test Result:</h3>";
    echo "<pre>";
    var_dump($_FILES);
    echo "</pre>";

    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['test_file'];
        $targetPath = $uploadDir . '/' . 'test_' . time() . '_' . $file['name'];

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo "<p style='color: green;'>✅ Upload successful: " . $targetPath . "</p>";
            echo "<p>File size: " . filesize($targetPath) . " bytes</p>";
        } else {
            echo "<p style='color: red;'>❌ Upload failed</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No file uploaded or error occurred</p>";
        if (isset($_FILES['test_file'])) {
            echo "Error code: " . $_FILES['test_file']['error'] . "<br>";
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <h3>Test Upload:</h3>
    <input type="file" name="test_file" accept="image/*" required><br><br>
    <input type="submit" value="Test Upload">
</form>