<?php
// 教练预约处理页面
session_start();

// 处理预约表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $service = $_POST['service'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // 验证必填字段
    if (!empty($name) && !empty($phone) && !empty($email) && !empty($service)) {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 创建教练预约表（如果不存在）
            $stmt = $pdo->exec("CREATE TABLE IF NOT EXISTS coach_bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                email VARCHAR(100) NOT NULL,
                service VARCHAR(50) NOT NULL,
                message TEXT,
                status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 插入预约记录
            $stmt = $pdo->prepare("INSERT INTO coach_bookings (name, phone, email, service, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $service, $message]);
            
            $success = true;
            $message = "預約提交成功！我們會盡快與您聯繫確認。";
            
        } catch(PDOException $e) {
            $error = "系統錯誤，請稍後再試。";
        }
    } else {
        $error = "請填寫所有必填字段。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約確認 - Coach NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="images/logo.svg" alt="Coach NmNiHealth" height="40">
                <span>Coach NmNiHealth</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">首頁</a></li>
                <li><a href="index.html#services">服務</a></li>
                <li><a href="index.html#about">關於</a></li>
                <li><a href="index.html#contact">聯絡</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="confirmation-card">
                <?php if (isset($success) && $success): ?>
                    <div class="success-message">
                        <h2>✅ 預約成功！</h2>
                        <p><?php echo htmlspecialchars($message); ?></p>
                        <div class="booking-details">
                            <h3>預約詳情：</h3>
                            <p><strong>姓名：</strong><?php echo htmlspecialchars($name); ?></p>
                            <p><strong>電話：</strong><?php echo htmlspecialchars($phone); ?></p>
                            <p><strong>郵箱：</strong><?php echo htmlspecialchars($email); ?></p>
                            <p><strong>服務項目：</strong><?php echo htmlspecialchars($service); ?></p>
                            <?php if (!empty($message)): ?>
                                <p><strong>留言：</strong><?php echo htmlspecialchars($message); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="next-steps">
                            <h3>接下來的步驟：</h3>
                            <ol>
                                <li>我們會在24小時內致電確認預約時間</li>
                                <li>確認後會發送詳細資訊到您的郵箱</li>
                                <li>如需修改或取消預約，請隨時聯繫我們</li>
                            </ol>
                        </div>
                        <div class="action-buttons">
                            <a href="index.html" class="btn-coach">返回首頁</a>
                            <a href="tel:+886-912345678" class="btn-coach">立即致電</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="error-message">
                        <h2>❌ 預約失敗</h2>
                        <p><?php echo isset($error) ? htmlspecialchars($error) : '發生未知錯誤'; ?></p>
                        <div class="action-buttons">
                            <a href="javascript:history.back()" class="btn-coach">返回修改</a>
                            <a href="index.html#contact" class="btn-coach">重新預約</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Coach NmNiHealth. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .main-content {
            padding: 100px 0 50px;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .confirmation-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .success-message {
            text-align: center;
        }
        
        .success-message h2 {
            color: #28a745;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .error-message h2 {
            color: #dc3545;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .booking-details {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: left;
        }
        
        .booking-details h3 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .booking-details p {
            margin-bottom: 0.5rem;
            color: #555;
        }
        
        .next-steps {
            text-align: left;
            margin: 2rem 0;
        }
        
        .next-steps h3 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .next-steps ol {
            color: #555;
            line-height: 1.8;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-coach {
            background: #667eea;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .btn-coach:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
    </style>
</body>
</html>