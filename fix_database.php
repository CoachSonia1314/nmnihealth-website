<?php
// 資料庫連接設定
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    // 建立PDO連接
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 檢查並添加缺少的字段到users表
    $checkPasswordColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'password'");
    if ($checkPasswordColumn->rowCount() == 0) {
        $sql = "ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT '' AFTER email";
        $pdo->exec($sql);
        echo "已添加 password 字段到 users 表<br>";
    }
    
    $checkRoleColumn = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($checkRoleColumn->rowCount() == 0) {
        $sql = "ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER phone";
        $pdo->exec($sql);
        echo "已添加 role 字段到 users 表<br>";
    }
    
    // 創建測試用戶（如果不存在）
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    if ($stmt->rowCount() == 0) {
        $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['測試用戶', 'test@example.com', $hashedPassword, '0912345678', 'user']);
        echo "已創建測試用戶: test@example.com / 123456<br>";
    }
    
    // 創建管理員用戶（如果不存在）
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@nmnihealth.com']);
    if ($stmt->rowCount() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['管理員', 'admin@nmnihealth.com', $hashedPassword, '0987654321', 'admin']);
        echo "已創建管理員用戶: admin@nmnihealth.com / admin123<br>";
    }
    
    echo "<h3>數據庫修復完成！</h3>";
    echo "<p>測試帳號：</p>";
    echo "<ul>";
    echo "<li>一般用戶: test@example.com / 123456</li>";
    echo "<li>管理員: admin@nmnihealth.com / admin123</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>前往登入頁面</a></p>";
    
} catch(PDOException $e) {
    echo "連接失敗: " . $e->getMessage();
}
?>