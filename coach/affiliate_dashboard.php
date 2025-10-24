<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 连接数据库
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 获取用户信息
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header("Location: login.php");
        exit();
    }
    
    // 获取用户的联盟营销分润数据
    $stmt = $pdo->prepare("
        SELECT ac.*, a.title as article_title 
        FROM affiliate_commissions ac 
        JOIN articles a ON ac.article_id = a.id 
        WHERE ac.user_id = ? 
        ORDER BY ac.last_updated DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $commissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 计算总收益
    $total_earnings = 0;
    $total_clicks = 0;
    $total_conversions = 0;
    
    foreach ($commissions as $commission) {
        $total_earnings += $commission['total_earnings'];
        $total_clicks += $commission['click_count'];
        $total_conversions += $commission['conversion_count'];
    }
    
    // 处理汇款账户信息更新
    $message = '';
    $error = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bank_account'])) {
        $bank_name = $_POST['bank_name'];
        $account_number = $_POST['account_number'];
        $account_holder = $_POST['account_holder'];
        
        if (!empty($bank_name) && !empty($account_number) && !empty($account_holder)) {
            try {
                $stmt = $pdo->prepare("UPDATE users SET bank_name = ?, account_number = ?, account_holder = ? WHERE id = ?");
                $stmt->execute([$bank_name, $account_number, $account_holder, $_SESSION['user_id']]);
                
                // 更新用户信息
                $user['bank_name'] = $bank_name;
                $user['account_number'] = $account_number;
                $user['account_holder'] = $account_holder;
                
                $message = "匯款帳戶資訊已更新成功！";
            } catch (PDOException $e) {
                $error = "更新失敗，請稍後再試。";
            }
        } else {
            $error = "請填寫所有必填欄位。";
        }
    }
    
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>聯盟行銷分潤查詢 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .affiliate-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px;
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 0.5rem 0;
        }
        
        .commission-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
        }
        
        .commission-table th,
        .commission-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .commission-table th {
            background: #f8f9fa;
            color: var(--primary-color);
        }
        
        .commission-table tr:hover {
            background: #f8f9fa;
        }
        
        .bank-info-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .bank-info-section h2 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: var(--font-primary);
            font-size: 1rem;
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
        
        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .commission-rate {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>聯盟行銷分潤查詢</h1>
        <p>查看您的聯盟行銷分潤成效</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="member.php">會員中心</a></li>
            <li><a href="affiliate_dashboard.php">聯盟行銷</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="affiliate-container">
            <div class="page-header">
                <h1>聯盟行銷分潤儀表板</h1>
                <p>歡迎回來，<?php echo htmlspecialchars($user['name']); ?>！</p>
            </div>
            
            <?php if ($message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="stats-summary">
                <div class="stat-card">
                    <h3>總收益</h3>
                    <div class="stat-value">NT$ <?php echo number_format($total_earnings, 2); ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>總點擊數</h3>
                    <div class="stat-value"><?php echo $total_clicks; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>總轉換數</h3>
                    <div class="stat-value"><?php echo $total_conversions; ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>文章數量</h3>
                    <div class="stat-value"><?php echo count($commissions); ?></div>
                </div>
            </div>
            
            <h2>分潤明細</h2>
            <?php if (empty($commissions)): ?>
            <p>目前還沒有聯盟行銷分潤記錄。</p>
            <?php else: ?>
            <table class="commission-table">
                <thead>
                    <tr>
                        <th>文章標題</th>
                        <th>分潤比例</th>
                        <th>點擊數</th>
                        <th>轉換數</th>
                        <th>收益</th>
                        <th>最後更新</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commissions as $commission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($commission['article_title']); ?></td>
                        <td class="commission-rate"><?php echo $commission['commission_rate']; ?>%</td>
                        <td><?php echo $commission['click_count']; ?></td>
                        <td><?php echo $commission['conversion_count']; ?></td>
                        <td>NT$ <?php echo number_format($commission['total_earnings'], 2); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($commission['last_updated'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <div class="bank-info-section">
                <h2>匯款帳戶資訊</h2>
                <p>請填寫您的匯款帳戶資訊，我們將於每月月底結算並匯款至您的帳戶。</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="bank_name">銀行名稱 *</label>
                        <input type="text" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($user['bank_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="account_number">帳戶號碼 *</label>
                        <input type="text" id="account_number" name="account_number" value="<?php echo htmlspecialchars($user['account_number'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="account_holder">戶名 *</label>
                        <input type="text" id="account_holder" name="account_holder" value="<?php echo htmlspecialchars($user['account_holder'] ?? ''); ?>" required>
                    </div>
                    
                    <button type="submit" class="btn">更新帳戶資訊</button>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="member.php" class="btn btn-secondary">返回會員中心</a>
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