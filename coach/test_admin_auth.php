<?php
session_start();

// 显示当前会话信息
echo "当前会话信息：<br>";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '未设置') . "<br>";
echo "User Name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '未设置') . "<br>";
echo "User Email: " . (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '未设置') . "<br>";
echo "User Role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '未设置') . "<br>";

// 连接数据库检查用户角色
$servername = "localhost";
$username = "nmnihealth";
$password = "health2025";
$dbname = "nmnihealth";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<br>数据库中的用户信息：<br>";
            echo "ID: " . $user['id'] . "<br>";
            echo "Name: " . $user['name'] . "<br>";
            echo "Email: " . $user['email'] . "<br>";
            echo "Role: " . $user['role'] . "<br>";
        }
    } else {
        echo "<br>用户未登录<br>";
    }
} catch(PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage();
}

echo "<br><a href='/admin.php'>访问管理后台</a> | <a href='/login.php'>登录页面</a> | <a href='/logout.php'>退出登录</a>";
?>