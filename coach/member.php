<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取用户信息
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// 处理登出
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員中心 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .member-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .welcome-section {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .dashboard-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
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
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .user-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>會員中心</h1>
        <p>歡迎回來，<?php echo htmlspecialchars($user_name); ?>！</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">幸福熟齡(文章區)</a></li>
            <li><a href="appointment.php">專家諮詢</a></li>
            <li><a href="member.php">會員中心</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="member-container">
            <div class="welcome-section">
                <h2>會員專屬空間</h2>
                <p>在這裡您可以管理您的健康資訊和預約記錄</p>
            </div>
            
            <div class="user-info">
                <h3>會員資訊</h3>
                <p><strong>姓名：</strong><?php echo htmlspecialchars($user_name); ?></p>
                <p><strong>電子信箱：</strong><?php echo htmlspecialchars($user_email); ?></p>
            </div>
            
            <div class="dashboard">
                <div class="dashboard-card">
                    <h3>個人健康記錄</h3>
                    <p>追蹤您的健康數據，監測更年期症狀變化趨勢</p>
                    <a href="#" class="btn">查看記錄</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>專屬健康方案</h3>
                    <p>訪問為您量身定制的健康建議和養生方案</p>
                    <a href="#" class="btn">查看方案</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>預約記錄</h3>
                    <p>查看和管理您的專家諮詢預約記錄</p>
                    <a href="appointment.php" class="btn">查看預約</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>會員專屬內容</h3>
                    <p>訪問專為會員準備的獨家健康資訊</p>
                    <a href="#" class="btn">查看內容</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>聯盟行銷分潤</h3>
                    <p>查看您的聯盟行銷分潤成效和匯款資訊</p>
                    <a href="affiliate_dashboard.php" class="btn">分潤查詢</a>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="member.php?action=logout" class="btn btn-danger">登出帳號</a>
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