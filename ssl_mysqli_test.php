<?php
// SSL和MySQLi測試頁面

// 檢查SSL
$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
$protocol = $https ? 'HTTPS' : 'HTTP';

// 檢查MySQLi
if (function_exists('mysqli_connect')) {
    $mysqli_status = '✅ MySQLi已安裝並啟用';
    
    // 測試數據庫連接
    try {
        $conn = new mysqli("localhost", "nmnihealth", "health2025", "nmnihealth");
        if ($conn->connect_error) {
            $db_status = '❌ 數據庫連接失敗: ' . $conn->connect_error;
        } else {
            $db_status = '✅ 數據庫連接成功';
            
            // 測試查詢
            $result = $conn->query("SELECT COUNT(*) as user_count FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                $user_count = $row['user_count'];
                $db_status .= " (用戶數: $user_count)";
            }
            $conn->close();
        }
    } catch (Exception $e) {
        $db_status = '❌ 數據庫錯誤: ' . $e->getMessage();
    }
} else {
    $mysqli_status = '❌ MySQLi未安裝';
    $db_status = 'N/A';
}

// PHP版本信息
$php_version = phpversion();
$apache_version = apache_get_version();

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSL和MySQLi測試 - NmNiHealth</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .status-item {
            margin: 15px 0;
            padding: 15px;
            border-radius: 5px;
            background: #f8f9fa;
        }
        .status-ok {
            border-left: 4px solid #28a745;
        }
        .status-error {
            border-left: 4px solid #dc3545;
        }
        .status-info {
            border-left: 4px solid #17a2b8;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-success {
            background: #28a745;
        }
        .badge-danger {
            background: #dc3545;
        }
        .badge-info {
            background: #17a2b8;
        }
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .back-link a:hover {
            background: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 SSL和MySQLi配置測試</h1>
        
        <div class="status-item status-info">
            <h3>連接協議</h3>
            <p><span class="badge badge-info"><?php echo $protocol; ?></span></p>
            <p>當前使用 <?php echo $protocol; ?> 協議訪問此頁面</p>
        </div>
        
        <div class="status-item <?php echo $https ? 'status-ok' : 'status-error'; ?>">
            <h3>SSL證書狀態</h3>
            <p><span class="badge <?php echo $https ? 'badge-success' : 'badge-danger'; ?>">
                <?php echo $https ? '已啟用' : '未啟用'; ?>
            </span></p>
            <p><?php echo $https ? '✅ SSL連接安全' : '❌ 建議使用HTTPS'; ?></p>
        </div>
        
        <div class="status-item <?php echo function_exists('mysqli_connect') ? 'status-ok' : 'status-error'; ?>">
            <h3>MySQLi擴展</h3>
            <p><?php echo $mysqli_status; ?></p>
        </div>
        
        <div class="status-item <?php echo strpos($db_status, '✅') !== false ? 'status-ok' : 'status-error'; ?>">
            <h3>數據庫連接</h3>
            <p><?php echo $db_status; ?></p>
        </div>
        
        <div class="status-item status-info">
            <h3>系統信息</h3>
            <p><strong>PHP版本:</strong> <?php echo $php_version; ?></p>
            <p><strong>Apache版本:</strong> <?php echo $apache_version; ?></p>
            <p><strong>服務器時間:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <div class="back-link">
            <a href="index.html">返回首頁</a>
        </div>
    </div>
</body>
</html>