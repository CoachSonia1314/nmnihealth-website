<?php
session_start();

// 檢查是否有測驗結果
if (!isset($_SESSION['quiz_score']) || !isset($_SESSION['quiz_answers'])) {
    header("Location: menopause_quiz.php");
    exit();
}

// 獲取測驗結果
$total_score = $_SESSION['quiz_score'];
$answers = $_SESSION['quiz_answers'];

// 連接資料庫
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 保存診斷結果到資料庫
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $answers_json = json_encode($answers);
        
        $stmt = $pdo->prepare("INSERT INTO user_diagnoses (user_id, quiz_score, quiz_answers) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $total_score, $answers_json]);
    }
} catch(PDOException $e) {
    // 資料庫錯誤不影響頁面顯示
    error_log("資料庫錯誤: " . $e->getMessage());
}

// 根據總分確定症狀嚴重程度
function getSeverityLevel($score) {
    if ($score <= 10) {
        return ['level' => '輕微', 'color' => '#28a745', 'description' => '您的更年期症狀較輕微，繼續保持健康的生活方式即可。'];
    } elseif ($score <= 20) {
        return ['level' => '中等', 'color' => '#ffc107', 'description' => '您有中等程度的更年期症狀，建議關注並採取適當的調理措施。'];
    } else {
        return ['level' => '嚴重', 'color' => '#dc3545', 'description' => '您的更年期症狀較為嚴重，建議儘快尋求專業醫療幫助。'];
    }
}

// 獲取個人化建議
function getRecommendations($score) {
    $recommendations = [];
    
    if ($score <= 10) {
        $recommendations = [
            '繼續保持均衡飲食，多吃富含植物雌激素的食物',
            '維持規律的運動習慣，如散步、瑜伽等',
            '保持良好的作息時間，確保充足睡眠',
            '定期進行健康檢查'
        ];
    } elseif ($score <= 20) {
        $recommendations = [
            '建議調整飲食結構，增加大豆製品攝入',
            '適當增加運動量，如游泳、太極等低強度運動',
            '學習壓力管理技巧，如冥想、深呼吸',
            '考慮諮詢專業醫生，了解更年期保健方案',
            '保持積極社交活動，維護心理健康'
        ];
    } else {
        $recommendations = [
            '強烈建議儘快諮詢更年期專科醫生',
            '可能需要進行荷爾蒙檢測和其他相關檢查',
            '在醫生指導下考慮荷爾蒙替代療法或其他治療方案',
            '尋求心理支持，必要時進行心理諮詢',
            '加入更年期支持小組，與其他女性分享經驗'
        ];
    }
    
    return $recommendations;
}

$severity = getSeverityLevel($total_score);
$recommendations = getRecommendations($total_score);

// 獲取推薦產品
$recommended_products = [];

try {
    // 建立症狀到產品類別的映射
    $symptom_to_category = [
        1 => '荷爾蒙調節',  // 熱潮紅
        2 => '睡眠改善',    // 夜間盜汗
        3 => '睡眠改善',    // 失眠
        4 => '情緒管理',    // 情緒波動
        5 => '頭痛緩解',    // 頭痛
        6 => '心悸緩解',    // 心悸
        7 => '情緒管理',    // 情緒抑鬱
        8 => '記憶改善',    // 注意力不集中
        9 => '私密保養',    // 陰道乾澀
        10 => '泌尿保健',   // 尿失禁
        11 => '關節保健',   // 骨關節疼痛
        12 => '皮膚保養',   // 皮膚乾燥
        13 => '能量補充',   // 疲勞
        14 => '代謝調節',   // 體重增加
        15 => '月經調理'    // 月經不規律
    ];
    
    // 根據最嚴重的症狀推薦產品
    $max_symptom_id = array_keys($answers, max($answers))[0];
    $category = $symptom_to_category[$max_symptom_id] ?? '綜合保健';
    
    // 查詢推薦產品
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND status = 'active' ORDER BY RAND() LIMIT 3");
    $stmt->execute([$category]);
    $recommended_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 如果沒有找到特定類別的產品，推薦綜合保健產品
    if (empty($recommended_products)) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = '綜合保健' AND status = 'active' ORDER BY RAND() LIMIT 3");
        $stmt->execute();
        $recommended_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    // 資料庫錯誤不影響頁面顯示
    error_log("資料庫錯誤: " . $e->getMessage());
}

// 处理重新测试
if (isset($_GET['action']) && $_GET['action'] === 'retry') {
    unset($_SESSION['quiz_score']);
    unset($_SESSION['quiz_answers']);
    header("Location: menopause_quiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>測驗結果 - 更年期症狀自檢表 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .result-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .result-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .result-header h1 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .score-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .score-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        
        .severity-level {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
            margin: 1rem 0;
        }
        
        .recommendations {
            margin: 2rem 0;
        }
        
        .recommendations h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .recommendations ul {
            padding-left: 1.5rem;
        }
        
        .recommendations li {
            margin-bottom: 0.5rem;
        }
        
        .actions {
            text-align: center;
            margin: 2rem 0;
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
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .services {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .service-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        /* 產品推薦區塊 */
        .products-section {
            margin: 3rem 0;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .product-card {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            height: 150px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-info h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
            margin: 0.5rem 0;
        }
        
        .product-category {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>更年期症狀自檢表</h1>
        <p>了解您的更年期症状程度，获得专业建议</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">幸福熟齡(文章區)</a></li>
            <li><a href="appointment.php">專家諮詢</a></li>
            <li><a href="login.php">會員登入</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="result-container">
            <div class="result-header">
                <h1>測驗結果</h1>
                <p>根據您的回答，我們為您提供了個人化的分析和建議</p>
            </div>
            
            <div class="score-card">
                <h2>您的總分</h2>
                <div class="score-value"><?php echo $total_score; ?>/30</div>
                <div class="severity-level" style="background-color: <?php echo $severity['color']; ?>; color: white;">
                    症狀程度：<?php echo $severity['level']; ?>
                </div>
                <p><?php echo $severity['description']; ?></p>
            </div>
            
            <div class="recommendations">
                <h3>專業建議</h3>
                <ul>
                    <?php foreach ($recommendations as $recommendation): ?>
                    <li><?php echo $recommendation; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <?php if (!empty($recommended_products)): ?>
            <div class="products-section">
                <h2 class="section-title">為您推薦的保健產品</h2>
                <div class="products-grid">
                    <?php foreach ($recommended_products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                            <span>產品圖片</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                            <div class="product-price">NT$<?php echo number_format($product['price']); ?></div>
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="btn">查看詳情</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="/products/" class="btn btn-secondary">查看所有產品</a>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="services">
                <div class="service-card">
                    <h3>專家諮詢</h3>
                    <p>我們的更年期專家可以為您提供個人化的諮詢服務，制定適合您的健康管理方案。</p>
                    <a href="appointment.php" class="btn btn-success">預約專家諮詢</a>
                </div>
                
                <div class="service-card">
                    <h3>加入支持小組</h3>
                    <p>加入我們的LINE群組，與其他更年期女性分享經驗，獲得支持和鼓勵。</p>
                    <a href="https://line.me/ti/g2/your_line_group_link" class="btn btn-warning">加入LINE群組</a>
                </div>
            </div>
            
            <div class="actions">
                <a href="quiz_result.php?action=retry" class="btn btn-secondary">重新測驗</a>
                <a href="index.html" class="btn">返回首頁</a>
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