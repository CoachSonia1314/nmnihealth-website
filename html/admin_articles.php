<?php
session_start();

// 检查用户是否已登录且为管理员
// 在实际应用中，您需要实现管理员权限验证
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['featured_image'])) {
    $uploadDir = '/var/www/www.nmnihealth.com/html/images/';
    
    // 确保上传目录存在
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = $_FILES['featured_image']['name'];
    $fileTmpName = $_FILES['featured_image']['tmp_name'];
    $fileSize = $_FILES['featured_image']['size'];
    $fileError = $_FILES['featured_image']['error'];
    
    // 检查是否有上传错误
    if ($fileError === UPLOAD_ERR_OK) {
        // 生成唯一的文件名
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid('article_') . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $uniqueFileName;
        
        // 检查文件类型
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileExtension), $allowedTypes)) {
            // 检查文件大小（限制为10MB）
            if ($fileSize <= 10 * 1024 * 1024) {
                // 移动文件到上传目录
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    // 返回图片URL
                    $imageUrl = 'https://www.nmnihealth.com/images/' . $uniqueFileName;
                    echo json_encode(['success' => true, 'url' => $imageUrl]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'error' => '文件移動失敗']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'error' => '文件大小超過限制（10MB）']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'error' => '不支持的文件類型']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'error' => '上傳錯誤: ' . $fileError]);
        exit();
    }
}

// 处理文章表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    try {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->beginTransaction();
        
        // 生成slug
        $title = $_POST['title'];
        $slug = preg_replace('~[^\pL\d]+~u', '-', $title);
        $slug = strtolower($slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = trim($slug, '-');
        $slug = preg_replace('~-+~', '-', $slug);
        
        // 检查slug是否已存在，如果存在则添加时间戳
        $stmt = $pdo->prepare("SELECT id FROM articles WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $_POST['id'] ?? 0]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        if (isset($_POST['id']) && $_POST['id']) {
            // 更新文章
            $sql = "UPDATE articles SET 
                    title = ?, slug = ?, content = ?, excerpt = ?, keywords = ?, 
                    description = ?, featured_image = ?, status = ?, category_id = ?,
                    published_at = ?
                    WHERE id = ?";
            $params = [
                $_POST['title'], $slug, $_POST['content'], $_POST['excerpt'],
                $_POST['keywords'], $_POST['description'], $_POST['featured_image'],
                $_POST['status'], $_POST['category_id'],
                $_POST['status'] === 'published' ? ($_POST['published_at'] ?? date('Y-m-d H:i:s')) : null,
                $_POST['id']
            ];
        } else {
            // 创建新文章
            $sql = "INSERT INTO articles (title, slug, content, excerpt, keywords, description, 
                    featured_image, status, category_id, published_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $_POST['title'], $slug, $_POST['content'], $_POST['excerpt'],
                $_POST['keywords'], $_POST['description'], $_POST['featured_image'],
                $_POST['status'], $_POST['category_id'],
                $_POST['status'] === 'published' ? date('Y-m-d H:i:s') : null
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $pdo->commit();
        
        // 重定向到文章列表页面
        header("Location: admin_articles.php?message=文章已保存");
        exit();
    } catch (Exception $e) {
        $error = "保存文章失敗: " . $e->getMessage();
    }
}

// 处理删除请求
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: admin_articles.php?message=文章已刪除");
        exit();
    } catch (Exception $e) {
        header("Location: admin_articles.php?error=刪除失敗: " . $e->getMessage());
        exit();
    }
}

// 获取所有文章
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 获取所有分类
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "數據庫連接失敗: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - NmNiHealth後台</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
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
        
        .article-form {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #8A2BE2;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Noto Sans TC', sans-serif;
            font-size: 1rem;
        }
        
        textarea {
            min-height: 150px;
        }
        
        .seo-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin: 2rem 0;
        }
        
        .seo-section h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .upload-section {
            border: 2px dashed #ddd;
            padding: 2rem;
            text-align: center;
            margin: 1rem 0;
            border-radius: 5px;
            background: #f8f9fa;
        }
        
        .upload-section:hover {
            border-color: #8A2BE2;
        }
        
        .image-preview {
            max-width: 300px;
            margin-top: 1rem;
        }
        
        .image-preview img {
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>文章管理</h1>
        <p>管理您的網站文章內容</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">返回首頁</a></li>
            <li><a href="admin.php">後台首頁</a></li>
            <li><a href="admin_articles.php">文章管理</a></li>
            <li><a href="admin_experts.php">專家管理</a></li>
            <li><a href="admin_appointments.php">預約管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="admin-container">
            <div class="admin-header">
                <h1>文章列表</h1>
                <a href="admin_articles.php?action=create" class="btn">新增文章</a>
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
            
            <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'edit')): ?>
                <?php 
                $article = null;
                if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
                    foreach ($articles as $a) {
                        if ($a['id'] == $_GET['id']) {
                            $article = $a;
                            break;
                        }
                    }
                }
                ?>
                <form method="POST" class="article-form" id="articleForm" enctype="multipart/form-data">
                    <?php if ($article): ?>
                    <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="title">文章標題 *</label>
                        <input type="text" id="title" name="title" value="<?php echo $article ? htmlspecialchars($article['title']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">文章分類 *</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">請選擇分類</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($article && $article['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">文章摘要</label>
                        <textarea id="excerpt" name="excerpt" placeholder="簡短描述文章內容..."><?php echo $article ? htmlspecialchars($article['excerpt']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">文章內容 *</label>
                        <textarea id="content" name="content" required><?php echo $article ? htmlspecialchars($article['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="upload-section" id="uploadSection">
                        <h3>圖片上傳</h3>
                        <p>上傳特色圖片或文章配圖</p>
                        <input type="file" id="imageUpload" name="featured_image" accept="image/*" style="display: none;">
                        <button type="button" class="btn btn-secondary" id="uploadBtn">選擇圖片</button>
                        <div id="uploadStatus"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="featured_image">特色圖片 URL</label>
                        <input type="text" id="featured_image" name="featured_image" value="<?php echo $article ? htmlspecialchars($article['featured_image']) : ''; ?>" placeholder="https://example.com/image.jpg">
                        <div class="image-preview" id="imagePreview">
                            <?php if ($article && $article['featured_image']): ?>
                            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="特色圖片預覽">
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="seo-section">
                        <h3>SEO優化設定</h3>
                        
                        <div class="form-group">
                            <label for="keywords">關鍵字 (Keywords)</label>
                            <input type="text" id="keywords" name="keywords" value="<?php echo $article ? htmlspecialchars($article['keywords']) : ''; ?>" placeholder="輸入關鍵字，用逗號分隔">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">描述 (Description)</label>
                            <textarea id="description" name="description" placeholder="輸入頁面描述，建議不超過160個字符"><?php echo $article ? htmlspecialchars($article['description']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">文章狀態</label>
                        <select id="status" name="status">
                            <option value="draft" <?php echo ($article && $article['status'] == 'draft') ? 'selected' : ''; ?>>草稿</option>
                            <option value="pending" <?php echo ($article && $article['status'] == 'pending') ? 'selected' : ''; ?>>待審核</option>
                            <option value="published" <?php echo ($article && $article['status'] == 'published') ? 'selected' : ''; ?>>已發布</option>
                            <option value="archived" <?php echo ($article && $article['status'] == 'archived') ? 'selected' : ''; ?>>已封存</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">保存文章</button>
                        <a href="admin_articles.php" class="btn btn-secondary">取消</a>
                    </div>
                </form>
            <?php else: ?>
                <?php if (empty($articles)): ?>
                <p>目前沒有文章，<a href="admin_articles.php?action=create">立即創建第一篇文章</a>。</p>
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
                                <a href="admin_articles.php?action=edit&id=<?php echo $article['id']; ?>" class="btn btn-secondary">編輯</a>
                                <a href="admin_articles.php?action=delete&id=<?php echo $article['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('確定要刪除這篇文章嗎？')">刪除</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        // 初始化CKEditor
        if (document.getElementById('content')) {
            CKEDITOR.replace('content', {
                height: 400,
                toolbar: [
                    { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print'] },
                    { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                    { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                    '/',
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                    { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
                    '/',
                    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
                ]
            });
        }
        
        // 圖片上傳功能
        if (document.getElementById('uploadBtn')) {
            document.getElementById('uploadBtn').addEventListener('click', function() {
                document.getElementById('imageUpload').click();
            });
            
            document.getElementById('imageUpload').addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const formData = new FormData();
                    formData.append('featured_image', this.files[0]);
                    
                    const uploadStatus = document.getElementById('uploadStatus');
                    uploadStatus.innerHTML = '上傳中...';
                    
                    fetch('admin_articles.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            uploadStatus.innerHTML = '上傳成功！';
                            // 自動將上傳的圖片設置為配圖
                            document.getElementById('featured_image').value = data.url;
                            // 顯示圖片預覽
                            document.getElementById('imagePreview').innerHTML = '<img src="' + data.url + '" alt="特色圖片預覽">';
                            // 清空文件輸入框
                            document.getElementById('imageUpload').value = '';
                        } else {
                            uploadStatus.innerHTML = '上傳失敗: ' + data.error;
                            console.error('上傳失敗:', data.error);
                        }
                    })
                    .catch(error => {
                        uploadStatus.innerHTML = '上傳失敗: ' + error.message;
                        console.error('上傳錯誤:', error);
                    });
                }
            });
        }
        
        // 圖片預覽功能
        if (document.getElementById('featured_image')) {
            document.getElementById('featured_image').addEventListener('change', function() {
                const imageUrl = this.value;
                const previewContainer = document.getElementById('imagePreview');
                
                if (imageUrl) {
                    previewContainer.innerHTML = '<img src="' + imageUrl + '" alt="特色圖片預覽">';
                } else {
                    previewContainer.innerHTML = '';
                }
            });
        }
    </script>
</body>
</html>