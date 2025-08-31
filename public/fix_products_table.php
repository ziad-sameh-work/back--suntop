<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح جدول المنتجات</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e8f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3e0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #005a8b; }
        .dangerous { background: #dc3545; }
        .dangerous:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح جدول المنتجات</h1>
        
        <?php
        if (isset($_POST['action'])) {
            echo '<div class="info">🔄 جاري تنفيذ العملية...</div>';
            
            try {
                // قراءة إعدادات قاعدة البيانات
                $envFile = dirname(__DIR__) . '/.env';
                $envVars = [];
                
                if (file_exists($envFile)) {
                    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                            list($key, $value) = explode('=', $line, 2);
                            $envVars[trim($key)] = trim($value);
                        }
                    }
                }
                
                $host = $envVars['DB_HOST'] ?? 'localhost';
                $dbname = $envVars['DB_DATABASE'] ?? 'suntop_db';
                $username = $envVars['DB_USERNAME'] ?? 'root';
                $password = $envVars['DB_PASSWORD'] ?? '';
                
                echo "<div class='info'>📡 الاتصال بقاعدة البيانات: $host/$dbname</div>";
                
                $pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                
                if ($_POST['action'] === 'check') {
                    // فحص هيكل الجدول الحالي
                    echo '<div class="info">📋 فحص هيكل جدول products الحالي:</div>';
                    $stmt = $pdo->query("DESCRIBE products");
                    $columns = $stmt->fetchAll();
                    
                    echo '<table>';
                    echo '<tr><th>اسم العمود</th><th>النوع</th><th>Null</th><th>القيمة الافتراضية</th></tr>';
                    foreach ($columns as $column) {
                        echo "<tr>";
                        echo "<td>{$column['Field']}</td>";
                        echo "<td>{$column['Type']}</td>";
                        echo "<td>{$column['Null']}</td>";
                        echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                    
                    // التحقق من الأعمدة المطلوبة
                    $requiredColumns = ['images', 'back_color', 'category_id'];
                    $existingColumns = array_column($columns, 'Field');
                    
                    echo '<div class="info">🔍 فحص الأعمدة المطلوبة:</div>';
                    echo '<ul>';
                    foreach ($requiredColumns as $col) {
                        $exists = in_array($col, $existingColumns);
                        echo '<li>' . ($exists ? '✅' : '❌') . " $col</li>";
                    }
                    echo '</ul>';
                    
                } elseif ($_POST['action'] === 'add_missing') {
                    // إضافة الأعمدة المفقودة
                    $alterStatements = [];
                    
                    // التحقق من وجود كل عمود قبل إضافته
                    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'category_id'");
                    if (!$stmt->fetch()) {
                        $alterStatements[] = "ADD COLUMN category_id BIGINT UNSIGNED NULL AFTER description";
                        echo '<div class="info">📝 سيتم إضافة عمود category_id</div>';
                    }
                    
                    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'back_color'");
                    if (!$stmt->fetch()) {
                        $alterStatements[] = "ADD COLUMN back_color VARCHAR(20) DEFAULT '#FF6B35' AFTER price";
                        echo '<div class="info">📝 سيتم إضافة عمود back_color</div>';
                    }
                    
                    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'images'");
                    if (!$stmt->fetch()) {
                        $alterStatements[] = "ADD COLUMN images JSON NULL AFTER back_color";
                        echo '<div class="info">📝 سيتم إضافة عمود images</div>';
                    }
                    
                    if (!empty($alterStatements)) {
                        $sql = "ALTER TABLE products " . implode(", ", $alterStatements);
                        $pdo->exec($sql);
                        echo '<div class="success">✅ تم إضافة الأعمدة المفقودة بنجاح!</div>';
                    } else {
                        echo '<div class="warning">⚠️ جميع الأعمدة المطلوبة موجودة بالفعل</div>';
                    }
                    
                } elseif ($_POST['action'] === 'migrate_images') {
                    // نقل البيانات من image_url إلى images إذا كان موجوداً
                    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'image_url'");
                    if ($stmt->fetch()) {
                        echo '<div class="info">🔄 نقل البيانات من image_url إلى images...</div>';
                        
                        $stmt = $pdo->query("SELECT id, image_url FROM products WHERE image_url IS NOT NULL AND image_url != ''");
                        $products = $stmt->fetchAll();
                        
                        $updateStmt = $pdo->prepare("UPDATE products SET images = ? WHERE id = ?");
                        $count = 0;
                        
                        foreach ($products as $product) {
                            $imageArray = json_encode([$product['image_url']]);
                            $updateStmt->execute([$imageArray, $product['id']]);
                            $count++;
                        }
                        
                        echo "<div class='success'>✅ تم نقل $count صورة من image_url إلى images</div>";
                    } else {
                        echo '<div class="warning">⚠️ عمود image_url غير موجود</div>';
                    }
                    
                } elseif ($_POST['action'] === 'update_migrations') {
                    // تحديث جدول migrations
                    $migrations = [
                        '2025_08_31_173000_create_loyalty_points_table',
                        '2025_08_31_999999_clean_products_table'
                    ];
                    
                    foreach ($migrations as $migration) {
                        $stmt = $pdo->prepare("INSERT IGNORE INTO migrations (migration, batch) VALUES (?, 1)");
                        $stmt->execute([$migration]);
                    }
                    
                    echo '<div class="success">✅ تم تحديث جدول migrations</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="error">❌ خطأ: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        } else {
            ?>
            <div class="info">
                <h3>🔍 مشاكل تم اكتشافها</h3>
                <ul>
                    <li><strong>مشكلة المفضلة:</strong> عمود image_url مفقود في جدول products</li>
                    <li><strong>السبب:</strong> migration clean_products_table حذف عمود image_url</li>
                    <li><strong>الحل:</strong> استخدام نظام images الجديد (JSON array)</li>
                </ul>
                
                <h3>💡 خطة الإصلاح</h3>
                <ol>
                    <li>فحص هيكل جدول products الحالي</li>
                    <li>إضافة الأعمدة المفقودة: category_id, back_color, images</li>
                    <li>نقل البيانات من image_url إلى images (إن وجدت)</li>
                    <li>تحديث جدول migrations</li>
                </ol>
            </div>
            
            <h3>🛠️ أدوات الإصلاح</h3>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="check">
                <button type="submit">🔍 فحص هيكل الجدول</button>
            </form>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="add_missing">
                <button type="submit">➕ إضافة الأعمدة المفقودة</button>
            </form>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="migrate_images">
                <button type="submit">🖼️ نقل الصور</button>
            </form>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="update_migrations">
                <button type="submit">📝 تحديث Migrations</button>
            </form>
            <?php
        }
        ?>
        
        <hr>
        <p><small>أداة إصلاح جدول المنتجات - SunTop Project</small></p>
    </div>
</body>
</html>
