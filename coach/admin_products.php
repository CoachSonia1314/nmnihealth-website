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

// 处理产品删除
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $message = "产品删除成功！";
    } catch(PDOException $e) {
        $error = "删除失败: " . $e->getMessage();
    }
}

// 处理产品添加/编辑表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $membership_level = $_POST['membership_level'];
    $product_id = $_POST['product_id'] ?? null;
    
    if (!empty($name) && !empty($price) && !empty($membership_level)) {
        try {
            if ($product_id) {
                // 更新产品
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, membership_level = ? WHERE id = ?");
                $stmt->execute([$name, $description, $price, $membership_level, $product_id]);
                $message = "产品更新成功！";
            } else {
                // 添加新产品
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, membership_level) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $membership_level]);
                $message = "产品添加成功！";
            }
        } catch(PDOException $e) {
            $error = "操作失败: " . $e->getMessage();
        }
    } else {
        $error = "请填写所有必填字段";
    }
}

// 获取所有产品
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY membership_level, name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "获取产品列表失败: " . $e->getMessage();
    $products = [];
}

// 如果是编辑模式，获取产品信息
$editing_product = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $editing_product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "获取产品信息失败: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>產品管理 - NmNiHealth</title>
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
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
        }
        
        .products-table th,
        .products-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .products-table th {
            background-color: #8A2BE2;
            color: white;
        }
        
        .products-table tr:hover {
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
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
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
    </style>
</head>
<body>
    <header>
        <h1>產品管理</h1>
        <p>管理網站上的產品資訊</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="admin.php">管理後台</a></li>
            <li><a href="admin_users.php">會員管理</a></li>
            <li><a href="admin_products.php">產品管理</a></li>
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
                <h2><?php echo $editing_product ? '編輯產品' : '新增產品'; ?></h2>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $editing_product ? $editing_product['id'] : ''; ?>">
                    
                    <div class="form-group">
                        <label for="name">產品名稱 *</label>
                        <input type="text" id="name" name="name" value="<?php echo $editing_product ? htmlspecialchars($editing_product['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">產品描述</label>
                        <textarea id="description" name="description" rows="3"><?php echo $editing_product ? htmlspecialchars($editing_product['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">價格 (NT$) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $editing_product ? $editing_product['price'] : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="membership_level">會員等級 *</label>
                        <select id="membership_level" name="membership_level" required>
                            <option value="free" <?php echo ($editing_product && $editing_product['membership_level'] === 'free') ? 'selected' : ''; ?>>免費會員可購</option>
                            <option value="premium" <?php echo ($editing_product && $editing_product['membership_level'] === 'premium') ? 'selected' : ''; ?>>黃金會員專享</option>
                            <option value="vip" <?php echo ($editing_product && $editing_product['membership_level'] === 'vip') ? 'selected' : ''; ?>>鑽石會員專享</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success"><?php echo $editing_product ? '更新產品' : '新增產品'; ?></button>
                    <?php if ($editing_product): ?>
                    <a href="admin_products.php" class="btn btn-secondary">取消編輯</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="form-section">
                <h2>產品列表</h2>
                <?php if (empty($products)): ?>
                    <p>暫無產品資料</p>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>產品名稱</th>
                                <th>描述</th>
                                <th>價格</th>
                                <th>會員等級</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . (strlen($product['description']) > 50 ? '...' : ''); ?></td>
                                <td>NT$ <?php echo number_format($product['price']); ?></td>
                                <td>
                                    <span class="membership-level level-<?php echo $product['membership_level']; ?>">
                                        <?php 
                                        $level_names = [
                                            'free' => '免費會員可購',
                                            'premium' => '黃金會員專享',
                                            'vip' => '鑽石會員專享'
                                        ];
                                        echo $level_names[$product['membership_level']];
                                        ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="admin_products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn">編輯</a>
                                    <a href="admin_products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('確定要刪除這個產品嗎？')">刪除</a>
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