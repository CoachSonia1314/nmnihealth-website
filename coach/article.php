<?php
// 前端文章展示頁面
session_start();

// 獲取文章ID
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$article_id && !$slug) {
    header("Location: index.html");
    exit();
}

// 連接資料庫
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 根據ID或slug獲取文章
    if ($article_id) {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND status = 'published'");
        $stmt->execute([$article_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
    }
    
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        header("Location: index.html");
        exit();
    }
    
    // 獲取分類資訊
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$article['category_id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("資料庫連接失敗: " . $e->getMessage());
}

// 檢查用戶是否為會員
$is_member = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
$membership_level = 'free'; // 默認免費會員

if ($is_member) {
    try {
        $stmt = $pdo->prepare("SELECT membership_level FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $membership_level = $user['membership_level'];
        }
    } catch(PDOException $e) {
        // 如果獲取用戶會員等級失敗，使用默認值
    }
}

// 處理聯盟行銷連結點擊
if (isset($_GET['affiliate']) && $article['affiliate_links']) {
    $affiliate_id = intval($_GET['affiliate']);
    $affiliate_links = explode("
", $article['affiliate_links']);
    
    if (isset($affiliate_links[$affiliate_id])) {
        $link_parts = explode("|", $affiliate_links[$affiliate_id]);
        if (isset($link_parts[0])) {
            // 記錄點擊（實際應用中應該記錄到資料庫）
            error_log("Affiliate link clicked: " . $link_parts[0] . " by user: " . ($is_member ? $_SESSION['user_id'] : 'guest'));
            
            // 重定向到聯盟連結
            header("Location: " . trim($link_parts[0]));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - NmNiHealth</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['description'] ?? $article['excerpt'] ?? ''); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($article['keywords'] ?? ''); ?>">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .article-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .article-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .article-title {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .article-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .article-content {
            line-height: 1.8;
        }
        
        .article-content h2 {
            color: var(--primary-color);
            margin-top: 2rem;
        }
        
        .article-content h3 {
            color: var(--secondary-color);
            margin-top: 1.5rem;
        }
        
        .article-content ul, .article-content ol {
            padding-left: 1.5rem;
        }
        
        .article-content li {
            margin-bottom: 0.5rem;
        }
        
        .featured-image {
            width: 100%;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .affiliate-section {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin: 2rem 0;
            border-radius: 0 10px 10px 0;
        }
        
        .affiliate-section h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .affiliate-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .affiliate-link {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .affiliate-link:hover {
            transform: translateY(-3px);
        }
        
        .affiliate-link h4 {
            margin-top: 0;
            color: var(--primary-color);
        }
        
        .affiliate-link .commission {
            color: #28a745;
            font-weight: bold;
        }
        
        .member-benefits {
            background: linear-gradient(135deg, #6B21A8, #F472B6);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .member-benefits h3 {
            color: white;
            margin-top: 0;
        }
        
        .member-benefits ul {
            padding-left: 1.5rem;
        }
        
        .member-benefits li {
            margin-bottom: 0.5rem;
        }
        
        .login-prompt {
            background: #fff3cd;
            color: #856404;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #7A1BD2;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .btn-success {
            background-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
        }
        
        .share-section {
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: 2rem;
            text-align: center;
        }
        
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .share-button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
        }
        
        .share-facebook { background: #3b5998; }
        .share-twitter { background: #1da1f2; }
        .share-line { background: #00c300; }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
        <p>專業的更年期健康保健資訊</p>
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
        <div class="article-container">
            <div class="article-header">
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="article-meta">
                    <span>分類：<?php echo htmlspecialchars($category['name'] ?? ''); ?></span> | 
                    <span>發布時間：<?php echo date('Y年m月d日', strtotime($article['published_at'] ?? $article['created_at'])); ?></span>
                </div>
            </div>
            
            <?php if ($article['featured_image']): ?>
            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="featured-image">
            <?php endif; ?>
            
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>
            
            <?php if ($article['member_benefits'] && $is_member): ?>
            <div class="member-benefits">
                <h3>會員專屬福利</h3>
                <div><?php echo nl2br(htmlspecialchars($article['member_benefits'])); ?></div>
                <p style="margin-top: 1rem; font-weight: bold;">您的會員等級：<?php 
                    switch($membership_level) {
                        case 'premium': echo '黃金會員'; break;
                        case 'vip': echo '鑽石會員'; break;
                        default: echo '免費會員';
                    }
                ?></p>
            </div>
            <?php elseif ($article['member_benefits'] && !$is_member): ?>
            <div class="login-prompt">
                <h3>會員專屬內容</h3>
                <p>此文章包含會員專屬福利資訊，登入後即可查看。</p>
                <a href="login.php" class="btn">會員登入</a>
                <a href="register.php" class="btn btn-secondary" style="margin-left: 1rem;">會員註冊</a>
            </div>
            <?php endif; ?>
            
            <?php if ($article['affiliate_links']): ?>
            <div class="affiliate-section">
                <h3>相關推薦</h3>
                <p>以下為精心挑選的相關產品，購買時請注意個人需求並諮詢專業意見。</p>
                
                <div class="affiliate-links">
                    <?php
                    $affiliate_links = explode("
", $article['affiliate_links']);
                    foreach ($affiliate_links as $index => $link) {
                        $link_parts = explode("|", $link);
                        if (count($link_parts) >= 3) {
                            $url = trim($link_parts[0]);
                            $name = trim($link_parts[1]);
                            $commission = trim($link_parts[2]);
                            ?>
                            <div class="affiliate-link">
                                <h4><?php echo htmlspecialchars($name); ?></h4>
                                <p>分潤比例：<span class="commission"><?php echo htmlspecialchars($commission); ?></span></p>
                                <a href="article.php?id=<?php echo $article['id']; ?>&affiliate=<?php echo $index; ?>" 
                                   class="btn btn-success" 
                                   target="_blank">查看產品</a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                
                <p style="margin-top: 1rem; font-size: 0.8rem; color: #6c757d;">
                    * 透過連結購買產品，NmNiHealth將獲得少量分潤，這有助於我們持續提供高品質的健康資訊。
                </p>
            </div>
            <?php endif; ?>
            
            <div class="share-section">
                <h3>分享文章</h3>
                <p>覺得這篇文章有幫助嗎？分享給更多需要的人吧！</p>
                <div class="share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                       class="share-button share-facebook" target="_blank">f</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>" 
                       class="share-button share-twitter" target="_blank">t</a>
                    <a href="https://line.me/R/msg/text/?<?php echo urlencode($article['title'] . ' ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                       class="share-button share-line" target="_blank">L</a>
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