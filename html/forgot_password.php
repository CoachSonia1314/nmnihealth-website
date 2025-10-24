<?php
session_start();

// 如果用户已登录，重定向到会员中心
if (isset($_SESSION['user_id'])) {
    header("Location: member.php");
    exit();
}

$message = '';
$error = '';

// 处理忘记密码表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    if (!empty($email)) {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password_db = "health2025";
        $dbname = "nmnihealth";
        
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 检查邮箱是否存在
            $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // 生成重置令牌
                $token = bin2hex(random_bytes(50));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
                
                // 保存重置令牌到数据库
                // 首先检查是否已存在该用户的令牌记录
                $stmt = $pdo->prepare("SELECT id FROM password_resets WHERE email = ?");
                $stmt->execute([$email]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($existing) {
                    // 更新现有记录
                    $stmt = $pdo->prepare("UPDATE password_resets SET token = ?, expiry = ? WHERE email = ?");
                    $stmt->execute([$token, $expiry, $email]);
                } else {
                    // 插入新记录
                    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
                    $stmt->execute([$email, $token, $expiry]);
                }
                
                // 发送重置邮件
                $reset_link = "https://www.nmnihealth.com/reset_password.php?token=" . $token;
                $subject = "NmNiHealth 密码重置";
                $body = "
                <html>
                <head>
                    <title>NmNiHealth 密码重置</title>
                </head>
                <body>
                    <h2>密码重置请求</h2>
                    <p>亲爱的 " . htmlspecialchars($user['name']) . "，</p>
                    <p>您请求重置您的密码。请点击下面的链接来设置新密码：</p>
                    <p><a href='" . $reset_link . "'>重置密码</a></p>
                    <p>如果您没有请求重置密码，请忽略此邮件。</p>
                    <p>此链接将在1小时后过期。</p>
                    <br>
                    <p>此致，<br>NmNiHealth 团队</p>
                </body>
                </html>
                ";
                
                // 邮件头部信息
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: noreply@nmnihealth.com" . "\r\n";
                
                // 发送邮件
                if (mail($email, $subject, $body, $headers)) {
                    $message = "密码重置链接已发送到您的邮箱。请检查您的收件箱（包括垃圾邮件文件夹）。";
                } else {
                    $error = "邮件发送失败，请稍后再试";
                }
            } else {
                // 为了安全，即使邮箱不存在也显示成功消息
                $message = "如果该邮箱地址已注册，密码重置链接已发送到您的邮箱。请检查您的收件箱（包括垃圾邮件文件夹）。";
            }
        } catch(PDOException $e) {
            $error = "操作失败，请稍后再试";
        }
    } else {
        $error = "请输入您的邮箱地址";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>忘記密碼 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .forgot-password-container {
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
        <h1>忘記密碼</h1>
        <p>請輸入您的電子信箱以重置密碼</p>
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
        <div class="forgot-password-container">
            <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
                <p style="margin-top: 1rem;"><a href="login.php">返回登入頁面</a></p>
            </div>
            <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">電子信箱 *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <button type="submit" class="btn">發送重置連結</button>
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