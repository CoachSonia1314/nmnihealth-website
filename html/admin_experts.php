<?php
// 检查用户是否已登录且为管理员
session_start();

// 在实际应用中，您需要实现管理员权限验证
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

// 处理专家表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    try {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (isset($_POST['id']) && $_POST['id']) {
            // 更新专家
            $sql = "UPDATE experts SET name = ?, specialty = ?, description = ? WHERE id = ?";
            $params = [$_POST['name'], $_POST['specialty'], $_POST['description'], $_POST['id']];
        } else {
            // 创建新专家
            $sql = "INSERT INTO experts (name, specialty, description) VALUES (?, ?, ?)";
            $params = [$_POST['name'], $_POST['specialty'], $_POST['description']];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // 重定向到专家列表页面
        header("Location: admin_experts.php?message=專家信息已保存");
        exit();
    } catch (Exception $e) {
        $error = "保存專家信息失敗: " . $e->getMessage();
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
        
        $stmt = $pdo->prepare("DELETE FROM experts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: admin_experts.php?message=專家已刪除");
        exit();
    } catch (Exception $e) {
        header("Location: admin_experts.php?error=刪除失敗: " . $e->getMessage());
        exit();
    }
}

// 获取所有专家
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT * FROM experts ORDER BY name");
    $experts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "數據庫連接失敗: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>專家管理 - NmNiHealth後台</title>
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
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .actions a {
            text-decoration: none;
        }
        
        .expert-form {
            max-width: 800px;
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
            min-height: 100px;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
    </style>
</head>
<body>
    <header>
        <h1>專家管理</h1>
        <p>管理預約專家信息</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">返回首頁</a></li>
            <li><a href="admin.php">後台首頁</a></li>
            <li><a href="admin_articles.php">文章管理</a></li>
            <li><a href="admin_experts.php">專家管理</a></li>
            <li><a href="admin_appointments.php">預約管理</a></li>
            <li><a href="admin_users.php">會員管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="admin-container">
            <div class="admin-header">
                <h1>專家列表</h1>
                <a href="admin_experts.php?action=create" class="btn">新增專家</a>
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
            
            <div class="filter-section">
                <h3>篩選條件</h3>
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <label for="search">搜尋專家</label>
                        <input type="text" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="姓名或專長">
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn">搜尋</button>
                        <a href="admin_experts.php" class="btn btn-secondary">重置</a>
                    </div>
                </form>
            </div>
            
            <?php if (isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'edit')): ?>
                <?php 
                $expert = null;
                if ($_GET['action'] === 'edit' && isset($_GET['id'])) {
                    foreach ($experts as $e) {
                        if ($e['id'] == $_GET['id']) {
                            $expert = $e;
                            break;
                        }
                    }
                }
                ?>
                <form method="POST" class="expert-form">
                    <?php if ($expert): ?>
                    <input type="hidden" name="id" value="<?php echo $expert['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">專家姓名 *</label>
                        <input type="text" id="name" name="name" value="<?php echo $expert ? htmlspecialchars($expert['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="specialty">專長領域 *</label>
                        <input type="text" id="specialty" name="specialty" value="<?php echo $expert ? htmlspecialchars($expert['specialty']) : ''; ?>" required>
                    </div>
                    
                    
                    
                    <div class="form-group">
                        <label for="description">專家簡介</label>
                        <textarea id="description" name="description" placeholder="請輸入專家簡介..."><?php echo $expert ? htmlspecialchars($expert['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">保存專家信息</button>
                        <a href="admin_experts.php" class="btn btn-secondary">取消</a>
                    </div>
                </form>
            <?php else: ?>
                <?php if (empty($experts)): ?>
                <p>目前沒有專家，<a href="admin_experts.php?action=create">立即添加第一位專家</a>。</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>姓名</th>
                            <th>專長領域</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                                                <tr>
                            <td><strong>張美華醫師</strong></td>
                            <td>婦女健康</td>
                            <td class="actions">
                                <a href="admin_experts.php?action=edit&id=1" class="btn btn-secondary">編輯</a>
                                <a href="admin_experts.php?action=delete&id=1" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('確定要刪除這位專家嗎？')">刪除</a>
                            </td>
                        </tr>
                                                <tr>
                            <td><strong>李雅文營養師</strong></td>
                            <td>營養諮詢</td>
                            <td class="actions">
                                <a href="admin_experts.php?action=edit&id=2" class="btn btn-secondary">編輯</a>
                                <a href="admin_experts.php?action=delete&id=2" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('確定要刪除這位專家嗎？')">刪除</a>
                            </td>
                        </tr>
                                                <tr>
                            <td><strong>王心怡諮商師</strong></td>
                            <td>心理諮商</td>
                            <td class="actions">
                                <a href="admin_experts.php?action=edit&id=3" class="btn btn-secondary">編輯</a>
                                <a href="admin_experts.php?action=delete&id=3" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('確定要刪除這位專家嗎？')">刪除</a>
                            </td>
                        </tr>
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
</body>
</html>