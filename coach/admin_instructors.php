<?php
// 講師團隊管理介面
session_start();

// 檢查用戶是否已登錄且為管理員
// 在實際應用中，您需要實現管理員權限驗證
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

// 資料庫連接設定
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    // 建立PDO連接
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 處理表單提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete_id'])) {
            // 刪除講師
            $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
            $stmt->execute([$_POST['delete_id']]);
            $message = "講師已刪除";
        } else {
            // 新增或更新講師
            if (isset($_POST['id']) && $_POST['id']) {
                // 更新講師
                $sql = "UPDATE instructors SET name = ?, title = ?, bio = ?, expertise = ?, image_url = ? WHERE id = ?";
                $params = [$_POST['name'], $_POST['title'], $_POST['bio'], $_POST['expertise'], $_POST['image_url'], $_POST['id']];
            } else {
                // 新增講師
                $sql = "INSERT INTO instructors (name, title, bio, expertise, image_url) VALUES (?, ?, ?, ?, ?)";
                $params = [$_POST['name'], $_POST['title'], $_POST['bio'], $_POST['expertise'], $_POST['image_url']];
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = "講師信息已保存";
        }
    }
    
    // 獲取所有講師
    $stmt = $pdo->query("SELECT * FROM instructors ORDER BY name");
    $instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "資料庫錯誤: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>講師團隊管理 - NmNiHealth後台</title>
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
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-header h1 {
            color: #8A2BE2;
            margin-bottom: 1rem;
        }
        
        .admin-nav {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .admin-nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 0;
            padding: 0;
            gap: 1rem;
        }
        
        .admin-nav li {
            margin: 0;
        }
        
        .admin-nav a {
            display: block;
            padding: 0.75rem 1.5rem;
            background: #8A2BE2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover {
            background: #7A1BD2;
        }
        
        .admin-content {
            margin-top: 2rem;
        }
        
        .instructor-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .instructor-card h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            background: #8A2BE2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #7A1BD2;
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
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>講師團隊管理</h1>
            <p>管理您的講師團隊成員</p>
        </div>
        
        <div class="admin-nav">
            <ul>
                <li><a href="admin.php">儀表板</a></li>
                <li><a href="admin_experts.php">專家管理</a></li>
                <li><a href="admin_instructors.php">講師管理</a></li>
                <li><a href="admin_courses.php">課程管理</a></li>
            </ul>
        </div>
        
        <?php if (isset($message)): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-content">
            <h2>新增/編輯講師</h2>
            <form method="POST" action="admin_instructors.php">
                <input type="hidden" id="id" name="id">
                <div class="form-group">
                    <label for="name">姓名</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="title">職稱</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="bio">簡介</label>
                    <textarea id="bio" name="bio" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="expertise">專長</label>
                    <input type="text" id="expertise" name="expertise" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="image_url">圖片URL</label>
                    <input type="text" id="image_url" name="image_url" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-success">保存講師</button>
                <button type="button" class="btn" onclick="clearForm()">清除</button>
            </form>
            
            <h2 style="margin-top: 2rem;">講師列表</h2>
            <?php foreach ($instructors as $instructor): ?>
                <div class="instructor-card">
                    <h3><?php echo htmlspecialchars($instructor['name']); ?></h3>
                    <p><strong>職稱:</strong> <?php echo htmlspecialchars($instructor['title']); ?></p>
                    <p><strong>簡介:</strong> <?php echo htmlspecialchars($instructor['bio']); ?></p>
                    <p><strong>專長:</strong> <?php echo htmlspecialchars($instructor['expertise']); ?></p>
                    <p><strong>圖片:</strong> <?php echo htmlspecialchars($instructor['image_url']); ?></p>
                    <p><strong>創建時間:</strong> <?php echo $instructor['created_at']; ?></p>
                    <button class="btn" onclick="editInstructor(<?php echo $instructor['id']; ?>, '<?php echo addslashes($instructor['name']); ?>', '<?php echo addslashes($instructor['title']); ?>', '<?php echo addslashes($instructor['bio']); ?>', '<?php echo addslashes($instructor['expertise']); ?>', '<?php echo addslashes($instructor['image_url']); ?>')">編輯</button>
                    <form method="POST" action="admin_instructors.php" style="display: inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $instructor['id']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('確定要刪除這個講師嗎？')">刪除</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function editInstructor(id, name, title, bio, expertise, imageUrl) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('title').value = title;
            document.getElementById('bio').value = bio;
            document.getElementById('expertise').value = expertise;
            document.getElementById('image_url').value = imageUrl;
        }
        
        function clearForm() {
            document.getElementById('id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('title').value = '';
            document.getElementById('bio').value = '';
            document.getElementById('expertise').value = '';
            document.getElementById('image_url').value = '';
        }
    </script>
</body>
</html>