<?php
// 管理员登录测试脚本
session_start();

$error = '';

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
            
            // 查询用户
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
    <title>管理员登录测试 - NmNiHealth</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .btn {
            background: #8A2BE2;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        
        .btn:hover {
            background: #7A1BD2;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>管理员登录测试</h1>
        
        <div class="info">
            <p><strong>管理员账户信息：</strong></p>
            <p>邮箱：admin@nmnihealth.com</p>
            <p>密码：test123</p>
        </div>
        
        <?php if ($error): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">邮箱地址 *</label>
                <input type="email" id="email" name="email" value="admin@nmnihealth.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">密码 *</label>
                <input type="password" id="password" name="password" value="test123" required>
            </div>
            
            <button type="submit" class="btn">登录</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="index.html">返回首页</a> | 
            <a href="login.php">普通用户登录</a>
        </div>
    </div>
</body>
</html>