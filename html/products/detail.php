<?php
// 產品詳細頁面
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

// 獲取產品ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: /products/");
    exit();
}

// 查詢產品信息
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: /products/");
    exit();
}

// 獲取用戶診斷結果（如果有）
$user_id = $_SESSION['user_id'];
$quiz_score = null;
$user_diagnosis = null;

// 查詢用戶最近的診斷結果
$stmt = $pdo->prepare("SELECT quiz_score, quiz_answers FROM user_diagnoses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$user_diagnosis = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_diagnosis) {
    $quiz_score = $user_diagnosis['quiz_score'];
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - NmNiHealth</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/fonts.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .product-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .product-category {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }
        
        .product-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .product-content {
                grid-template-columns: 1fr;
            }
        }
        
        .product-image {
            height: 400px;
            background: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
        }
        
        .product-price {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
            margin: 1rem 0;
        }
        
        .product-description {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .product-description h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
            padding: 1rem 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .meta-value {
            font-weight: bold;
            color: var(--primary-color);
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
            text-align: center;
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
        
        .recommendation-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .recommendation-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .recommendation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .recommendation-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }
        
        .recommendation-item h4 {
            margin: 0.5rem 0;
            font-size: 0.875rem;
        }
        
        .recommendation-item .price {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <header>
        <h1>產品詳細資訊</h1>
        <p>了解產品的詳細資訊和功效</p>
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
        <div class="product-detail-container">
            <div class="product-header">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
            </div>
            
            <div class="product-content">
                <div class="product-image">
                    <?php if ($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <span>產品圖片</span>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-price">NT$<?php echo number_format($product['price']); ?></div>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <div class="meta-label">產品編號</div>
                            <div class="meta-value"><?php echo $product['id']; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">庫存狀態</div>
                            <div class="meta-value"><?php echo $product['stock'] > 0 ? '有貨' : '缺貨'; ?></div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label">上架日期</div>
                            <div class="meta-value"><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></div>
                        </div>
                    </div>
                    
                    <a href="#" class="btn btn-success">加入購物車</a>
                    <a href="#" class="btn btn-warning">立即購買</a>
                    <a href="/products/" class="btn btn-secondary">返回產品列表</a>
                </div>
            </div>
            
            <div class="product-description">
                <h3>產品介紹</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>
            
            <div class="product-description">
                <h3>產品功效</h3>
                <p><?php echo nl2br(htmlspecialchars($product['benefits'])); ?></p>
            </div>
            
            <?php if ($user_diagnosis): ?>
            <div class="recommendation-section">
                <h3>個人化推薦</h3>
                <p>根據您的健康診斷結果，這款產品非常適合您：</p>
                <div class="diagnosis-info">
                    <p>您的診斷分數：<?php echo $quiz_score; ?>/30</p>
                    <p>症狀程度：<?php 
                        if ($quiz_score <= 10) echo '輕微';
                        elseif ($quiz_score <= 20) echo '中等';
                        else echo '嚴重';
                    ?></p>
                </div>
            </div>
            <?php endif; ?>
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