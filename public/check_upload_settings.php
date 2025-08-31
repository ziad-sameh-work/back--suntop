<?php
echo "<h2>PHP Upload Settings Check</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$settings = [
    'file_uploads' => ini_get('file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'max_file_uploads' => ini_get('max_file_uploads'),
];

foreach ($settings as $setting => $value) {
    $status = 'OK';
    $color = 'green';
    
    if ($setting == 'file_uploads' && !$value) {
        $status = 'DISABLED';
        $color = 'red';
    }
    
    echo "<tr>";
    echo "<td>$setting</td>";
    echo "<td>$value</td>";
    echo "<td style='color: $color'>$status</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Upload Directory Permissions</h3>";
$uploadDir = __DIR__ . '/uploads';
$productsDir = $uploadDir . '/products';

echo "<p>Upload directory: " . $uploadDir . "</p>";
echo "<p>Exists: " . (is_dir($uploadDir) ? 'YES' : 'NO') . "</p>";
echo "<p>Writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . "</p>";

echo "<p>Products directory: " . $productsDir . "</p>";
echo "<p>Exists: " . (is_dir($productsDir) ? 'YES' : 'NO') . "</p>";
echo "<p>Writable: " . (is_writable($productsDir) ? 'YES' : 'NO') . "</p>";

// Test creating a file
$testFile = $productsDir . '/test_write.txt';
if (file_put_contents($testFile, 'test')) {
    echo "<p style='color: green'>Write test: SUCCESS</p>";
    unlink($testFile);
} else {
    echo "<p style='color: red'>Write test: FAILED</p>";
}
?>
