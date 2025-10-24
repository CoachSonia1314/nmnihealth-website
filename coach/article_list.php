<?php
require_once '/var/www/www.nmnihealth.com/html/includes/article_functions.php';

// 獲取所有文章
$articles = getArticles();

// 處理刪除請求
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        deleteArticle($_GET['id']);
        header("Location: article_list.php?message=文章已刪除");
        exit();
    } catch (Exception $e) {
        header("Location: article_list.php?error=刪除失敗: " . $e->getMessage());
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .article-list {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            color: #8A2BE2;
            margin: 0;
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
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            color: #8A2BE2;
            font-weight: bold;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.875rem;
            font-weight: bold;
        }
        
        .status.draft {
            background: #fff3cd;
            color: #856404;
        }
        
        .status.pending {
            background: #cce7ff;
            color: #004085;
        }
        
        .status.published {
            background: #d4edda;
            color: #155724;
        }
        
        .status.archived {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .actions a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>文章管理</h1>
        <p>管理您的更年期健康保健文章</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="article_list.php">文章管理</a></li>
            <li><a href="appointment.php">預約管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="article-list">
            <div class="page-header">
                <h1>文章列表</h1>
                <a href="article_edit.php" class="btn">新增文章</a>
            </div>
            
            <?php if (isset($_GET['message'])): ?>
            <div class="message success">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="message error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
            <?php endif; ?>
            
            <?php if (empty($articles)): ?>
            <p>目前沒有文章，<a href="article_edit.php">立即創建第一篇文章</a>。</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>標題</th>
                        <th>分類</th>
                        <th>狀態</th>
                        <th>創建時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($article['title']); ?></strong>
                            <?php if ($article['featured_image']): ?>
                            <br><small>包含特色圖片</small>
                            <?php endif; ?>
                            <?php if ($article['affiliate_links']): ?>
                            <br><small style="color: #28a745;">✓ 聯盟行銷連結</small>
                            <?php endif; ?>
                            <?php if ($article['member_benefits']): ?>
                            <br><small style="color: #007bff;">✓ 會員專屬福利</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($article['category_name']); ?></td>
                        <td>
                            <span class="status <?php echo $article['status']; ?>">
                                <?php 
                                switch($article['status']) {
                                    case 'draft': echo '草稿'; break;
                                    case 'pending': echo '待審核'; break;
                                    case 'published': echo '已發布'; break;
                                    case 'archived': echo '已封存'; break;
                                }
                                ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></td>
                        <td class="actions">
                            <a href="article_edit.php?id=<?php echo $article['id']; ?>" class="btn btn-secondary">編輯</a>
                            <a href="article_list.php?action=delete&id=<?php echo $article['id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('確定要刪除這篇文章嗎？')">刪除</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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