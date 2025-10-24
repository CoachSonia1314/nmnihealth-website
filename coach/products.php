<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 获取用户信息
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$membership_level = $_SESSION['membership_level'] ?? 'free';

// 连接数据库
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 根据会员等级获取产品
    $stmt = $pdo->prepare("SELECT * FROM products WHERE membership_level = ? OR membership_level = 'free' ORDER BY membership_level, price");
    $stmt->execute([$membership_level]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 会员等级显示文本
    $membership_levels = [
        'free' => '免費會員',
        'premium' => '黃金會員',
        'vip' => '鑽石會員'
    ];
    
    $membership_display = $membership_levels[$membership_level] ?? '免費會員';
    
    // 会员等级折扣
    $membership_discounts = [
        'free' => 0,      // 免费会员无折扣
        'premium' => 10,  // 黄金会员10%折扣
        'vip' => 20       // 钻石会员20%折扣
    ];
    
    $discount_rate = $membership_discounts[$membership_level] ?? 0;
    
} catch(PDOException $e) {
    $error = "数据库连接失败: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>產品推薦 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .products-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .membership-info {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: center;
        }
        
        .membership-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }
        
        .membership-free {
            background-color: #6c757d;
            color: white;
        }
        
        .membership-premium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .membership-vip {
            background-color: #0d6efd;
            color: white;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 1rem 0;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 1rem;
            margin-right: 0.5rem;
        }
        
        .discount-badge {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
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
        
        .membership-level {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 0.8rem;
            margin: 0.5rem 0;
        }
        
        .level-free {
            background-color: #6c757d;
            color: white;
        }
        
        .level-premium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .level-vip {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>產品推薦</h1>
        <p>專為 <?php echo htmlspecialchars($user_name); ?> 推薦的健康產品</p>
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
        <div class="products-container">
            <div class="membership-info">
                <p>您的會員等級：<?php echo htmlspecialchars($membership_display); ?>
                    <span class="membership-badge membership-<?php echo $membership_level; ?>">
                        <?php echo strtoupper($membership_level); ?>
                    </span>
                </p>
                <?php if ($discount_rate > 0): ?>
                    <p>您享有 <?php echo $discount_rate; ?>% 專屬折扣</p>
                <?php else: ?>
                    <p>升級會員可享更多專屬折扣</p>
                <?php endif; ?>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <h2>推薦產品</h2>
            <p>根據您的會員等級，以下是為您精選的健康產品</p>
            
            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        
                        <div class="membership-level level-<?php echo $product['membership_level']; ?>">
                            <?php 
                            $level_names = [
                                'free' => '免費會員可購',
                                'premium' => '黃金會員專享',
                                'vip' => '鑽石會員專享'
                            ];
                            echo $level_names[$product['membership_level']];
                            ?>
                        </div>
                        
                        <div class="product-price">
                            <?php if ($product['membership_level'] !== 'free' && $discount_rate > 0): ?>
                                <span class="original-price">NT$ <?php echo number_format($product['price']); ?></span>
                                NT$ <?php echo number_format($product['price'] * (100 - $discount_rate) / 100); ?>
                                <span class="discount-badge"><?php echo $discount_rate; ?>% OFF</span>
                            <?php else: ?>
                                NT$ <?php echo number_format($product['price']); ?>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn" onclick="addToCart(<?php echo $product['id']; ?>)">加入購物車</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>暫無推薦產品</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        function addToCart(productId) {
            alert('產品已加入購物車！（此為演示功能）');
            // 在實際應用中，這裡會發送 AJAX 請求將產品添加到購物車
        }
    </script>
</body>
</html>