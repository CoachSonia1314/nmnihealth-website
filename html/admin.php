<?php
session_start();

// 检查用户是否已登录且为管理员
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 连接数据库获取统计数据
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>後台管理系統 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            color: #8A2BE2;
            margin-bottom: 1rem;
        }
        
        .admin-nav {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .admin-nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 0;
            padding: 0;
            gap: 1rem;
        }
        
        .admin-nav li {
            margin: 0;
        }
        
        .admin-nav a {
            display: block;
            padding: 0.75rem 1.5rem;
            background: #8A2BE2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover {
            background: #7A1BD2;
        }
        
        .admin-content {
            margin-top: 2rem;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #8A2BE2;
            margin: 0.5rem 0;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .action-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .action-card h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .btn {
            background: #8A2BE2;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #7A1BD2;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .admin-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>後台管理系統</h1>
        <p>管理您的網站內容和用戶數據</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">返回首頁</a></li>
            <li><a href="admin.php">後台首頁</a></li>
            <li><a href="admin_articles.php">文章管理</a></li>
            <li><a href="admin_experts.php">專家管理</a></li>
            <li><a href="admin_appointments.php">預約管理</a></li>
            <li><a href="admin_products.php">產品管理</a></li>
            <li><a href="admin_testimonials.php">用戶見證管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="admin-container">
            <div class="admin-info">
                <p>管理員：<?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo htmlspecialchars($_SESSION['user_email']); ?>)</p>
            </div>
            
            <div class="admin-header">
                <h1>後台管理系統</h1>
                <p>歡迎來到NmNiHealth後台管理面板</p>
            </div>
            
            <div class="admin-nav">
                <ul>
                    <li><a href="admin_users.php">會員管理</a></li>
                    <li><a href="admin_articles.php">文章管理</a></li>
                    <li><a href="admin_experts.php">專家管理</a></li>
                    <li><a href="admin_appointments.php">預約管理</a></li>
                    <li><a href="admin_products.php">產品管理</a></li>
                    <li><a href="admin_testimonials.php">用戶見證管理</a></li>
                    <li><a href="admin_guide.php">管理說明</a></li>
                </ul>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>會員總數</h3>
                    <div class="stat-value">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $result['count'];
                        } catch(PDOException $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>文章總數</h3>
                    <div class="stat-value">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $result['count'];
                        } catch(PDOException $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>專家人數</h3>
                    <div class="stat-value">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM experts");
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $result['count'];
                        } catch(PDOException $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
                
                <div class="stat-card">
                    <h3>預約總數</h3>
                    <div class="stat-value">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments");
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo $result['count'];
                        } catch(PDOException $e) {
                            echo "0";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="quick-actions">
                <div class="action-card">
                    <h3>會員管理</h3>
                    <p>查看和管理網站會員信息</p>
                    <a href="admin_users.php" class="btn">進入會員管理</a>
                </div>
                
                <div class="action-card">
                    <h3>文章管理</h3>
                    <p>創建、編輯和管理網站文章內容</p>
                    <a href="admin_articles.php" class="btn">進入文章管理</a>
                </div>
                
                <div class="action-card">
                    <h3>專家管理</h3>
                    <p>添加、編輯和管理預約專家信息</p>
                    <a href="admin_experts.php" class="btn">進入專家管理</a>
                </div>
                
                <div class="action-card">
                    <h3>預約管理</h3>
                    <p>查看和管理會員預約記錄</p>
                    <a href="admin_appointments.php" class="btn">進入預約管理</a>
                </div>
                
                <div class="action-card">
                    <h3>產品管理</h3>
                    <p>添加、編輯和管理產品信息</p>
                    <a href="admin_products.php" class="btn">進入產品管理</a>
                </div>
                
                <div class="action-card">
                    <h3>用戶見證管理</h3>
                    <p>添加、編輯和管理用戶見證內容</p>
                    <a href="admin_testimonials.php" class="btn">進入用戶見證管理</a>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
</body>
</html>