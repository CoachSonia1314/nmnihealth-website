<?php
session_start();

// 如果用户已登录，重定向到会员中心
if (isset($_SESSION['user_id'])) {
    header("Location: member.php");
    exit();
}

$message = '';
$error = '';

// 获取令牌参数
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = "无效的重置链接";
} else {
    // 连接数据库
    $servername = "localhost";
    $username = "nmnihealth";
    $password_db = "health2025";
    $dbname = "nmnihealth";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password_db);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 验证令牌
        $stmt = $pdo->prepare("SELECT email, expiry FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reset_request) {
            $error = "无效的重置链接";
        } elseif (new DateTime() > new DateTime($reset_request['expiry'])) {
            $error = "重置链接已过期，请重新申请";
        } else {
            // 保存邮箱到会话，用于后续密码更新
            $_SESSION['reset_email'] = $reset_request['email'];
        }
    } catch(PDOException $e) {
        $error = "系统错误，请稍后再试";
    }
}

// 处理密码重置表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = "请填写所有必填字段";
    } elseif ($password !== $confirm_password) {
        $error = "两次输入的密码不一致";
    } elseif (strlen($password) < 6) {
        $error = "密码长度至少为6位";
    } else {
        try {
            // 更新密码
            $email = $_SESSION['reset_email'] ?? '';
            
            if (!empty($email)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->execute([$hashed_password, $email]);
                
                // 删除已使用的重置令牌
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                
                // 清除会话中的重置邮箱
                unset($_SESSION['reset_email']);
                
                $message = "密码重置成功！您可以立即登录";
            } else {
                $error = "重置链接已过期，请重新申请";
            }
        } catch(PDOException $e) {
            $error = "密码重置失败，请稍后再试";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>重置密碼 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .reset-password-container {
            max-width: 500px;
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
        
        input {
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
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #7A1BD2;
        }
        
        .btn-secondary {
            background: #6c757d;
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 1rem;
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
        
        .login-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-links a {
            color: #8A2BE2;
            text-decoration: none;
        }
        
        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>重置密碼</h1>
        <p>請輸入您的新密碼</p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.html">首頁</a></li>
            <li><a href="index.html#knowledge">幸福熟齡(文章區)</a></li>
            <li><a href="appointment.php">專家諮詢</a></li>
            <li><a href="login.php">會員登入</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <div class="reset-password-container">
            <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
                <p style="margin-top: 1rem;"><a href="login.php">立即登录</a></p>
            </div>
            <?php elseif (empty($error)): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">新密碼 *</label>
                    <input type="password" id="password" name="password" required>
                    <small>密码长度至少为6位</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">確認新密碼 *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">重置密碼</button>
            </form>
            <?php endif; ?>
            
            <div class="login-links">
                <p><a href="login.php">返回登入頁面</a></p>
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