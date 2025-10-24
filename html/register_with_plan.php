<?php
session_start();

// 如果用户已登录，重定向到会员中心
if (isset($_SESSION['user_id'])) {
    header("Location: member.php");
    exit();
}

$error = '';
$success = '';

// 处理注册表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $membership_plan = $_POST['membership_plan'];
    
    // 验证输入
    if (!empty($name) && !empty($email) && !empty($phone) && !empty($age) && !empty($password) && !empty($confirm_password) && !empty($membership_plan)) {
        if ($password !== $confirm_password) {
            $error = "两次输入的密码不一致";
        } elseif (strlen($password) < 6) {
            $error = "密码长度至少为6位";
        } elseif (!in_array($membership_plan, ['free', 'premium', 'vip'])) {
            $error = "请选择有效的会员计划";
        } else {
            // 连接数据库
            $servername = "localhost";
            $username = "nmnihealth";
            $password_db = "health2025";
            $dbname = "nmnihealth";
            
            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password_db);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // 检查邮箱是否已存在
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "该邮箱已被注册";
                } else {
                    // 插入新用户，包含会员等级
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, age, membership_level) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $hashed_password, $phone, $age, $membership_plan]);
                    
                    $success = "注册成功！您可以立即登录";
                }
            } catch(PDOException $e) {
                $error = "注册失败，请稍后再试";
            }
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
    <title>會員註冊 - NmNiHealth</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/fonts.css">
    <style>
        .register-container {
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
            color: var(--primary-color);
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: var(--font-primary);
            font-size: 1rem;
        }
        
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
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .register-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .register-links a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .register-links a:hover {
            text-decoration: underline;
        }
        
        /* 会员计划选择样式 */
        .plan-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .plan-card {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .plan-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(107, 33, 168, 0.1);
        }
        
        .plan-card h3 {
            color: var(--primary-color);
            margin-top: 0;
        }
        
        .plan-card .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--secondary-color);
            margin: 0.5rem 0;
        }
        
        .plan-card .features {
            text-align: left;
            margin: 1rem 0;
        }
        
        .plan-card .features li {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .plan-input {
            display: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>會員註冊</h1>
        <p>創建帳號以享受個人化健康管理服務</p>
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
        <div class="register-container">
            <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
                <p style="margin-top: 1rem;"><a href="login.php">立即登录</a></p>
            </div>
            <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="name">姓名 *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">電子信箱 *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">聯絡電話 *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="age">年齡 *</label>
                    <input type="number" id="age" name="age" min="30" max="80" required>
                </div>
                
                <div class="form-group">
                    <label for="password">密碼 *</label>
                    <input type="password" id="password" name="password" required>
                    <small>密码长度至少为6位</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">確認密碼 *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label>選擇會員計劃 *</label>
                    <div class="plan-selection">
                        <div class="plan-card" onclick="selectPlan('free')">
                            <h3>免費會員</h3>
                            <div class="price">NT$ 0/月</div>
                            <div class="features">
                                <ul>
                                    <li>基本健康資訊</li>
                                    <li>文章閱讀權限</li>
                                    <li>基礎健康評估</li>
                                </ul>
                            </div>
                            <input type="radio" name="membership_plan" value="free" class="plan-input" id="plan-free">
                        </div>
                        
                        <div class="plan-card" onclick="selectPlan('premium')">
                            <h3>黃金會員</h3>
                            <div class="price">NT$ 299/月</div>
                            <div class="features">
                                <ul>
                                    <li>免費會員所有功能</li>
                                    <li>個人化健康方案</li>
                                    <li>專家諮詢9折</li>
                                    <li>專屬電子報</li>
                                </ul>
                            </div>
                            <input type="radio" name="membership_plan" value="premium" class="plan-input" id="plan-premium">
                        </div>
                        
                        <div class="plan-card" onclick="selectPlan('vip')">
                            <h3>鑽石會員</h3>
                            <div class="price">NT$ 599/月</div>
                            <div class="features">
                                <ul>
                                    <li>黃金會員所有功能</li>
                                    <li>無限次專家諮詢</li>
                                    <li>優先預約權</li>
                                    <li>專屬健康顧問</li>
                                    <li>線下活動邀請</li>
                                </ul>
                            </div>
                            <input type="radio" name="membership_plan" value="vip" class="plan-input" id="plan-vip">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn">註冊</button>
            </form>
            <?php endif; ?>
            
            <div class="register-links">
                <p>已有帳號？<a href="login.php">立即登入</a></p>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>© 2025 NmNiHealth 更年期健康保健養生專家. All rights reserved.</p>
            <p>本網站資訊僅供參考，不能替代專業醫療建議。如有嚴重症狀，請諮詢醫生。</p>
        </div>
    </footer>
    
    <script>
        function selectPlan(plan) {
            // 移除所有选中状态
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // 添加选中状态到当前卡片
            event.currentTarget.classList.add('selected');
            
            // 选中对应的单选按钮
            document.getElementById('plan-' + plan).checked = true;
        }
    </script>
</body>
</html>