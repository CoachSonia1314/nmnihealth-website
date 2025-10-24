<?php
// 處理課程購買表單提交和模擬付款

// 檢查是否是表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 獲取表單數據
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $course_id = htmlspecialchars($_POST['course_id']);
    
    // 資料庫連接設定
    $servername = "localhost";
    $username = "nmnihealth";
    $password = "health2025";
    $dbname = "nmnihealth";
    
    try {
        // 建立PDO連接
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 獲取課程信息
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 檢查課程是否存在
        if (!$course) {
            die("無效的課程方案");
        }
        
        // 獲取價格
        $price = $course['price'];
        $course_title = $course['title'];
        
        // 生成訂單號
        $order_id = date("YmdHis") . rand(1000, 9999);
        
        // 儲存訂單信息到資料庫
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_name, user_email, course_id, course_title, price, order_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $course_id, $course_title, $price, $order_id]);
        
        // 顯示訂單確認頁面
        ?>
        <!DOCTYPE html>
        <html lang="zh-TW">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>訂單確認 - 綻放 BloomWell</title>
            <link rel="stylesheet" href="styles.css">
            <link rel="stylesheet" href="css/fonts.css">
        </head>
        <body>
            <header>
                <div class="brand-logo">
                    <img src="images/logo.svg" alt="綻放 BloomWell Logo" class="logo-icon">
                    <div class="brand-name">
                        <span class="chinese">綻放</span>
                        <span class="english">BloomWell</span>
                    </div>
                </div>
                <p>陪伴您舒適自在度過更年期，迎向人生下半場的健康美好生活</p>
            </header>
            
            <nav>
                <ul>
                    <li><a href="index.html">首頁</a></li>
                    <li><a href="index.html#knowledge">幸福熟齡</a></li>
                    <li><a href="appointment.php">專家諮詢</a></li>
                    <li><a href="login.php">會員登入</a></li>
                </ul>
            </nav>
            
            <div class="container">
                <section class="section">
                    <h2>訂單確認</h2>
                    <p>感謝您的購買，<?php echo $name; ?>！</p>
                    
                    <div class="feature" style="text-align: left; max-width: 600px; margin: 2rem auto;">
                        <h3>訂單詳情</h3>
                        <p><strong>訂單號：</strong> <?php echo $order_id; ?></p>
                        <p><strong>姓名：</strong> <?php echo $name; ?></p>
                        <p><strong>電子信箱：</strong> <?php echo $email; ?></p>
                        <p><strong>課程方案：</strong> <?php echo $course_title; ?></p>
                        <p><strong>價格：</strong> NT$ <?php echo number_format($price); ?></p>
                    </div>
                    
                    <div class="feature" style="text-align: left; max-width: 600px; margin: 2rem auto;">
                        <h3>付款方式</h3>
                        <p>請選擇以下任一方式完成付款：</p>
                        <ul>
                            <li><strong>銀行轉帳：</strong> 
                                <p>銀行：玉山銀行</p>
                                <p>帳號：123-456-789012</p>
                                <p>戶名：NmNiHealth</p>
                            </li>
                            <li style="margin-top: 1rem;"><strong>ATM轉帳：</strong> 
                                <p>銀行代碼：808</p>
                                <p>帳號：12345678901234</p>
                            </li>
                        </ul>
                        <p style="margin-top: 1rem;">轉帳完成後請保留單據，我們將於1個工作日內確認您的付款並發送課程資訊至您的電子信箱。</p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="ai_course.php" class="btn">返回課程頁面</a>
                    </div>
                </section>
            </div>
            
            <footer>
                <div class="container">
                    <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
                    <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
                </div>
            </footer>
        </body>
        </html>
        <?php
    } catch(PDOException $e) {
        echo "資料庫錯誤: " . $e->getMessage();
    }
} else {
    // 如果不是表單提交，重定向到課程頁面
    header("Location: ai_course.php");
    exit();
}
?>