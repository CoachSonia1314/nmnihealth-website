<?php
session_start();

// 检查用户是否已登录且为管理员
// 在实际应用中，您需要实现管理员权限验证
// if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

// 处理预约状态更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'])) {
    try {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password = "health2025";
        $dbname = "nmnihealth";
        
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 更新预约状态
        $sql = "UPDATE appointments SET status = ?, notes = ? WHERE id = ?";
        $params = [$_POST['status'], $_POST['admin_notes'], $_POST['appointment_id']];
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // 重定向到预约列表页面
        header("Location: admin_appointments.php?message=預約狀態已更新");
        exit();
    } catch (Exception $e) {
        $error = "更新預約狀態失敗: " . $e->getMessage();
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
        
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        
        header("Location: admin_appointments.php?message=預約記錄已刪除");
        exit();
    } catch (Exception $e) {
        header("Location: admin_appointments.php?error=刪除失敗: " . $e->getMessage());
        exit();
    }
}

// 获取所有预约记录
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT a.*, u.name as user_name, u.email, u.phone, e.name as expert_name, e.specialty 
            FROM appointments a 
            JOIN users u ON a.user_id = u.id 
            JOIN experts e ON a.expert_id = e.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = $pdo->query($sql);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "數據庫連接失敗: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預約管理 - NmNiHealth後台</title>
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
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
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
        
        .status.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status.confirmed {
            background: #cce7ff;
            color: #004085;
        }
        
        .status.completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .actions a {
            text-decoration: none;
        }
        
        .appointment-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .detail-item {
            margin-bottom: 0.75rem;
        }
        
        .detail-label {
            font-weight: bold;
            color: #8A2BE2;
            display: inline-block;
            width: 120px;
        }
        
        .appointment-form {
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
        <h1>預約管理</h1>
        <p>查看和管理會員預約記錄</p>
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
                <h1>預約記錄列表</h1>
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
                        <label for="status">預約狀態</label>
                        <select id="status" name="status">
                            <option value="">所有狀態</option>
                            <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>待確認</option>
                            <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : ''; ?>>已確認</option>
                            <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : ''; ?>>已完成</option>
                            <option value="cancelled" <?php echo (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : ''; ?>>已取消</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="expert">預約專家</label>
                        <select id="expert" name="expert">
                            <option value="">所有專家</option>
                            <?php 
                            // 获取所有专家用于筛选
                            try {
                                $stmt = $pdo->query("SELECT id, name FROM experts ORDER BY name");
                                $experts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($experts as $expert) {
                                    $selected = (isset($_GET['expert']) && $_GET['expert'] == $expert['id']) ? 'selected' : '';
                                    echo '<option value="' . $expert['id'] . '" ' . $selected . '>' . htmlspecialchars($expert['name']) . '</option>';
                                }
                            } catch(PDOException $e) {
                                // 忽略错误
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn">篩選</button>
                        <a href="admin_appointments.php" class="btn btn-secondary">重置</a>
                    </div>
                </form>
            </div>
            
            <?php if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])): ?>
                <?php 
                $appointment = null;
                foreach ($appointments as $a) {
                    if ($a['id'] == $_GET['id']) {
                        $appointment = $a;
                        break;
                    }
                }
                
                if ($appointment):
                ?>
                <div class="appointment-form">
                    <h2>預約詳細信息</h2>
                    
                    <div class="appointment-details">
                        <div class="detail-item">
                            <span class="detail-label">預約編號：</span>
                            <?php echo $appointment['id']; ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">預約專家：</span>
                            <?php echo htmlspecialchars($appointment['expert_name']); ?>（<?php echo htmlspecialchars($appointment['specialty']); ?>）
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">預約日期：</span>
                            <?php echo $appointment['appointment_date']; ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">預約時間：</span>
                            <?php echo substr($appointment['appointment_time'], 0, 5); ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">會員姓名：</span>
                            <?php echo htmlspecialchars($appointment['user_name']); ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">聯絡電話：</span>
                            <?php echo htmlspecialchars($appointment['phone']); ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">電子郵件：</span>
                            <?php echo htmlspecialchars($appointment['email']); ?>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">預約狀態：</span>
                            <span class="status <?php echo $appointment['status']; ?>">
                                <?php 
                                switch($appointment['status']) {
                                    case 'pending': echo '待確認'; break;
                                    case 'confirmed': echo '已確認'; break;
                                    case 'completed': echo '已完成'; break;
                                    case 'cancelled': echo '已取消'; break;
                                }
                                ?>
                            </span>
                        </div>
                        <?php if ($appointment['notes']): ?>
                        <div class="detail-item">
                            <span class="detail-label">會員備註：</span>
                            <?php echo htmlspecialchars($appointment['notes']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                        
                        <div class="form-group">
                            <label for="status">更新預約狀態</label>
                            <select id="status" name="status">
                                <option value="pending" <?php echo ($appointment['status'] === 'pending') ? 'selected' : ''; ?>>待確認</option>
                                <option value="confirmed" <?php echo ($appointment['status'] === 'confirmed') ? 'selected' : ''; ?>>已確認</option>
                                <option value="completed" <?php echo ($appointment['status'] === 'completed') ? 'selected' : ''; ?>>已完成</option>
                                <option value="cancelled" <?php echo ($appointment['status'] === 'cancelled') ? 'selected' : ''; ?>>已取消</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_notes">管理員備註</label>
                            <textarea id="admin_notes" name="admin_notes" placeholder="請輸入管理員備註..."><?php echo htmlspecialchars($appointment['admin_notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn">更新狀態</button>
                            <a href="admin_appointments.php" class="btn btn-secondary">返回列表</a>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <p>找不到指定的預約記錄。<a href="admin_appointments.php">返回列表</a></p>
                <?php endif; ?>
            <?php else: ?>
                <?php if (empty($appointments)): ?>
                <p>目前沒有預約記錄。</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>預約編號</th>
                            <th>會員姓名</th>
                            <th>預約專家</th>
                            <th>預約日期</th>
                            <th>預約時間</th>
                            <th>狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo $appointment['id']; ?></td>
                            <td><?php echo htmlspecialchars($appointment['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['expert_name']); ?></td>
                            <td><?php echo $appointment['appointment_date']; ?></td>
                            <td><?php echo substr($appointment['appointment_time'], 0, 5); ?></td>
                            <td>
                                <span class="status <?php echo $appointment['status']; ?>">
                                    <?php 
                                    switch($appointment['status']) {
                                        case 'pending': echo '待確認'; break;
                                        case 'confirmed': echo '已確認'; break;
                                        case 'completed': echo '已完成'; break;
                                        case 'cancelled': echo '已取消'; break;
                                    }
                                    ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="admin_appointments.php?action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-secondary">查看</a>
                                <a href="admin_appointments.php?action=delete&id=<?php echo $appointment['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('確定要刪除這筆預約記錄嗎？')">刪除</a>
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
</body>
</html>