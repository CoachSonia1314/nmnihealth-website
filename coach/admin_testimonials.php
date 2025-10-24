<?php
session_start();

// 检查用户是否已登录且是管理员
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
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

$message = '';
$error = '';

// 处理用户见证删除
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "用户见证删除成功！";
    } catch(PDOException $e) {
        $error = "删除失败: " . $e->getMessage();
    }
}

// 处理用户见证状态更新
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    try {
        // 获取当前状态
        $stmt = $pdo->prepare("SELECT status FROM testimonials WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($testimonial) {
            $new_status = $testimonial['status'] === 'active' ? 'inactive' : 'active';
            $stmt = $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $_GET['id']]);
            $message = "用户见证状态更新成功！";
        }
    } catch(PDOException $e) {
        $error = "状态更新失败: " . $e->getMessage();
    }
}

// 处理用户见证添加/编辑表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $content = $_POST['content'];
    $age = $_POST['age'];
    $membership_level = $_POST['membership_level'];
    $status = $_POST['status'];
    $testimonial_id = $_POST['testimonial_id'] ?? null;
    
    // 处理头像上传
    $avatar = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '/var/www/www.nmnihealth.com/html/images/';
        $file_extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = 'testimonial_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;
        
        // 检查文件类型
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            // 移动上传文件
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                $avatar = 'images/' . $file_name;
            }
        }
    }
    
    if (!empty($name) && !empty($content) && !empty($membership_level) && !empty($status)) {
        try {
            if ($testimonial_id) {
                // 更新用户见证
                if ($avatar) {
                    $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, content = ?, age = ?, membership_level = ?, status = ?, avatar = ? WHERE id = ?");
                    $stmt->execute([$name, $content, $age, $membership_level, $status, $avatar, $testimonial_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, content = ?, age = ?, membership_level = ?, status = ? WHERE id = ?");
                    $stmt->execute([$name, $content, $age, $membership_level, $status, $testimonial_id]);
                }
                $message = "用户见证更新成功！";
            } else {
                // 添加新用户见证
                if ($avatar) {
                    $stmt = $pdo->prepare("INSERT INTO testimonials (name, content, age, membership_level, status, avatar) VALUES (?, ?, ?, ?, ?, ?)");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO testimonials (name, content, age, membership_level, status) VALUES (?, ?, ?, ?, ?)");
                }
                $params = [$name, $content, $age, $membership_level, $status];
                if ($avatar) {
                    $params[] = $avatar;
                }
                $stmt->execute($params);
                $message = "用户见证添加成功！";
            }
        } catch(PDOException $e) {
            $error = "操作失败: " . $e->getMessage();
        }
    } else {
        $error = "请填写所有必填字段";
    }
}

// 获取所有用户见证
try {
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "获取用户见证列表失败: " . $e->getMessage();
    $testimonials = [];
}

// 如果是编辑模式，获取用户见证信息
$editing_testimonial = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $editing_testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "获取用户见证信息失败: " . $e->getMessage();
    }
}

// 会员等级显示文本
$membership_levels = [
    'free' => '免費會員',
    'premium' => '黃金會員',
    'vip' => '鑽石會員'
];
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用戶見證管理 - NmNiHealth</title>
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
            resize: vertical;
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
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .testimonials-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
        }
        
        .testimonials-table th,
        .testimonials-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .testimonials-table th {
            background-color: #8A2BE2;
            color: white;
        }
        
        .testimonials-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .membership-level {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            font-size: 0.8rem;
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
        
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-inactive {
            color: #6c757d;
            font-weight: bold;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .form-section {
            margin: 2rem 0;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .form-section h2 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .content-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .avatar-preview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <header>
        <h1>用戶見證管理</h1>
        <p>管理網站上的用戶見證內容</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="admin.php">管理後台</a></li>
            <li><a href="admin_users.php">會員管理</a></li>
            <li><a href="admin_products.php">產品管理</a></li>
            <li><a href="admin_testimonials.php">用戶見證管理</a></li>
            <li><a href="admin_experts.php">專家管理</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="admin-container">
            <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            
            <div class="form-section">
                <h2><?php echo $editing_testimonial ? '編輯用戶見證' : '新增用戶見證'; ?></h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="testimonial_id" value="<?php echo $editing_testimonial ? $editing_testimonial['id'] : ''; ?>">
                    
                    <div class="form-group">
                        <label for="name">用戶姓名 *</label>
                        <input type="text" id="name" name="name" value="<?php echo $editing_testimonial ? htmlspecialchars($editing_testimonial['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">見證內容 *</label>
                        <textarea id="content" name="content" rows="4" required><?php echo $editing_testimonial ? htmlspecialchars($editing_testimonial['content']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="age">年齡</label>
                        <input type="number" id="age" name="age" min="30" max="80" value="<?php echo $editing_testimonial ? $editing_testimonial['age'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">用戶頭像</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*">
                        <?php if ($editing_testimonial && !empty($editing_testimonial['avatar'])): ?>
                            <div style="margin-top: 10px;">
                                <img src="<?php echo htmlspecialchars($editing_testimonial['avatar']); ?>" alt="當前頭像" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="membership_level">會員等級 *</label>
                        <select id="membership_level" name="membership_level" required>
                            <option value="free" <?php echo ($editing_testimonial && $editing_testimonial['membership_level'] === 'free') ? 'selected' : ''; ?>>免費會員</option>
                            <option value="premium" <?php echo ($editing_testimonial && $editing_testimonial['membership_level'] === 'premium') ? 'selected' : ''; ?>>黃金會員</option>
                            <option value="vip" <?php echo ($editing_testimonial && $editing_testimonial['membership_level'] === 'vip') ? 'selected' : ''; ?>>鑽石會員</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">狀態 *</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo ($editing_testimonial && $editing_testimonial['status'] === 'active') ? 'selected' : ''; ?>>顯示</option>
                            <option value="inactive" <?php echo ($editing_testimonial && $editing_testimonial['status'] === 'inactive') ? 'selected' : ''; ?>>隱藏</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success"><?php echo $editing_testimonial ? '更新見證' : '新增見證'; ?></button>
                    <?php if ($editing_testimonial): ?>
                    <a href="admin_testimonials.php" class="btn btn-secondary">取消編輯</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="form-section">
                <h2>用戶見證列表</h2>
                <?php if (empty($testimonials)): ?>
                    <p>暫無用戶見證資料</p>
                <?php else: ?>
                    <table class="testimonials-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>用戶頭像</th>
                                <th>用戶姓名</th>
                                <th>見證內容</th>
                                <th>年齡</th>
                                <th>會員等級</th>
                                <th>狀態</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td><?php echo $testimonial['id']; ?></td>
                                <td>
                                    <?php if (!empty($testimonial['avatar'])): ?>
                                        <img src="<?php echo htmlspecialchars($testimonial['avatar']); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="avatar-preview">
                                    <?php else: ?>
                                        <img src="images/default_avatar.svg" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="avatar-preview">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($testimonial['name']); ?></td>
                                <td class="content-preview"><?php echo htmlspecialchars($testimonial['content']); ?></td>
                                <td><?php echo $testimonial['age'] ?: '-'; ?></td>
                                <td>
                                    <span class="membership-level level-<?php echo $testimonial['membership_level']; ?>">
                                        <?php echo $membership_levels[$testimonial['membership_level']]; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-<?php echo $testimonial['status']; ?>">
                                        <?php echo $testimonial['status'] === 'active' ? '顯示' : '隱藏'; ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="admin_testimonials.php?action=edit&id=<?php echo $testimonial['id']; ?>" class="btn">編輯</a>
                                    <a href="admin_testimonials.php?action=toggle&id=<?php echo $testimonial['id']; ?>" class="btn <?php echo $testimonial['status'] === 'active' ? 'btn-warning' : 'btn-success'; ?>">
                                        <?php echo $testimonial['status'] === 'active' ? '隱藏' : '顯示'; ?>
                                    </a>
                                    <a href="admin_testimonials.php?action=delete&id=<?php echo $testimonial['id']; ?>" class="btn btn-danger" onclick="return confirm('確定要刪除這個用戶見證嗎？')">刪除</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</body>
</html>