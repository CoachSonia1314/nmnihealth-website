<?php
session_start();

// 处理课程报名表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $course = $_POST['course'] ?? '';
    
    // 验证必填字段
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($course)) {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 创建课程报名表（如果不存在）
            $stmt = $pdo->exec("CREATE TABLE IF NOT EXISTS course_enrollments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                course VARCHAR(100) NOT NULL,
                status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // 插入报名记录
            $stmt = $pdo->prepare("INSERT INTO course_enrollments (name, email, phone, course) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $course]);
            
            $success = true;
            $message = "報名提交成功！我們會盡快與您聯繫確認課程詳情。";
            
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
    <title>報名確認 - 幸福商學院</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .main-content {
            padding: 120px 0 50px;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .confirmation-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
        }
        
        .enrollment-details {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .next-steps {
            background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
        }
        
        .btn-action {
            background: #3B82F6;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 10px;
        }
        
        .btn-action:hover {
            background: #2563EB;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid #3B82F6;
            color: #3B82F6;
        }
        
        .btn-secondary:hover {
            background: #3B82F6;
            color: white;
        }
    </style>
</head>
<body class="font-inter">
    <header class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-sm shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="index.html" class="flex items-center space-x-2">
                        <span class="text-3B82F6 text-2xl"><i class="fa fa-graduation-cap"></i></span>
                        <span class="font-bold text-xl">幸福商學院</span>
                    </a>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="index.html#courses" class="text-gray-700 hover:text-3B82F6 font-medium transition-colors">課程介紹</a>
                    <a href="index.html#features" class="text-gray-700 hover:text-3B82F6 font-medium transition-colors">聯盟行銷</a>
                    <a href="index.html#testimonials" class="text-gray-700 hover:text-3B82F6 font-medium transition-colors">學員評價</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container mx-auto px-4">
            <div class="confirmation-card">
                <?php if (isset($success) && $success): ?>
                    <div class="text-center">
                        <div class="success-icon">
                            <i class="fa fa-check"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">報名成功！</h2>
                        <p class="text-lg text-gray-600 mb-6"><?php echo htmlspecialchars($message); ?></p>
                        
                        <div class="enrollment-details">
                            <h3 class="text-xl font-semibold mb-4">報名詳情</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600">姓名</p>
                                    <p class="font-semibold"><?php echo htmlspecialchars($name); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">郵箱</p>
                                    <p class="font-semibold"><?php echo htmlspecialchars($email); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">電話</p>
                                    <p class="font-semibold"><?php echo htmlspecialchars($phone); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">選擇課程</p>
                                    <p class="font-semibold"><?php echo htmlspecialchars($course); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="next-steps">
                            <h3 class="text-xl font-semibold mb-4">接下來的步驟</h3>
                            <ol class="space-y-2">
                                <li>我們會在24小時內致電確認課程詳情和時間安排</li>
                                <li>確認後會發送課程資料和付款資訊到您的郵箱</li>
                                <li>完成付款後即可開始您的學習之旅</li>
                                <li>如需修改或取消報名，請隨時聯繫我們</li>
                            </ol>
                        </div>
                        
                        <div class="text-center mt-8">
                            <h3 class="text-lg font-semibold mb-4">需要幫助？</h3>
                            <div class="flex justify-center gap-4">
                                <a href="tel:+886-954-014-591" class="btn-action">
                                    <i class="fa fa-phone mr-2"></i>
                                    立即致電
                                </a>
                                <a href="mailto:coachsonia1314@gmail.com" class="btn-action btn-secondary">
                                    <i class="fa fa-envelope mr-2"></i>
                                    發送郵件
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <div class="error-icon">
                            <i class="fa fa-times"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">報名失敗</h2>
                        <p class="text-lg text-gray-600 mb-6">
                            <?php echo isset($error) ? htmlspecialchars($error) : '發生未知錯誤，請稍後再試。'; ?>
                        </p>
                        
                        <div class="flex justify-center gap-4">
                            <a href="javascript:history.back()" class="btn-action btn-secondary">
                                <i class="fa fa-arrow-left mr-2"></i>
                                返回修改
                            </a>
                            <a href="index.html#enroll" class="btn-action">
                                <i class="fa fa-redo mr-2"></i>
                                重新報名
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-2 mb-6">
                    <span class="text-3B82F6 text-2xl"><i class="fa fa-graduation-cap"></i></span>
                    <span class="font-bold text-xl">幸福商學院</span>
                </div>
                <p class="text-gray-400 mb-4">提供高質量的線上課程，幫助學員掌握實用技能</p>
                <div class="flex space-x-4 justify-center mb-8">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-3B82F6 transition-colors">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-3B82F6 transition-colors">
                        <i class="fa fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-3B82F6 transition-colors">
                        <i class="fa fa-line"></i>
                    </a>
                </div>
                <p class="text-gray-500 text-sm">© 2025 @幸福創業家. 保留所有權利.</p>
            </div>
        </div>
    </footer>
</body>
</html>