<?php
// 產品推薦系統 - 前台頁面
session_start();

// 檢查用戶是否已登錄
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

// 數據庫連接
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("數據庫連接失敗: " . $e->getMessage());
}

// 獲取用戶診斷結果（如果有）
$user_id = $_SESSION['user_id'];
$quiz_score = null;
$recommendations = [];

// 查詢用戶最近的診斷結果
$stmt = $pdo->prepare("SELECT quiz_score, quiz_answers FROM user_diagnoses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$diagnosis = $stmt->fetch(PDO::FETCH_ASSOC);

if ($diagnosis) {
    $quiz_score = $diagnosis['quiz_score'];
    $quiz_answers = json_decode($diagnosis['quiz_answers'], true);
    
    // 根據診斷結果推薦產品
    $recommendations = getRecommendedProducts($pdo, $quiz_score, $quiz_answers);
}

// 獲取所有產品
$stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC");
$all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 產品推薦函數
function getRecommendedProducts($pdo, $score, $answers) {
    // 根據診斷分數和答案推薦產品
    $recommended_products = [];
    
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
    
    // 根據嚴重程度推薦產品
    if ($score <= 10) {
        $severity = '輕微';
    } elseif ($score <= 20) {
        $severity = '中等';
    } else {
        $severity = '嚴重';
    }
    
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
    
    return $recommended_products;
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>保健產品推薦 - NmNiHealth</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/fonts.css">
    <style>
        .products-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .products-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .products-header h1 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .recommendations-section {
            margin-bottom: 3rem;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            height: 200px;
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
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .no-recommendations {
            text-align: center;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .diagnosis-info {
            background: #e9ecef;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .diagnosis-info h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .severity-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        
        .severity-mild {
            background: #28a745;
            color: white;
        }
        
        .severity-moderate {
            background: #ffc107;
            color: #212529;
        }
        
        .severity-severe {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>保健產品推薦</h1>
        <p>根據您的健康診斷結果，為您推薦最適合的保健產品</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="/index.html">首頁</a></li>
            <li><a href="/index.html#knowledge">幸福熟齡(文章區)</a></li>
            <li><a href="/appointment.php">專家諮詢</a></li>
            <li><a href="/member.php">會員中心</a></li>
            <li><a href="/products/">產品推薦</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="products-container">
            <div class="products-header">
                <h1>個人化保健產品推薦</h1>
                <p>根據您的健康診斷結果，我們為您精心挑選了最適合的保健產品</p>
            </div>
            
            <?php if ($diagnosis): ?>
            <div class="diagnosis-info">
                <h3>您的診斷結果</h3>
                <div class="severity-badge severity-<?php 
                    if ($quiz_score <= 10) echo 'mild';
                    elseif ($quiz_score <= 20) echo 'moderate';
                    else echo 'severe';
                ?>">
                    症狀程度：<?php 
                    if ($quiz_score <= 10) echo '輕微';
                    elseif ($quiz_score <= 20) echo '中等';
                    else echo '嚴重';
                    ?>
                </div>
                <p>總分：<?php echo $quiz_score; ?>/30</p>
                <p>我們根據您的診斷結果，為您推薦以下產品：</p>
            </div>
            
            <?php if (!empty($recommendations)): ?>
            <div class="recommendations-section">
                <h2 class="section-title">為您推薦的產品</h2>
                <div class="products-grid">
                    <?php foreach ($recommendations as $product): ?>
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
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-price">NT$<?php echo number_format($product['price']); ?></div>
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="btn">查看詳情</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="no-recommendations">
                <h3>暫時沒有推薦產品</h3>
                <p>我們會根據您的診斷結果為您推薦最適合的保健產品，請稍後再回來查看。</p>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="no-recommendations">
                <h3>尚未進行健康診斷</h3>
                <p>請先完成更年期症狀自檢表，我們將根據您的診斷結果為您推薦最適合的保健產品。</p>
                <a href="/menopause_quiz_new.php" class="btn btn-success">開始健康診斷</a>
            </div>
            <?php endif; ?>
            
            <div class="all-products-section">
                <h2 class="section-title">所有保健產品</h2>
                <div class="products-grid">
                    <?php foreach ($all_products as $product): ?>
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
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-price">NT$<?php echo number_format($product['price']); ?></div>
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="btn">查看詳情</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
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