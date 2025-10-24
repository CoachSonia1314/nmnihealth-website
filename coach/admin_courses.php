<?php
// 課程管理介面
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
            // 刪除課程
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$_POST['delete_id']]);
            $message = "課程已刪除";
        } else if (isset($_POST['add_module']) && isset($_POST['course_id'])) {
            // 新增課程模組
            $stmt = $pdo->prepare("INSERT INTO course_modules (course_id, title, description, module_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['course_id'], $_POST['module_title'], $_POST['module_description'], $_POST['module_order']]);
            $message = "課程模組已新增";
        } else {
            // 新增或更新課程
            if (isset($_POST['id']) && $_POST['id']) {
                // 更新課程
                $sql = "UPDATE courses SET title = ?, description = ?, price = ?, duration = ?, level = ?, status = ?, image_url = ? WHERE id = ?";
                $params = [$_POST['title'], $_POST['description'], $_POST['price'], $_POST['duration'], $_POST['level'], $_POST['status'], $_POST['image_url'], $_POST['id']];
            } else {
                // 新增課程
                $sql = "INSERT INTO courses (title, description, price, duration, level, status, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $params = [$_POST['title'], $_POST['description'], $_POST['price'], $_POST['duration'], $_POST['level'], $_POST['status'], $_POST['image_url']];
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = "課程信息已保存";
        }
    }
    
    // 獲取所有課程
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY title");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 獲取所有課程模組
    $stmt = $pdo->query("SELECT * FROM course_modules ORDER BY course_id, module_order");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 將模組按課程分組
    $modulesByCourse = [];
    foreach ($modules as $module) {
        $modulesByCourse[$module['course_id']][] = $module;
    }
    
} catch(PDOException $e) {
    $error = "資料庫錯誤: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>課程管理 - NmNiHealth後台</title>
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
        
        .course-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .course-card h3 {
            color: #8A2BE2;
            margin-top: 0;
        }
        
        .module-list {
            margin-top: 1rem;
            padding-left: 1rem;
        }
        
        .module-item {
            background: #e9ecef;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 5px;
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
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .module-form {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>課程管理</h1>
            <p>管理您的課程內容</p>
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
            <h2>新增/編輯課程</h2>
            <form method="POST" action="admin_courses.php">
                <input type="hidden" id="id" name="id">
                <div class="form-group">
                    <label for="title">課程標題</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">課程描述</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">價格</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="duration">課程時長</label>
                    <input type="text" id="duration" name="duration" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="level">難度等級</label>
                    <select id="level" name="level" class="form-control" required>
                        <option value="beginner">初級</option>
                        <option value="intermediate">中級</option>
                        <option value="advanced">高級</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="status">狀態</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="draft">草稿</option>
                        <option value="published">已發布</option>
                        <option value="archived">已封存</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image_url">圖片URL</label>
                    <input type="text" id="image_url" name="image_url" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-success">保存課程</button>
                <button type="button" class="btn" onclick="clearForm()">清除</button>
            </form>
            
            <h2 style="margin-top: 2rem;">課程列表</h2>
            <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><strong>描述:</strong> <?php echo htmlspecialchars($course['description']); ?></p>
                    <p><strong>價格:</strong> NT$ <?php echo number_format($course['price'], 2); ?></p>
                    <p><strong>時長:</strong> <?php echo htmlspecialchars($course['duration']); ?></p>
                    <p><strong>難度:</strong> <?php echo ucfirst($course['level']); ?></p>
                    <p><strong>狀態:</strong> <?php echo ucfirst($course['status']); ?></p>
                    <p><strong>圖片:</strong> <?php echo htmlspecialchars($course['image_url']); ?></p>
                    <p><strong>創建時間:</strong> <?php echo $course['created_at']; ?></p>
                    <button class="btn" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo addslashes($course['title']); ?>', '<?php echo addslashes($course['description']); ?>', <?php echo $course['price']; ?>, '<?php echo addslashes($course['duration']); ?>', '<?php echo $course['level']; ?>', '<?php echo $course['status']; ?>', '<?php echo addslashes($course['image_url']); ?>')">編輯</button>
                    <form method="POST" action="admin_courses.php" style="display: inline;">
                        <input type="hidden" name="delete_id" value="<?php echo $course['id']; ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('確定要刪除這個課程嗎？')">刪除</button>
                    </form>
                    
                    <h4 style="margin-top: 1rem;">課程模組</h4>
                    <?php if (isset($modulesByCourse[$course['id']])): ?>
                        <div class="module-list">
                            <?php foreach ($modulesByCourse[$course['id']] as $module): ?>
                                <div class="module-item">
                                    <strong><?php echo htmlspecialchars($module['title']); ?></strong> (順序: <?php echo $module['module_order']; ?>)
                                    <p><?php echo htmlspecialchars($module['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>尚無模組</p>
                    <?php endif; ?>
                    
                    <button class="btn btn-secondary" onclick="showModuleForm(<?php echo $course['id']; ?>)">新增模組</button>
                    
                    <div id="moduleForm<?php echo $course['id']; ?>" class="module-form" style="display: none;">
                        <h4>新增模組</h4>
                        <form method="POST" action="admin_courses.php">
                            <input type="hidden" name="add_module" value="1">
                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                            <div class="form-group">
                                <label for="module_title<?php echo $course['id']; ?>">模組標題</label>
                                <input type="text" id="module_title<?php echo $course['id']; ?>" name="module_title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="module_description<?php echo $course['id']; ?>">模組描述</label>
                                <textarea id="module_description<?php echo $course['id']; ?>" name="module_description" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="module_order<?php echo $course['id']; ?>">模組順序</label>
                                <input type="number" id="module_order<?php echo $course['id']; ?>" name="module_order" class="form-control" value="1" required>
                            </div>
                            <button type="submit" class="btn btn-success">保存模組</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        function editCourse(id, title, description, price, duration, level, status, imageUrl) {
            document.getElementById('id').value = id;
            document.getElementById('title').value = title;
            document.getElementById('description').value = description;
            document.getElementById('price').value = price;
            document.getElementById('duration').value = duration;
            document.getElementById('level').value = level;
            document.getElementById('status').value = status;
            document.getElementById('image_url').value = imageUrl;
        }
        
        function clearForm() {
            document.getElementById('id').value = '';
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('price').value = '';
            document.getElementById('duration').value = '';
            document.getElementById('level').value = 'beginner';
            document.getElementById('status').value = 'draft';
            document.getElementById('image_url').value = '';
        }
        
        function showModuleForm(courseId) {
            var form = document.getElementById('moduleForm' + courseId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>