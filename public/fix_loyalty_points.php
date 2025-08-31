<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصلاح جدول نقاط الولاء</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffe8e8; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e8f0ff; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3e0; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #005a8b; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 إصلاح جدول نقاط الولاء</h1>
        
        <?php
        if (isset($_POST['fix'])) {
            echo '<div class="info">🔄 جاري إصلاح المشكلة...</div>';
            
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
                
                // الاتصال بقاعدة البيانات
                $pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                
                // التحقق من وجود الجدول
                $stmt = $pdo->query("SHOW TABLES LIKE 'loyalty_points'");
                $exists = $stmt->fetch();
                
                if ($exists) {
                    echo '<div class="warning">⚠️ جدول loyalty_points موجود بالفعل</div>';
                } else {
                    // إنشاء الجدول
                    $sql = "
                    CREATE TABLE loyalty_points (
                      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      user_id bigint(20) unsigned NOT NULL,
                      points int(11) NOT NULL,
                      type varchar(255) NOT NULL DEFAULT 'earned',
                      description text,
                      order_id bigint(20) unsigned DEFAULT NULL,
                      expires_at datetime DEFAULT NULL,
                      metadata json DEFAULT NULL,
                      reference_type varchar(255) DEFAULT NULL,
                      reference_id bigint(20) unsigned DEFAULT NULL,
                      is_processed tinyint(1) NOT NULL DEFAULT '1',
                      created_at timestamp NULL DEFAULT NULL,
                      updated_at timestamp NULL DEFAULT NULL,
                      PRIMARY KEY (id),
                      KEY loyalty_points_user_id_foreign (user_id),
                      KEY loyalty_points_order_id_foreign (order_id),
                      KEY loyalty_points_user_id_type_index (user_id,type),
                      KEY loyalty_points_user_id_expires_at_index (user_id,expires_at),
                      KEY loyalty_points_reference_type_reference_id_index (reference_type,reference_id),
                      KEY loyalty_points_created_at_index (created_at),
                      CONSTRAINT loyalty_points_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                      CONSTRAINT loyalty_points_order_id_foreign FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE SET NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    
                    $pdo->exec($sql);
                    echo '<div class="success">✅ تم إنشاء جدول loyalty_points بنجاح!</div>';
                    
                    // إضافة إلى جدول migrations
                    $pdo->exec("INSERT IGNORE INTO migrations (migration, batch) VALUES ('2025_08_31_173000_create_loyalty_points_table', 1)");
                    echo '<div class="success">✅ تم تحديث جدول migrations</div>';
                }
                
                // عرض هيكل الجدول
                echo '<div class="info">📋 هيكل جدول loyalty_points:</div>';
                $stmt = $pdo->query("DESCRIBE loyalty_points");
                $columns = $stmt->fetchAll();
                
                echo '<pre>';
                foreach ($columns as $column) {
                    echo "{$column['Field']}: {$column['Type']}";
                    if ($column['Null'] === 'NO') echo ' (NOT NULL)';
                    if ($column['Default'] !== null) echo " DEFAULT '{$column['Default']}'";
                    echo "\n";
                }
                echo '</pre>';
                
                // عدد السجلات
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM loyalty_points");
                $count = $stmt->fetch()['count'];
                echo "<div class='info'>📊 عدد السجلات في الجدول: $count</div>";
                
                echo '<div class="success">🎉 تم حل المشكلة! الآن يمكن تسجيل الدخول بنجاح.</div>';
                
            } catch (Exception $e) {
                echo '<div class="error">❌ خطأ: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            ?>
            <div class="info">
                <h3>🔍 تشخيص المشكلة</h3>
                <p>المشكلة المكتشفة:</p>
                <pre>SQLSTATE[42S02]: Base table or view not found: 1146 Table 'suntop_db.loyalty_points' doesn't exist</pre>
                
                <h3>💡 الحل</h3>
                <p>سيتم إنشاء جدول <code>loyalty_points</code> مع جميع الحقول المطلوبة:</p>
                <ul>
                    <li>id - المفتاح الأساسي</li>
                    <li>user_id - مرجع المستخدم</li>
                    <li>points - عدد النقاط (موجب للمكسب، سالب للاستهلاك)</li>
                    <li>type - نوع المعاملة</li>
                    <li>description - وصف المعاملة</li>
                    <li>order_id - مرجع الطلبية (اختياري)</li>
                    <li>expires_at - تاريخ انتهاء النقاط</li>
                    <li>metadata - بيانات إضافية</li>
                    <li>reference_type & reference_id - للعلاقات المتعددة الأشكال</li>
                    <li>is_processed - حالة المعالجة</li>
                    <li>created_at & updated_at - طوابع زمنية</li>
                </ul>
            </div>
            
            <form method="post">
                <button type="submit" name="fix" value="1">🔧 إصلاح المشكلة الآن</button>
            </form>
            <?php
        }
        ?>
        
        <hr>
        <p><small>تم إنشاء هذا المصلح تلقائياً لحل مشكلة جدول نقاط الولاء المفقود</small></p>
    </div>
</body>
</html>
