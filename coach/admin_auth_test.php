<?php
// 管理员权限验证测试脚本
session_start();

echo "<h1>管理员权限验证测试</h1>";

// 显示当前会话信息
echo "<h2>当前会话信息：</h2>";
echo "<p>User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '未设置') . "</p>";
echo "<p>User Name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '未设置') . "</p>";
echo "<p>User Email: " . (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '未设置') . "</p>";
echo "<p>User Role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '未设置') . "</p>";

// 检查管理员权限
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        echo "<p style='color: green; font-weight: bold;'>✓ 管理员权限验证通过</p>";
        echo "<p><a href='admin.php'>访问管理后台</a></p>";
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠ 普通用户权限</p>";
        echo "<p><a href='member.php'>访问会员中心</a></p>";
    }
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ 未登录</p>";
    echo "<p><a href='login.php'>用户登录</a> | <a href='admin_login_test.php'>管理员登录测试</a></p>";
}

// 显示数据库中的用户信息（如果已登录）
if (isset($_SESSION['user_id'])) {
    $servername = "localhost";
    $username = "nmnihealth";
    $password = "health2025";
    $dbname = "nmnihealth";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<h2>数据库中的用户信息：</h2>";
            echo "<p>ID: " . $user['id'] . "</p>";
            echo "<p>Name: " . $user['name'] . "</p>";
            echo "<p>Email: " . $user['email'] . "</p>";
            echo "<p>Role: " . $user['role'] . "</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>数据库连接失败: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='index.html'>返回首页</a> | <a href='logout.php'>退出登录</a></p>";
?>