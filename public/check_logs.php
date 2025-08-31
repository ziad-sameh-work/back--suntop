<?php
echo "<h2>📋 Latest Laravel Logs</h2>";

$logFile = '../storage/logs/laravel.log';

if (file_exists($logFile)) {
    echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h3>Last 50 lines from laravel.log:</h3>";
    echo "<pre style='background: #1e1e1e; color: #fff; padding: 15px; overflow-x: auto; font-size: 12px; max-height: 400px; overflow-y: auto;'>";
    
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    
    foreach ($lastLines as $line) {
        // Highlight our debug messages
        if (strpos($line, '=== Product first_image DEBUG') !== false) {
            echo "<span style='color: #00ff00; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
        } elseif (strpos($line, '=== IMAGE UPLOAD DEBUG') !== false) {
            echo "<span style='color: #ffff00; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
        } elseif (strpos($line, '=== PRODUCT CREATION DEBUG') !== false) {
            echo "<span style='color: #ff9500; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
        } elseif (strpos($line, 'ERROR') !== false || strpos($line, 'error') !== false) {
            echo "<span style='color: #ff0000; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
        } elseif (strpos($line, 'WARNING') !== false || strpos($line, 'warning') !== false) {
            echo "<span style='color: #ff6600;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    
    echo "</pre>";
    echo "</div>";
    
    echo "<p><strong>Log file size:</strong> " . filesize($logFile) . " bytes</p>";
    echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', filemtime($logFile)) . "</p>";
    
} else {
    echo "<p style='color: red;'>❌ Log file not found: $logFile</p>";
}

echo "<hr>";
echo "<h3>🔄 Refresh Actions:</h3>";
echo "<ul>";
echo "<li><a href='debug_images.php' target='_blank'>🔍 Run Image Debug</a> - This will trigger the logs</li>";
echo "<li><a href='check_logs.php'>📋 Refresh Logs</a> - Reload this page</li>";
echo "<li><a href='/admin/products'>🛍️ Go to Products Page</a> - This will also trigger first_image</li>";
echo "</ul>";
?>
