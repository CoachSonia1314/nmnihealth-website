<?php
session_start();

// 检查管理员权限
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
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
    
    // 获取预约统计
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM coach_bookings");
    $total_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM coach_bookings WHERE status = 'pending'");
    $pending_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as confirmed FROM coach_bookings WHERE status = 'confirmed'");
    $confirmed_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['confirmed'];
    
    // 获取最新预约
    $stmt = $pdo->query("SELECT * FROM coach_bookings ORDER BY created_at DESC LIMIT 10");
    $recent_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "資料庫連接失敗: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教練管理後台 - Coach NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .admin-header h1 {
            color: #667eea;
            margin: 0;
        }
        
        .admin-nav {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .admin-nav a {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .bookings-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .bookings-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bookings-table th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
        }
        
        .bookings-table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .bookings-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .status-pending {
            background: #ffc107;
            color: #333;
        }
        
        .status-confirmed {
            background: #28a745;
            color: white;
        }
        
        .status-cancelled {
            background: #dc3545;
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-action {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        
        .btn-cancel {
            background: #dc3545;
            color: white;
        }
        
        .btn-action:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="images/logo.svg" alt="Coach NmNiHealth" height="40">
                <span>Coach NmNiHealth</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.html">首頁</a></li>
                <li><a href="admin.php">管理後台</a></li>
                <li><a href="../logout.php">登出</a></li>
            </ul>
        </div>
    </nav>

    <div class="admin-container">
        <div class="admin-header">
            <h1>教練管理後台</h1>
            <div>
                <span>管理員：<?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>
        
        <div class="admin-nav">
            <a href="admin.php">儀表板</a>
            <a href="bookings.php">預約管理</a>
            <a href="../admin_users.php">會員管理</a>
            <a href="../admin.php">主站管理</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <h2>預約統計</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_bookings; ?></div>
                <div class="stat-label">總預約數</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_bookings; ?></div>
                <div class="stat-label">待確認</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $confirmed_bookings; ?></div>
                <div class="stat-label">已確認</div>
            </div>
        </div>
        
        <h2>最新預約</h2>
        <div class="bookings-table">
            <table>
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>電話</th>
                        <th>服務項目</th>
                        <th>狀態</th>
                        <th>預約時間</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                            <td><?php echo htmlspecialchars($booking['service']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $booking['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => '待確認',
                                        'confirmed' => '已確認',
                                        'cancelled' => '已取消'
                                    ];
                                    echo $status_text[$booking['status']] ?? $booking['status'];
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($booking['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($booking['status'] === 'pending'): ?>
                                        <button class="btn-action btn-confirm" onclick="updateStatus(<?php echo $booking['id']; ?>, 'confirmed')">確認</button>
                                        <button class="btn-action btn-cancel" onclick="updateStatus(<?php echo $booking['id']; ?>, 'cancelled')">取消</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateStatus(bookingId, status) {
            if (confirm('確定要更新預約狀態嗎？')) {
                fetch('update_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + bookingId + '&status=' + status
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('更新失敗：' + data.message);
                    }
                })
                .catch(error => {
                    alert('系統錯誤，請稍後再試');
                });
            }
        }
    </script>
</body>
</html>