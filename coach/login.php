<?php
session_start();

// 如果用户已登录，重定向到会员中心
if (isset($_SESSION['user_id'])) {
    header("Location: member.php");
    exit();
}

$error = '';

// 处理登录表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        // 连接数据库
        $servername = "localhost";
        $username = "nmnihealth";
        $password_db = "health2025";
        $dbname = "nmnihealth";
        
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 查询用户（包含角色信息）
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // 登录成功
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // 根据角色重定向
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: member.php");
                }
                exit();
            } else {
                $error = "邮箱或密码错误";
            }
        } catch(PDOException $e) {
            $error = "登录失败，请稍后再试";
        }
    } else {
        $error = "请填写所有必填字段";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>會員登入 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .login-container {
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
            color: var(--primary-color);
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: var(--font-primary);
            font-size: 1rem;
        
        .btn {
            background: var(--primary-color);
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
        
        .login-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>會員登入</h1>
        <p>登入您的帳號以享受個人化健康管理服務</p>
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
        <div class="login-container">
            <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">電子信箱 *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">密碼 *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">登入</button>
            </form>
            
            <div class="social-login">
                <p style="text-align: center; margin: 1.5rem 0; color: #6c757d;">或使用以下帳號登入</p>
                <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                    <a href="google_login.php" style="background: #dd4b39; color: white; padding: 0.75rem 1.5rem; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                            <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/>
                        </svg>
                        Google
                    </a>
                    <a href="facebook_login.php" style="background: #3b5998; color: white; padding: 0.75rem 1.5rem; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                        </svg>
                        Facebook
                    </a>
                    <a href="line_login.php" style="background: #00c300; color: white; padding: 0.75rem 1.5rem; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.5rem;">
                            <path d="M0 0v16h16V0H0zm13.56 10.845c-.188.338-.729.539-1.167.539-.608 0-1.09-.235-1.09-.757 0-.394.244-.64.82-1.002l.894-.568c1.305-.8 2.2-1.98 2.2-3.55 0-2.3-1.75-3.78-4.4-3.78-2.8 0-4.3 1.82-4.3 3.66 0 .4.08.94.21 1.35L2.44 9.16c-.18.33-.72.53-1.16.53-.6 0-1.08-.23-1.08-.75 0-.4.24-.64.82-1.01L2.8 6.8C1.5 8 .6 9.18.6 10.75.6 13.05 2.35 14.53 5 14.53c2.8 0 4.3-1.82 4.3-3.66 0-.4-.08-.94-.21-1.35l2.97-1.875z"/>
                        </svg>
                        LINE
                    </a>
                </div>
            </div>
            
            <div class="login-links">
                <p>還沒有帳號？<a href="register.php">立即註冊</a></p>
                <p><a href="#">忘記密碼？</a></p>
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